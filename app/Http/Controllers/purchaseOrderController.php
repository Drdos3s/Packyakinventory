<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\ItemCategory;
use App\Item;

use DB;
use Auth;
use App;


//Put in for auth check before finishing and pushing to production

class purchaseOrderController extends Controller
{
    public function ajaxRoute(){
        if(Request::ajax()) {
            //direct the ajax request to where it needs to go
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'createPO':
                        return createOrEditNewPurchaseOrder($_POST['action'] ,$_POST['po_name'], $_POST['po_status'], $_POST['po_vendor'], $_POST['po_invoice_number'], $_POST['po_location'], $_POST['po_shipping_cost'] );
                        break;
                    case 'updatePO':
                        return createOrEditNewPurchaseOrder($_POST['action'] ,$_POST['po_name'], $_POST['po_status'], $_POST['po_vendor'], $_POST['po_invoice_number'], $_POST['po_location'], $_POST['po_shipping_cost'] );
                        break;
                    case 'getPendingPO':
                        return getPendingPurchaseOrder();
                        break;
                    case 'addToPO': //<-Added Location ID
                        return addItemToPurchaseOrder($_POST['selectedPurchaseOrder'], $_POST['packyakPurchaseOrderID'], $_POST['itemVariationID'], $_POST['itemLocationID'], $_POST['itemUnitCost']);
                        break;
                    case 'completePO': //<-Added Location ID
                        return completePO_UpdateSquare($_POST['po_id_number']);
                        break;
                    case 'removeItemFromPO': //<-Added Location ID
                        return removeItemFromPO($_POST['itemVariationID'], $_POST['itemLocationID'], $_POST['packyakPurchaseOrderID']);
                        break;
                    case 'deletePO':
                        return deletePurchaseOrder($_POST['packyakPurchaseOrderID']);
                        break;
                    case 'createNewItem':
                        //retrieve new item object and decode into PHP readable 
                        $data = $_POST['data'];
                        $decodedItemData = json_decode($data);
                        return createNewItem($decodedItemData);
                        break;
                    case 'updateNewItemInventory':
                        //retrieve new item object and decode into PHP readable 
                        $decodedInventoryData = json_decode($_POST['data']);
                        return updateNewItemInventory($decodedInventoryData);
                        break;
                    case 'updateQuantityToOrder':
                        $integerUnitCost = $_POST['unitCost']*100;

                        updateItemUnitPrice($_POST['poItemID'], $_POST['itemLocationID'], $integerUnitCost);//<-Added Location ID

                        return updateQuantityToOrder($_POST['poItemID'], $_POST['purchaseOrderID'], $_POST['itemLocationID'], $_POST['quantityToOrder']);//<-Added Location ID
                        break;
                    case 'updatePOItemsTable':
                        //get null modals
                        $poItems = App\PurchaseOrderItem::orderBy('id', 'desc')->where('itemLocationID', '=', NULL)->take(300)->get();

                        //loop through models
                        foreach($poItems as $poItem){
                            //get item var id
                            $varID = $poItem -> purchaseOrderItemVariationID;

                            //get the location ID from the PO
                            $location = App\PurchaseOrder::where('id', '=', $poItem -> purchaseOrderID)->first();
                            $locationID = DB::table('locations')->where('locationCity', '=', $location -> po_location)->value('squareID');

                            $poItem -> itemLocationID = $locationID;
                            $poItem -> save();

                        }
                        return $poItems;
                        break;
                }
            }else{
                echo 'The action variable is not set in the ajax request';
            }
        }
    }

    public function index(){
        if (Auth::check()){//The user is logged in
            return $this->populatePurchaseOrderPage(); 
        }else{
            return redirect('/auth/login');
        }
    }

    public function itemSearch(){
        
        if(isset($_GET['term']))
        {
            $term = $_GET['term'];

            $searchedItems = App\Item::where('itemName', $term)->get();
        }


        $test = ['testKey' => 'paintballvendor1',
                    'testKey2' => 'paintballvendor2',
                    'totally' => 'different'];
        return json_encode($test);
    }

    public function populatePurchaseOrderPage() {
        //creating the array for item categories to populate the DB
        $itemCategoriesAllLocations = [];
        //purchase order array
        $purchaseOrderDetailsWithItems = [];

        $existingPurchaseOrders = json_decode(
                                    json_encode(
                                        DB::table('purchase_orders')
                                            //->leftJoin('locations', 'purchase_orders.po_location', '=', 'locations.locationCity')
                                            ->whereNotIn('po_status', ['Deleted', 'Closed'])
                                            ->orderBy('created_at', 'desc')
                                            ->get()
                                    ),true);
        //getting all locations to dynamically populate locations lists used for create items and create purchase orders
         $existingLocations = json_decode( 
                                json_encode(
                                    DB::table('locations')->get()
                                ),true);

        //setting up batch body for the categories request -- Could probably get away with putting into a new function with logic
        foreach ($existingLocations as $singleLocation) {
            $formattedCategoryRequest = array('method' => 'GET',
                                              'relative_path' => '/v1/'.$singleLocation['squareID'].'/categories',
                                              'access_token' => 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ',
                                              'request_id' => $singleLocation['squareID']
                                              );
            array_push($itemCategoriesAllLocations, $formattedCategoryRequest);
        }
        $categoryRequestBatch = array('requests' => $itemCategoriesAllLocations); 

        //initialize new client
        $client = new Client();

        //retrieve all categories
        //UNCOMMENT SO THAT CATEGORIES UPDATES
        $categoriesBatch = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ], 'body' => json_encode($categoryRequestBatch)
        ]);

        $categoriesList = json_decode($categoriesBatch->getBody(), true);

        //write each category to the database
        foreach($categoriesList as $categoryList){
            foreach($categoryList['body'] as $categoryNameAndID){        
                $newCategoryInDB = ItemCategory::firstOrNew(['locationID' => $categoryList['request_id'], 'categoryName' => $categoryNameAndID['name']]);
                $newCategoryInDB -> locationID = $categoryList['request_id'];
                $newCategoryInDB -> categoryName = $categoryNameAndID['name'];
                $newCategoryInDB -> categoryID = $categoryNameAndID['id'];
                $newCategoryInDB -> save();
            }  
        }

        //get all the items within a purchase order
        foreach($existingPurchaseOrders as $existingPurchaseOrder){
            $existingPurchaseOrder['po_items'] = json_decode(json_encode(DB::table('purchase_order_items')
                                                ->leftJoin('inventoryList', function($join){
                                                    $join->on('purchase_order_items.purchaseOrderItemVariationID', '=', 'inventoryList.itemVariationID');
                                                    $join->on('purchase_order_items.itemLocationID', '=', 'inventoryList.itemLocationID');
                                                })
                                                ->where('purchaseOrderID', '=', $existingPurchaseOrder['id'])
                                                ->get()),true);

            array_push($purchaseOrderDetailsWithItems, $existingPurchaseOrder);
        }      

        //sending the names of the unique categoires to the view for the select button
        $itemCategoryList = json_decode(json_encode(DB::table('item_categories')
                                ->select('categoryName')
                                ->distinct()
                                ->get()), true);

        return view('purchaseOrder', ['existingPurchaseOrders' => $purchaseOrderDetailsWithItems, 'existingLocations' => $existingLocations, 'categoryList' => $itemCategoryList]);
    }
}

