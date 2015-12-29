<?php namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use GuzzleHttp\Client;
use DB;
use Schema;


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


            $numItemsInDB = DB::table('inventoryList')
                ->where('locationSoldAt', $location['locationCity'])
                ->count();
            
            $numVariationsFromSquare = 0;

            foreach($itemList as $item){ 
                foreach ($item['variations'] as $variation) { // <- check each variation
                    if($variation['track_inventory'] == true){ //<- are we tracking inventory for that item?

                        $numVariationsFromSquare++;
                    }
                }
            }


            if($numItemsInDB == 0){
                echo 'Fresh Install';
                foreach($itemList as $item){ 
                    foreach ($item['variations'] as $variation) { // <- check each variation
                        if($variation['track_inventory'] == true){ //<- are we tracking inventory for that item?

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

                            DB::insert('insert into inventoryList (squareItemID, itemName, itemCategoryName,
                                                                    itemCategoryID, itemVariationName, itemVariationID,
                                                                    itemVariationPrice, itemVariationSKU, locationSoldAt
                                                                    ) values (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                                                                    [$squareItemID, $itemName, $itemCategoryName, 
                                                                     $itemCategoryID, $itemVariationName, $itemVariationID,
                                                                     $itemVariationPrice, $itemVariationSKU, $locationSoldAt]);
                            //echo $location['locationState'].' '.$item['name'].' '.$variation['name'].isset($variation['sku']).' - '; // <- print out item name and variation name
                             
                       } 
                    }
                }
            }elseif($numItemsInDB == $numVariationsFromSquare){
                echo 'No need to update, Please proceed - ';
            }else{
                echo 'Needs to be updated - ';
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
        if (Auth::check()) {
            //echo "USER IS LOGGED IN"; // The user is logged in...

            //-------------------If locations table does not exist, create table---//
            if (!Schema::hasTable('locations') || !Schema::hasTable('inventoryList')){
                Schema::create('locations', function($table){
                    $table->increments('id');
                    $table->char('squareID', 255);
                    $table->char('businessName', 255);
                    $table->char('businessEmail', 255);
                    $table->char('locationAddressLine1', 255);
                    $table->char('locationAddressLine2', 255);
                    $table->char('locationCity', 255);
                    $table->char('locationState', 255);
                    $table->char('locationZip', 255);
                    $table->char('locationPhone', 255);
                    $table->char('locationNickname', 255);
                });

                Schema::create('inventoryList', function($table){
                    $table->increments('id');
                    $table->char('squareItemID', 255); //<- use the item id from the variation if it is easier
                    $table->char('itemName', 255);
                    $table->char('itemCategoryName', 255);
                    $table->char('itemCategoryID', 255);
                    $table->char('itemVariationName', 255);
                    $table->char('itemVariationID', 255);
                    $table->char('itemVariationPrice', 255);
                    $table->char('itemVariationSKU', 255);
                    $table->char('locationSoldAt', 255);
                    $table->char('itemVariationInventory', 255);

                });
                //echo 'tables got built ';
                return $this->createLocations();
            }else{
                //echo 'tables were already built ';
                return $this->createLocations();
            }   
        }else{
            return redirect('/auth/register');
        }
    }

    function setupAndSendInventoryUpdate() {
        if(Request::ajax()) {
            $access_token = 'KI0ethBHis2N76q1jyYung';
            $client = new Client();
            $itemLocation = $_POST['itemLocation'];
            $itemVariationID = $_POST['itemVariationID'];
            $quantityDelta = $_POST['quantityDelta'];
            $body = json_encode(array('quantity_delta' => $quantityDelta, 'adjustment_type' => 'RECEIVE_STOCK'), JSON_FORCE_OBJECT);
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