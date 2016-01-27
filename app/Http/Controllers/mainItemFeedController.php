<?php 
namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use GuzzleHttp\Client;
use DB;
use Schema;
use App\Item;


class mainItemFeedController extends Controller {
    function sendDataToFeedView(){
        $allItems = DB::select('select * from inventoryList');
        $itemData['items'] = json_decode(json_encode($allItems),true);
        return view('mainItemFeed', $itemData);
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
            $inventoryList = json_decode($inventoryContents, true);
            //var_dump($inventoryList);

            foreach ($inventoryList as $itemInv) {
                DB::table('inventoryList')
                    ->where('itemVariationID', $itemInv['variation_id'])
                    ->update(['itemVariationInventory' => $itemInv['quantity_on_hand']]);
            }
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
            $adjustmentType = 'RECEIVE_STOCK';

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
            $updateInventoryRequest = $client->post('https://connect.squareup.com/v1/'.$itemLocationID.'/inventory/'.$itemVariationID, [
                'headers' => ['Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                        ], 
                        'body' => $body
            ]);

            $updateInventoryReponse = json_decode($updateInventoryRequest->getBody(), true);
            return $updateInventoryReponse['quantity_on_hand'];
        }
    }
}