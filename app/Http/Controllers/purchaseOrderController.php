<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use App\purchaseOrder;
use DB;

class purchaseOrderController extends Controller
{
    public function index(){
        $existingPurchaseOrders = DB::table('purchase_orders')->get();

        return view('purchaseOrder', ['existingPurchaseOrders' => $existingPurchaseOrders]);
    }

    public function createNewPurchaseOrder(){
    	if(Request::ajax()) {
            //retriving variables from modal form
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


            /*DB::table('purchase_orders')->insert(
                ['po_name' => $po_name, 'po_status' => $po_status, 'po_vendor' => $po_vendor, 'po_invoice_number' => $po_invoice_number, 'po_location' => $po_location]
            );*/

            $createdPurchaseOrder = json_encode(array('po_name' => $po_name, 'po_status' => $po_status, 'po_vendor' => $po_vendor, 'po_invoice_number' => $po_invoice_number, 'po_location' => $po_location));
    		return $createdPurchaseOrder; 

    	}
    }
}
