<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use App\purchaseOrder;
use App\purchaseOrderItem;
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
        
        $purchaseOrderDetailsWithItems = [];
        
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
        

        return view('purchaseOrder', ['existingPurchaseOrders' => $purchaseOrderDetailsWithItems]);
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
                }
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
}