function createOrEditNewPurchaseOrder($action, $po_name, $po_status, $po_vendor, $po_invoice_number, $po_location, $po_shipping_cost){
    //retriving variables from modal form
    switch ($action) {
        case 'createPO':
            $purchaseOrder = new PurchaseOrder;
            $purchaseOrder->po_name = $po_name;
            if(!empty($po_status)){
                $purchaseOrder->po_status = $po_status;
            };
            $purchaseOrder->po_vendor = $po_vendor;
            $purchaseOrder->po_invoice_number = $po_invoice_number;
            $purchaseOrder->po_location = $po_location;
            $purchaseOrder->po_shipping_cost = $po_shipping_cost;
            $purchaseOrder->po_total_cost = $po_shipping_cost;
            $purchaseOrder->save();

            return json_encode(['action' => $action, 'purchaseOrder' => $purchaseOrder]);
            break;
        case 'updatePO': 
            $po_id_number = $_POST['po_id_number'];



            PurchaseOrder::where('id', $po_id_number)
                                ->update(['po_name' => $po_name,
                                        'po_status' => $po_status,
                                        'po_invoice_number' => $po_invoice_number,
                                        'po_location' => $po_location,
                                        'po_shipping_cost' => $po_shipping_cost]);

            updatePurchaseOrderPrices($po_id_number);

            $updatedPurchaseOrder = DB::table('purchase_orders')->where('id', $po_id_number)->first();
            return json_encode(['action' => $action, 'purchaseOrder' => $updatedPurchaseOrder]);
            break;
    }
};

function getPendingPurchaseOrder(){
    $allPendingPO = DB::table('purchase_orders')->where('po_status', '=', 'pending')->orderBy('created_at', 'desc')->get();
    return json_encode($allPendingPO);
};

