<?php 
namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use DB;
use Schema;
use App\Item;


class mainItemFeedController extends Controller {
    function sendDataToFeedView(){
        $dashboardData['items'] = DB::select('select * from inventoryList');
        $dashboardData['purchaseOrders'] = DB::table('purchase_orders')->where('po_status', '=', 'pending')->get();

        //$testVar = json_decode(json_encode(array('$itemData' => $allItems, 'purchaseOrders' => $openPurchaseOrders)),true);
        //var_dump($dashboardData);
        $dashboardDataFinal = json_decode(json_encode($dashboardData),true);
        //var_dump($itemData['items'][0]);
        return view('mainItemFeed', ['dashboardDataFinal' => $dashboardDataFinal]);
    }

    function getInventory(){
            $access_token = 'KI0ethBHis2N76q1jyYung';
            $client = new Client();
            $locationsQuery = DB::select('select * from locations');
            $locations = json_decode(json_encode($locationsQuery),true);
            

        foreach($locations as $location){
            $inventoryRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location['squareID'].'/inventory', [
                'headers' => [
                    'Authorization' => 'Bearer '.$access_token ,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            //store response
            $inventoryContents = $inventoryRequest->getBody();
            $responseHeaders = $inventoryRequest->getHeaders();
            //var_dump($responseHeaders);

            $inventoryList = json_decode($inventoryContents, true);
            //echo $location['squareID'].": ".count($inventoryList).' ';
            //echo 'Response header: '.$responseHeaders['Link'][0].' ';



            foreach ($inventoryList as $itemInv) {
                DB::table('inventoryList')
                    ->where('itemVariationID', $itemInv['variation_id'])
                    ->update(['itemVariationInventory' => $itemInv['quantity_on_hand']]);
            }

            while($inventoryRequest->hasHeader('Link')):

                $parsedHeader = Psr7\parse_header($inventoryRequest->getHeader('Link'));

                if ($parsedHeader[0]['rel'] == 'next') {
                  # Extract the next batch URL from the header.
                  # Pagination headers have the following format:
                  # <https://connect.squareup.com/v1/MERCHANT_ID/payments?batch_token=BATCH_TOKEN>;rel='next'
                  # This line extracts the URL from the angle brackets surrounding it.
                    $requestPath = explode('>', explode('<', $parsedHeader[0][0])[1])[0];

                    $inventoryRequest = $client->request('GET', $requestPath, [
                        'headers' => [
                            'Authorization' => 'Bearer '.$access_token ,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ]
                    ]);
                    $inventoryContents = $inventoryRequest->getBody();

                     $inventoryList = json_decode($inventoryContents, true);

                    foreach ($inventoryList as $itemInv) {
                        DB::table('inventoryList')
                            ->where('itemVariationID', $itemInv['variation_id'])
                            ->update(['itemVariationInventory' => $itemInv['quantity_on_hand']]);
                    };
                };
            endwhile;
        }
            //DEN -> 1H5A5ZGP2T4DA
            //PHX -> 3526BMVFNJZZX
            //OUT -> 9SQD525GSB3T3
        return $this->sendDataToFeedView();    
    }

    function createAndUpdateItems() {
        $access_token = 'KI0ethBHis2N76q1jyYung';
        $client = new Client();
        $locationsQuery = DB::select('select * from locations');
        $locations = json_decode(json_encode($locationsQuery),true);
        

        
        foreach($locations as $location){
            //var_dump($location);
            $itemsRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location['squareID'].'/items', [
                    'headers' => [
                        'Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
            ]);

            $itemContents = $itemsRequest->getBody();
            $itemList = json_decode($itemContents, true);

                foreach($itemList as $item){ 
                    foreach ($item['variations'] as $variation) { // <- check each variation
                        if($variation['track_inventory'] == true){ //<- are we tracking inventory for that item variation?

                            //define variables for each item variation

                            $squareItemID = $item['id'];
                            if(isset($item['category']['name'])){
                                $itemCategoryName = $item['category']['name'];
                                $itemCategoryID = $item['category']['id'];
                            }else{
                                $itemCategoryName = 'No Category';
                                $itemCategoryID = 'No Category ID';
                            }

                            $itemName = $item['name'];
                            $itemVariationName = $variation['name'];
                            $itemVariationID = $variation['id'];

                            if(isset($variation['price_money']['amount'])){
                                $itemVariationPrice = $variation['price_money']['amount'];
                            }else{
                                $itemVariationPrice = 'No Price';
                            }
                            if(isset($variation['sku'])){
                                $itemVariationSKU = $variation['sku'];
                            }else{
                                $itemVariationSKU = 'No SKU';
                            }
                            
                            $locationSoldAt = $location['locationCity'];
                            
                            $itemInDB = Item::firstOrNew(['itemVariationID' => $itemVariationID]);
                            $itemInDB -> squareItemID = $squareItemID; 
                            $itemInDB -> itemName = $itemName; 
                            $itemInDB -> itemCategoryName = $itemCategoryName;                     
                            $itemInDB -> itemCategoryID = $itemCategoryID; 
                            $itemInDB -> itemVariationName = $itemVariationName; 
                            $itemInDB -> itemVariationID = $itemVariationID;                    
                            $itemInDB -> itemVariationPrice = $itemVariationPrice; 
                            $itemInDB -> itemVariationSKU = $itemVariationSKU; 
                            $itemInDB -> locationSoldAt = $locationSoldAt;
                            $itemInDB -> save();
                       } 
                    }
                }
        }
        return $this->getInventory();
    }

    function createLocations() {
        //-------------------Get Locations from square--------------//
        $numberOfLocations = count(DB::select('select * from locations'));

        //access token that is created through square
        $access_token = 'KI0ethBHis2N76q1jyYung';
        $client = new Client();

        $locationsRequest = $client->request('GET', 'https://connect.squareup.com/v1/me/locations', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token ,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        //store response
        $locationsContents = $locationsRequest->getBody();
        $locationsList = json_decode($locationsContents, true);

        //var_dump($locationsList);

        if($numberOfLocations !== count($locationsList)){
            //-------------------Get location variables and add to database--------------//
            foreach($locationsList as $location){
                $squareID = $location['id'];
                $businessName = $location['name'];
                $businessEmail = $location['email'];
                $locationAddressLine1 = $location['business_address']['address_line_1'];
                $locationAddressLine2 = $location['business_address']['address_line_2'];
                $locationCity = $location['business_address']['locality'];
                $locationState = $location['business_address']['administrative_district_level_1'];
                $locationZip = $location['business_address']['postal_code'];
                $locationPhone = $location['business_phone']['number'];
                $locationNickname = $location['location_details']['nickname'];

                DB::insert('insert into locations (squareID, businessName, businessEmail, locationAddressLine1,
                                                   locationAddressLine2, locationCity, locationState, locationZip,
                                                   locationPhone, locationNickname) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                                  [$squareID, $businessName, $businessEmail, $locationAddressLine1,
                                                   $locationAddressLine2, $locationCity, $locationState, $locationZip,
                                                   $locationPhone, $locationNickname]);
            }
            //echo 'locations have been updated ';
            return $this->createAndUpdateItems();
        }else{
            //echo 'locations did not need updated ';
            return $this->createAndUpdateItems();
        }
    }

    function index() {
        if (Auth::check()){//The user is logged in
            return $this->createLocations(); 
        }else{
            return redirect('/auth/register');
        }
    }

    function setupAndSendInventoryUpdate() { //What to do with ajax request 
        if(Request::ajax()) {
            $access_token = 'KI0ethBHis2N76q1jyYung';
            $client = new Client();
            $itemLocation = $_POST['itemLocation'];
            $itemVariationID = $_POST['itemVariationID'];
            $quantityDelta = $_POST['quantityDelta'];
            $updatedUnitPrice = $_POST['updatedUnitPrice']*100;
            $adjustmentType = 'RECEIVE_STOCK';

            //Update database variation with new cost of goods
            DB::table('inventoryList')
                    ->where('itemVariationID', $itemVariationID)
                    ->update(['itemVariationUnitCost' => $updatedUnitPrice]);

            if($quantityDelta < 0){
                $adjustmentType = 'SALE';
            }

            $body = json_encode(array('quantity_delta' => $quantityDelta, 'adjustment_type' => $adjustmentType), JSON_FORCE_OBJECT);
            //echo $body;
            switch($itemLocation){
                case 'Denver':
                    $itemLocationID = '1H5A5ZGP2T4DA';
                    break;
                case 'Phoenix':
                    $itemLocationID = '3526BMVFNJZZX';
                    break;
                case 'Brighton':
                    $itemLocationID = '9SQD525GSB3T3';
                    break;
            }
            //DEN -> 1H5A5ZGP2T4DA
            //PHX -> 3526BMVFNJZZX
            //OUT -> 9SQD525GSB3T3

            //If quantity delta does not equal 0 then send square request
            if($quantityDelta != 0){
                $updateInventoryRequest = $client->post('https://connect.squareup.com/v1/'.$itemLocationID.'/inventory/'.$itemVariationID, [
                'headers' => ['Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                        ], 
                        'body' => $body
                ]);

                $updateInventoryReponse = json_decode($updateInventoryRequest->getBody(), true);

                $successResponse = json_encode(array('itemVariationInventory' => $updateInventoryReponse['quantity_on_hand'], 'itemVariationUnitPrice' => $updatedUnitPrice));
                
                return $successResponse;
            }else{
                //If the quantity delta is 0 then just update and do the stuff. 
                $updatedItem = DB::table('inventoryList')->where('itemVariationID', $itemVariationID)->first();
                $successResponse = json_encode(array('itemVariationInventory' => $updatedItem->itemVariationInventory, 'itemVariationUnitPrice' => $updatedItem->itemVariationUnitCost));
                return $successResponse;
            }
        }
    }
}