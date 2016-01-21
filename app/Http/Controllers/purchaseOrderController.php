<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use GuzzleHttp\Client;
use DB;

class purchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchaseOrder');
    }

    public function createNewPurchaseOrder(){
    	if(Request::ajax()) {
    		$po_name = $_POST['po_name'];
    		$po_status = $_POST['po_status'];
    		$po_vendor = $_POST['po_vendor'];
    		$po_location = $_POST['po_location'];
    		$po_invoice_number = $_POST['po_invoice_number'];

    		return $po_name;

    	}
    }
}