function addItemToPurchaseOrder($poName, $poOrderID, $poVarID, $poLocationID, $varUnitCost){
    //retrieve data from POST
    $varUnitCost = substr($varUnitCost, 1);
    //set up new instance for purchase order item
    $purchaseOrderItem = new PurchaseOrderItem;
    $purchaseOrderItem->purchaseOrderName = $poName;
    $purchaseOrderItem->purchaseOrderID = $poOrderID;
    $purchaseOrderItem->purchaseOrderItemVariationID = $poVarID;
    $purchaseOrderItem->itemLocationID = $poLocationID;
    $purchaseOrderItem->itemUnitCost = $varUnitCost*100;

    $purchaseOrderItem->save();

    return 'Adding Item To PO Working';
    exit;
};

function completePO_UpdateSquare($po_id_number){

    //chunk the results from DB in 30 in order to make the batch request work for large PO's
    DB::table('purchase_order_items')->where('purchaseOrderID', $po_id_number)->chunk(30, function($items) {

        $variationInventoryUpdateForSquare = [];

        foreach ($items as $item) {
            $locationID = $item -> itemLocationID;
            $quantityToOrder = $item -> quantityToOrder;
            //set up JSON post data
            $postData = array(
                'quantity_delta' => intval($quantityToOrder),
                'adjustment_type' => 'RECEIVE_STOCK'
            );

            if($quantityToOrder != 0 && $quantityToOrder != ''){
                //running through and creating each request for the batch in order to create items
                $formattedUpdateInventorySingleRequest = array('method' => 'POST',
                                      'relative_path' => '/'.'v1/'.$locationID.'/inventory/'.$item -> purchaseOrderItemVariationID,
                                      'access_token' => 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ',
                                      'body' => $postData
                                      );

                array_push($variationInventoryUpdateForSquare , $formattedUpdateInventorySingleRequest);
            };
        };

        $inventoryUpdateRequestFullBatch = array('requests' => $variationInventoryUpdateForSquare);

        $client = new Client();
        //send the batch request with JSON for each item variation that should be updated
        $inventoryUpdateBatchResponse = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ], 'body' => json_encode($inventoryUpdateRequestFullBatch)
        ]);

        $decodedInventoryUpdateBatchResponse = json_decode($inventoryUpdateBatchResponse->getBody(), true);
    });

    //update purchase order status
    purchaseOrder::where('id', $po_id_number)
                                ->update(['po_status' => 'Completed']);

    return json_encode(['action' => 'completePO', 'purchaseOrderID' => $po_id_number]);
};

function removeItemFromPO($itemVariationID, $itemLocationID, $purchaseOrderID){

    DB::table('purchase_order_items')
        ->where('purchaseOrderID', '=', $purchaseOrderID)
        ->where('purchaseOrderItemVariationID','=', $itemVariationID)
        ->where('itemLocationID','=', $itemLocationID)
        ->delete();

    updatePurchaseOrderPrices($purchaseOrderID);

    $updatedPOInfo = json_encode(DB::table('purchase_orders')->where('id', $purchaseOrderID)->first());

    return $updatedPOInfo;
    exit;
};

function deletePurchaseOrder($POID){
    PurchaseOrder::where('id', $POID)
                            ->update(['po_status' => 'Deleted']);
    return $POID; 
};
    

