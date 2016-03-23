<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use App\purchaseOrder;
use App\purchaseOrderItem;
use App\ItemCategory;
//use App\createNewItem
use DB;
use Auth;


//Put in for auth check before finishing and pushing to production

class purchaseOrderController extends Controller
{
    public function index(){
        //getting all open purchase orders and converting object to array
        $existingPurchaseOrders = json_decode(
                                    json_encode(
                                        DB::table('purchase_orders')->get()
                                    ),true);
        //getting all locations to dynamically populate locations lists used for create items and create purchase orders
         $existingLocations = json_decode( 
                                json_encode(
                                    DB::table('locations')->get()
                                ),true);

        $purchaseOrderDetailsWithItems = [];

        //creating the batch call for item categories to populate the DB
        $itemCategoriesAllLocations = [];

        //setting up batch body for the categories request
        foreach ($existingLocations as $singleLocation) {
            $formattedCategoryRequest = array('method' => 'GET',
                                              'relative_path' => '/v1/'.$singleLocation['squareID'].'/categories',
                                              'access_token' => 'KI0ethBHis2N76q1jyYung',
                                              'request_id' => $singleLocation['squareID']
                                              );
            array_push($itemCategoriesAllLocations, $formattedCategoryRequest);
        }

        $categoryRequestBatch = array('requests' => $itemCategoriesAllLocations); 

        //initialize new client
        $client = new Client();

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
                //var_dump($categoryNameAndID);
                //echo $categoryList['request_id'];
                
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
                                                ->leftJoin('inventoryList', 'purchase_order_items.purchaseOrderItemVariationID', '=', 'inventoryList.itemVariationID')
                                                //->select('purchaseOrderItemVariationID')
                                                ->where('purchaseOrderID', '=', $existingPurchaseOrder['id'])
                                                ->get()),true);


            //var_dump($existingPurchaseOrder);
            array_push($purchaseOrderDetailsWithItems, $existingPurchaseOrder);
        }      

        //sending the names of the uniquecategoires to the view for the select button
        $itemCategoryList = json_decode(json_encode(DB::table('item_categories')
                                ->select('categoryName')
                                ->distinct()->get()), true);

        //var_dump($itemCategoryList);

        //var_dump($purchaseOrderDetailsWithItems);
        return view('purchaseOrder', ['existingPurchaseOrders' => $purchaseOrderDetailsWithItems, 'existingLocations' => $existingLocations, 'categoryList' => $itemCategoryList]);
    }

    public function ajaxRoute(){
        if(Request::ajax()) {
            //direct the ajax request to where it needs to go
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'createPO':
                        return createNewPurchaseOrder();
                        break;
                    case 'addToPO':
                        return addItemToPurchaseOrder();
                        break;
                    case 'removeItemFromPO':
                        return removeItemFromPO();
                        break;
                    case 'createNewItem':
                        //retrieve new item object and decode into PHP readable 
                        $data = $_POST['data'];
                        $decodedItemData = json_decode($data);
                        return createNewItem($decodedItemData);
                        break;
                }
            }else{
                echo 'The action variable is not set in the ajax request';
            }
        }
    }
}

function createNewPurchaseOrder(){
    //retriving variables from modal form
    $action = $_POST['action'];
    $po_name = $_POST['po_name'];
    $po_status = $_POST['po_status'];
    $po_vendor = $_POST['po_vendor'];
    $po_location = $_POST['po_location'];
    $po_invoice_number = $_POST['po_invoice_number'];

    /*echo 'Values being passed with ajax';
    echo 'po_name: '.$po_name;
    echo ' po_status: '.$po_status;
    echo ' po_vendor: '.$po_vendor;
    echo ' po_location: '.$po_location;
    echo 'po_invoice set?: '.$po_invoice_number;*/
    //write to database for new purchase order

    $purchaseOrder = new purchaseOrder;
    $purchaseOrder->po_name = $po_name;
    $purchaseOrder->po_status = $po_status;
    $purchaseOrder->po_vendor = $po_vendor;
    $purchaseOrder->po_invoice_number = $po_invoice_number;
    $purchaseOrder->po_location = $po_location;
    $purchaseOrder->save();

    $createdPurchaseOrder = json_encode(array('po_name' => $po_name, 'po_status' => $po_status, 'po_vendor' => $po_vendor, 'po_invoice_number' => $po_invoice_number, 'po_location' => $po_location));
    return $createdPurchaseOrder; 
    exit;
};

function addItemToPurchaseOrder(){
    //retrieve data from POST
    $action = $_POST['action'];
    $selectedPurchaseOrder = $_POST['selectedPurchaseOrder'];
    $itemVariationID = $_POST['itemVariationID'];
    $purchaseOrderID = $_POST['packyakPurchaseOrderID'];

    //set up new instance for purchase order item
    $purchaseOrderItem = new purchaseOrderItem;
    $purchaseOrderItem->purchaseOrderName = $selectedPurchaseOrder;
    $purchaseOrderItem->purchaseOrderID = $purchaseOrderID;
    $purchaseOrderItem->purchaseOrderItemVariationID = $itemVariationID;

    $purchaseOrderItem->save();


    return json_encode(array($action, $selectedPurchaseOrder, $itemVariationID, $purchaseOrderID));
    exit;
};

