<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use App\purchaseOrder;
use App\purchaseOrderItem;
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
        //var_dump($purchaseOrderDetailsWithItems);
        return view('purchaseOrder', ['existingPurchaseOrders' => $purchaseOrderDetailsWithItems, 'existingLocations' => $existingLocations]);
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
                echo 'The action variable is not set in the ajax response';
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
    $action = $_POST['action'];
    $selectedPurchaseOrder = $_POST['selectedPurchaseOrder'];
    $itemVariationID = $_POST['itemVariationID'];
    $purchaseOrderID = $_POST['packyakPurchaseOrderID'];

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
    //setting variables from the post data from ajax call
    $newItemCategory            = $createdItem->newItemCategory;
    $newItemName                = $createdItem->newItemName;
    $newItemVariation           = $createdItem->newItemVariation;
    $newItemSku                 = $createdItem->newItemSku;
    $newItemCurrentInventory    = $createdItem->newItemInventoryLevel;
    $newItemAlertInventoryLevel = $createdItem->newItemInventoryAlert;
    $newItemPrice               = $createdItem->newItemPriceSold*100;
    $newItemUnitCost            = $createdItem->newItemUnitCost*100;
    $newItemLocationSoldAt      = $createdItem->newItemLocationSoldAt;


    $access_token = 'KI0ethBHis2N76q1jyYung';
    $client = new Client();

    $postData = array(
      'name' => $newItemName,
      'variations' => array(array(//need to make this run through all the variations that are created
        'name' => 'Regular',
        'price_money' => array(
          'currency_code' => 'USD',
          'amount' => $newItemPrice,
        ),
      )),
    );

    $json = json_encode($postData);

    var_dump($postData);


    # Creates a "Milkshake" item.
    /*$itemsRequest = $client->request('POST', 'https://connect.squareup.com/v1/9SQD525GSB3T3/items', [
        'headers' => [
            'Authorization' => 'Bearer '.$access_token ,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ], 'body' => $json
    ]);*/

    //var_dump($createdItem);

    //echo $newItemPrice;
    //echo $newItemUnitCost;
    


    //var_dump($data);  
    return 'it works';
    exit;

};