function createNewItem($createdItem){
    //setting variables from the post data from ajax call
    $newItemCategory            = $createdItem->newItemCategory;
    $newItemName                = $createdItem->newItemName;
    $newItemVariationsRaw       = $createdItem->newItemVariations;
    $newItemLocationSoldAt      = $createdItem->newItemLocationSoldAt;


    
    $newItemVariationsDecoded = json_decode(json_encode($newItemVariationsRaw), true);
    $variationsListForSquare = [];
    $newItemArray = [];
    $access_token = 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ';
    
    //adds each variation to an array that will be added to other data in order to create item
    foreach($newItemVariationsDecoded as $newItemVariation){
        $formattedItemVariation = array(
                'name' => $newItemVariation['newVariationName'],
                'price_money' => array(
                  'currency_code' => 'USD',
                  'amount' => $newItemVariation['newVariationPrice']
                ),
                'track_inventory'=> true,
                'sku' => $newItemVariation['newVariationSKU'],
                'inventory_alert_type'=> "LOW_QUANTITY"
            );

        //pushes new variation with properties into an array to be formatted into JSON right before request happens
        array_push($variationsListForSquare, $formattedItemVariation);
    }

    foreach($newItemLocationSoldAt as $newSquareItemLocation){
        //get the location info that is needed
        $existingLocation = json_decode( 
                                json_encode(
                                    DB::table('locations')
                                    ->where('locationCity', '=', $newSquareItemLocation)
                                    ->get()
                                ),true);

        //get the individual category id to be able to place the item
        $categoryIDForSpecificLocation = json_decode(json_encode(
                                                        DB::table('item_categories')
                                                        ->select('categoryID')
                                                        ->where(['categoryName' => $newItemCategory, 'locationID' => $existingLocation[0]['squareID']])
                                                        ->get()
                                                        ), true);

        //set up general data for each item to once again be converted to json
        $postData = array(
            'name' => $newItemName,
            'category_id' => $categoryIDForSpecificLocation[0]['categoryID'],
            'variations' => $variationsListForSquare
        );

        //running through and creating each request for the batch in order to create items
        $formattedCreateItemSingleRequest = array('method' => 'POST',
                                          'relative_path' => '/'.'v1/'.$existingLocation[0]['squareID'].'/items',
                                          'access_token' => 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ',
                                          'body' => $postData,
                                          'request_id' => $existingLocation[0]['squareID']
                                          );

        array_push($newItemArray, $formattedCreateItemSingleRequest);
    };

    //Putting in batch request and getting ready to be sent
    $newItemRequestFullBatch = array('requests' => $newItemArray);

    $client = new Client();
    //send the batch request with JSON for each item created per location with variations
    $newItemBatchResponse = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ], 'body' => json_encode($newItemRequestFullBatch)
    ]);

    $decodedNewItemBatchResponse = json_decode($newItemBatchResponse->getBody(), true);

    //create new item in DB and also write the unit cost and current inventory level.
    foreach($decodedNewItemBatchResponse as $decodedItem){
        
        //echo 'NEW DECODED ITEM ---------------------------------';
        //var_dump($decodedItem);
        foreach($decodedItem['body']['variations'] as $decodedItemVariation){

            
            //echo 'NEW DECODED ITEM VARIATION ******************************************';
            //var_dump($decodedItemVariation);
            
            $locationCityName = json_decode(json_encode(DB::table('locations')
                                    ->select('locationCity')
                                    ->where('squareID', '=', $decodedItem['request_id'])
                                    ->get()),true);

            

            $createdItemToDB = Item::firstOrNew(['itemVariationID' => $decodedItemVariation['id']]);
            $createdItemToDB -> squareItemID = $decodedItemVariation['item_id'];
            $createdItemToDB -> itemName = $decodedItem['body']['name'];
            $createdItemToDB -> itemCategoryName = $decodedItem['body']['category']['name'];
            $createdItemToDB -> itemCategoryID = $decodedItem['body']['category']['id'];
            $createdItemToDB -> itemVariationName = $decodedItemVariation['name'];
            $createdItemToDB -> itemVariationID = $decodedItemVariation['id'];
            $createdItemToDB -> itemVariationPrice = $decodedItemVariation['price_money']['amount'];
            $createdItemToDB -> itemVariationSKU = $decodedItemVariation['sku'];
            $createdItemToDB -> locationSoldAt = $locationCityName[0]['locationCity'];

            $createdItemToDB -> save();
        }
    }

    $sendingItemToView = json_decode(json_encode(DB::table('inventoryList')->where('itemName', '=', $newItemName)->get()),true);

    //Time to set up and add to DB new item and update inventory as well as unit price to finish out the request. 
    return $sendingItemToView;
    /*var = (query your database for the match_id and pull the info)
    if var is empty/none/len==0/etc (so match isnt already in your database):
    ---> call API, pull data, insert into database
    else if var contains data and is not empty (so the match already in it)
    ---> use that data, save yourself the API call*/
    exit;
};

