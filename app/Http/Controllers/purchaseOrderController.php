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

    //***********Working list category request********************//
    /*$categoryRequest = $client->request('GET', 'https://connect.squareup.com/v1/1H5A5ZGP2T4DA/categories', [
                'headers' => [
                    'Authorization' => 'Bearer '.$access_token ,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            //store response
            $categoryContents = $categoryRequest->getBody();
            $categoryList = json_decode($categoryContents, true);
            var_dump($categoryList);*/

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
    $newItemVariationsRaw       = $createdItem->newItemVariations;
    $newItemLocationSoldAt      = $createdItem->newItemLocationSoldAt;

    
    $newItemVariationsDecoded = json_decode(json_encode($newItemVariationsRaw), true);
    $variationsListForSquare = [];
    $access_token = 'KI0ethBHis2N76q1jyYung';
    $client = new Client();

    foreach($newItemVariationsDecoded as $newItemVariation){
        $formattedVariation = array(//need to make this run through all the variations that are created
                'name' => $newItemVariation['newVariationName'],
                'price_money' => array(
                  'currency_code' => 'USD',
                  'amount' => $newItemVariation['newVariationPrice']
                ),
                'track_inventory'=> true,
                'inventory_alert_type'=> "LOW_QUANTITY"
            );

        array_push($variationsListForSquare, $formattedVariation);
    }

    //var_dump($variationsListForSquare);

    //test category id used for testing ->> 67c8e187-45af-4795-ba56-985f88051453
    $postData = array(
        'name' => 'test item',
        'variations' => $variationsListForSquare
    );

    $json = json_encode($postData);

    foreach($newItemLocationSoldAt as $newSquareItemLocation){
        $existingLocation = json_decode( 
                                json_encode(
                                    DB::table('locations')
                                    ->where('locationCity', '=', $newSquareItemLocation)
                                    ->get()
                                ),true);

        # Creates an item with the input values item.
        $itemsRequest = $client->request('POST', 'https://connect.squareup.com/v1/'.$existingLocation[0]['squareID'].'/items', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token ,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ], 'body' => $json
        ]);
    }

    $itemsContents = $itemsRequest->getBody();
    $itemsList = json_decode($itemsContents, true);

    return $itemsContents;
    exit;

};