function removeItemFromPO(){
    $action = $_POST['action'];
    $itemVariationID = $_POST['itemVariationID'];
    $purchaseOrderID = $_POST['packyakPurchaseOrderID'];

    DB::table('purchase_order_items')
        ->where('purchaseOrderID', '=', $purchaseOrderID)
        ->where('purchaseOrderItemVariationID','=', $itemVariationID)
        ->delete();

    exit;
};

function createNewItem($createdItem){

    //var_dump($createdItem);

    //setting variables from the post data from ajax call
    $newItemCategory            = $createdItem->newItemCategory;
    $newItemName                = $createdItem->newItemName;
    $newItemVariationsRaw       = $createdItem->newItemVariations;
    $newItemLocationSoldAt      = $createdItem->newItemLocationSoldAt;

    
    $newItemVariationsDecoded = json_decode(json_encode($newItemVariationsRaw), true);
    $variationsListForSquare = [];
    $access_token = 'KI0ethBHis2N76q1jyYung';
    
    //adds each variation to an array that will be added to other data in order to create item
    foreach($newItemVariationsDecoded as $newItemVariation){
        $formattedVariation = array(
                'name' => $newItemVariation['newVariationName']/*,
                'price_money' => array(
                  'currency_code' => 'USD',
                  'amount' => $newItemVariation['newVariationPrice']
                ),
                'track_inventory'=> true,
                'sku' => $newItemVariation['newVariationSKU'],
                'inventory_alert_type'=> "LOW_QUANTITY"*/
            );

        array_push($variationsListForSquare, $formattedVariation);
    }

    /*$postData = array(
        'name' => $newItemName,
        //'category_id' => '67c8e187-45af-4795-ba56-985f88051453',
        'variations' => $variationsListForSquare
    );*/

    //var_dump($postData);

    

    $newItemArray = [];

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

        //var_dump($categoryIDForSpecificLocation);


        $postData = array(
            'name' => $newItemName,
            'category_id' => $categoryIDForSpecificLocation[0]['categoryID'],
            'variations' => $variationsListForSquare
        );

        //$json = json_encode($postData);
        //echo $json;

        $formattedCreateItemBatchRequest = array('method' => 'POST',
                                          'relative_path' => '/'.'v1/'.$existingLocation[0]['squareID'].'/items',
                                          'access_token' => 'KI0ethBHis2N76q1jyYung',
                                          'body' => $postData
                                          );

        array_push($newItemArray, $formattedCreateItemBatchRequest);
    };





    $newItemRequestBatch = array('requests' => $newItemArray);

    echo json_encode($newItemRequestBatch);
    $client = new Client();


    $newItemBatch = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ], 'body' => json_encode($newItemRequestBatch)
    ]);

        $newItemList = json_decode($newItemBatch->getBody(), true);

        var_dump($newItemList);
        # Creates an item with the input values item.
        /*$itemsRequest = $client->request('POST', 'https://connect.squareup.com/v1/'.$existingLocation[0]['squareID'].'/items', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token ,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ], 'body' => $json
        ]);

        $itemsContents = $itemsRequest->getBody();
        $itemsList = json_decode($itemsContents, true);

        array_push($newItemArray, array('itemID' => $itemsList['id'], 'locationID' => $existingLocation[0]['squareID'], 'locationName' => $newSquareItemLocation));*/
    

    //write get request to make sure I can retrieve all the item corectly
    /*$getNewItemsMade = [];
    $catData = json_encode(array('category_id' => '67c8e187-45af-4795-ba56-985f88051453'));

    $batchBody = array('requests' => array(
                        array('method' => 'PUT',
                              'relative_path' => '/v1/3526BMVFNJZZX/items/62c7c04e-37b6-44cd-9d11-c7d36ea154ef',
                              'access_token' => 'KI0ethBHis2N76q1jyYung',
                              'body' => array('category_id' => 'b60e92d8-97ef-4ebf-a84f-b2b9a695419e')
                              ),
                        array('method' => 'PUT',
                              'relative_path' => '/v1/1H5A5ZGP2T4DA/items/15055252-67a6-4429-ad3a-c7076e56ff39',
                              'access_token' => 'KI0ethBHis2N76q1jyYung',
                              'body' => array('category_id' => '67c8e187-45af-4795-ba56-985f88051453')
                              )
                        ));
    $jsonBatchBody = json_encode($batchBody);

    $itemsBatch = $client->request('POST', 'https://connect.squareup.com/v1/batch', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ], 'body' => $jsonBatchBody
        ]);

        $itemsContents = $itemsBatch->getBody();
        $itemsList = json_decode($itemsContents, true);*/

    return 'working';
    exit;
};