function updateNewItemInventory($inventoryData) {

    $inventoryData = json_decode(json_encode($inventoryData), true);

    $variationInventoryUpdateForSquare = [];

    //echo 'Data coming into method';
    //var_dump($inventoryData);

    foreach($inventoryData['inventoryInfo'] as $inventoryUpdate){
        echo 'New Inventory Update';
        //var_dump($inventoryUpdate);

        //update DB record 

            $inProgressVariationPrice = DB::table('inventoryList')->where('itemVariationID', $inventoryUpdate['newVariationID'])->value('itemVariationPrice');

            //setting profit margin variable for DB
            $variationProfitMargin = intval($inProgressVariationPrice) - intval($inventoryUpdate['newVariationUnitPrice']);
            echo 'this is the profit margin: '.$variationProfitMargin;

            Item::where('itemVariationID', $inventoryUpdate['newVariationID'])
                                ->update(['itemVariationUnitCost' => intval($inventoryUpdate['newVariationUnitPrice']),
                                        'itemVariationInventory' => $inventoryUpdate['newVariationInventoryLevel'],
                                        'itemVariationProfitMargin' => $variationProfitMargin]);

            echo 'We got to the bottom of the query';
            echo 'Check for this ID in DB: '.$inventoryUpdate['newVariationID'];


        $variationLocationID = json_decode(json_encode(DB::table('locations')
                                    ->select('squareID')
                                    ->where('locationCity', '=', $inventoryUpdate['inventoryLocationSoldAt'])
                                    ->get()), true);
        

        $quantityDelta = $inventoryUpdate['newVariationInventoryLevel'];
        //set up general data for each item to once again be converted to json
        $postData = array(
            'quantity_delta' => intval($quantityDelta),
            'adjustment_type' => 'RECEIVE_STOCK'
        );

        //var_dump($postData);
        if($quantityDelta != 0 && $quantityDelta != ''){
            //running through and creating each request for the batch in order to create items
            $formattedUpdateInventorySingleRequest = array('method' => 'POST',
                                  'relative_path' => '/'.'v1/'.$variationLocationID[0]['squareID'].'/inventory/'.$inventoryUpdate['newVariationID'],
                                  'access_token' => 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ',
                                  'body' => $postData,
                                  'request_id' => $inventoryUpdate['newVariationUnitPrice']
                                  );

            array_push($variationInventoryUpdateForSquare , $formattedUpdateInventorySingleRequest);
        };  
    };

    $inventoryUpdateRequestFullBatch = array('requests' => $variationInventoryUpdateForSquare);

    $client = new Client();
    //send the batch request with JSON for each item created per location with variations
    $inventoryUpdateBatchResponse = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ], 'body' => json_encode($inventoryUpdateRequestFullBatch)
    ]);

    $decodedInventoryUpdateBatchResponse = json_decode($inventoryUpdateBatchResponse->getBody(), true);

    //var_dump($decodedInventoryUpdateBatchResponse);

    return 'Update inventory is now working';
};

function updateQuantityToOrder($itemVariationID, $purchaseOrderID, $itemLocationID, $quantityToOrder) {

    $itemCost = DB::table('purchase_order_items')->where('purchaseOrderItemVariationID', $itemVariationID)->value('itemUnitCost');


    //update quantity of the line item in database
    purchaseOrderItem::where('purchaseOrderID', $purchaseOrderID)
                        ->where('purchaseOrderItemVariationID', $itemVariationID)
                        ->where('itemLocationID', $itemLocationID)
                                ->update(['quantityToOrder' => $quantityToOrder,
                                            'lineItemTotal' => $quantityToOrder*$itemCost]);
    //update the PO details
    updatePurchaseOrderPrices($purchaseOrderID);

    $itemAndPOReturnInfo = ['item' => DB::table('purchase_order_items')->where('purchaseOrderItemVariationID', $itemVariationID)->first(), 'purchaseOrder' => DB::table('purchase_orders')->where('id', $purchaseOrderID)->first()];

    return json_encode($itemAndPOReturnInfo);
};

function updateItemUnitPrice($itemVariationID, $itemLocationID, $unitCost) {
    //update unit cost within the item list
    item::where('itemVariationID', $itemVariationID)
        ->where('itemLocationID', $itemLocationID)
        ->update(['itemVariationUnitCost' => $unitCost]);

    purchaseOrderItem::where('purchaseOrderItemVariationID', $itemVariationID)
                        ->where('itemLocationID', $itemLocationID)
                                ->update(['itemUnitCost' => $unitCost]);

}

function updatePurchaseOrderPrices($purchaseOrderID){
    $purchaseOrderSubtotal = DB::table('purchase_order_items')->where('purchaseOrderID', $purchaseOrderID)->sum('lineItemTotal');

    $purchaseOrderShippingCost = DB::table('purchase_orders')->where('id', $purchaseOrderID)->value('po_shipping_cost');

    $purchaseOrderTaxRate = 0.00; //<-THis should be removed and not hardcoded in the future
    $purchaseOrderTotal = 0;
    $purchaseOrderTotal = $purchaseOrderSubtotal + $purchaseOrderShippingCost + ($purchaseOrderTaxRate*$purchaseOrderSubtotal);
    //update purchase order with new totals and numbers
    purchaseOrder::where('id', $purchaseOrderID)
                    ->update(['po_subtotal' => $purchaseOrderSubtotal,
                            'po_total_cost' => $purchaseOrderTotal]);
};
