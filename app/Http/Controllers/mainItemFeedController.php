<?php 
namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

use DB;
use Schema;
use App\Item;
use App\Location;


class mainItemFeedController extends Controller {

    function getInventory($token, $client, $location){
        //Retrieve data from square about inventory
        $inventoryRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location.'/inventory', [
            'headers' => [
                'Authorization' => 'Bearer '.$token ,
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
                        'Authorization' => 'Bearer '.$token ,
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

    //Function outside of progressing things to save the items to DB
    function saveItemsToDB($itemList, $location){
        foreach($itemList as $item){ 
            foreach ($item['variations'] as $variation) { // <- check each variation
                if($variation['track_inventory'] == true){ //<- are we tracking inventory for that item variation?

                    //define variables for each item variation
                    $itemInDB = Item::firstOrNew(['itemVariationID' => $variation['id'], 'itemLocationID' => $location]);
                    $itemInDB -> squareItemID = $item['id']; //DOne
                    
                    if(isset($item['category']['name'])){
                        $itemInDB -> itemCategoryName = $item['category']['name'];//DOne
                        $itemInDB -> itemCategoryID = $item['category']['id'];//Done
                    }else{
                        $itemInDB -> itemCategoryName = 'No Category';//DOne
                        $itemInDB -> itemCategoryID = 'No Category ID';//Done
                    }

                    $itemInDB -> itemName = $item['name']; //Done
                    $itemInDB -> itemVariationName = $variation['name'];//Done
                    $itemInDB -> itemVariationID = $variation['id'];//Done

                    if(isset($variation['price_money']['amount'])){
                        $itemInDB -> itemVariationPrice = $variation['price_money']['amount'];//Done
                    }else{
                        $itemInDB -> itemVariationPrice = 'No Price';//Done
                    }
                    if(isset($variation['sku'])){
                        $itemInDB -> itemVariationSKU = $variation['sku'];//Done
                    }else{
                        $itemInDB -> itemVariationSKU = 'No SKU';//Done
                    }
                    
                    $itemInDB -> locationSoldAt = DB::table('locations')->where('squareID', $location)->value('locationCity');//Done
                    $itemInDB -> itemLocationID = $location;
                    $itemInDB -> save();
               } 
            }
        }
    }

    function createAndUpdateItems(Request $request) {
        $locationsListID = $request -> locations;
        $completedItemList = [];

        //Set up tokens and variables
        $access_token = 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ';
        $client = new Client();

        //Getting all the items from square
        foreach($locationsListID as $location){
            //var_dump($location);
            $itemsRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location.'/items', [
                    'headers' => [
                        'Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
            ]);

            $itemContents = $itemsRequest->getBody();
            $itemList = json_decode($itemContents, true);
            //var_dump($itemList);

            //Save new items
            $this -> saveItemsToDB($itemList, $location);
            //Get inventory and match with items
            $this -> getInventory($access_token, $client, $location);

            $locationName = DB::table('locations')->where('squareID', '=', $location)->value('locationCity');

            array_push($completedItemList, DB::table('inventoryList')->where('locationSoldAt','=', $locationName)->orderBy('itemName', 'asc')->get());
        }//endforeach
        return $completedItemList;
        //echo 'made it to the bottom of create items';
        
    }

    function createLocations($token) {
        //Count number of locations
        $numberOfLocations = count(DB::table('locations')->get());
        $locationListID = [];

        //access token that is created through square
        $access_token = $token;
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

        //get asssoc array from body to use to place into DB
        $locationsList = json_decode($locationsContents, true);

        //var_dump($locationsList);
        if($numberOfLocations !== count($locationsList)){
            //-------------------Get location variables and add to database--------------//
            foreach($locationsList as $location){
                //Initialize new location to be stored
                $squareLocation = new Location;

                $squareLocation -> squareID = $location['id'];
                $squareLocation -> businessName = $location['name'];
                $squareLocation -> businessEmail = $location['email'];
                $squareLocation -> locationAddressLine1 = $location['business_address']['address_line_1'];
                $squareLocation -> locationAddressLine2 = $location['business_address']['address_line_2'];
                $squareLocation -> locationCity = $location['business_address']['locality'];
                $squareLocation -> locationState = $location['business_address']['administrative_district_level_1'];
                $squareLocation -> locationZip = $location['business_address']['postal_code'];
                $squareLocation -> locationPhone = $location['business_phone']['number'];

                $squareLocation -> locationNickname = $location['location_details']['nickname'];

                $squareLocation -> save();

                array_push($locationListID, $squareLocation->value('squareID'));
            }
            //echo 'locations have been updated ';
            //return $this->createAndUpdateItems($access_token, $locations);
        }else{
            //echo 'locations did not need updated ';
            //Grabs all the location ID's to work with moving forward
            $locationListID = DB::table('locations')->lists('squareID');
            //var_dump($locationListID);
            //return $this->createAndUpdateItems($access_token, $locationListID);
        }
    }

    function index() {

        if (Auth::check()){//The user is logged in
            //set the personal access token - used to make request to V1 API
            $access_token = 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ';
            $this->createLocations($access_token); 
            
            //Get all the items and send them to the view
            $dashLocations = Location::all();






            //Testing item responses
            $client = new Client();
            $locationsRequest = $client->request('GET', 'https://connect.squareup.com/v1/1H5A5ZGP2T4DA/items/PC2R2A5R4EUDYPT4PGPFVCSE', [
                'headers' => [
                    'Authorization' => 'Bearer '.$access_token ,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);
            //store response
            $locationsContents = $locationsRequest->getBody();

            //get asssoc array from body to use to place into DB
            $locationsList = json_decode($locationsContents, true);
            //var_dump($locationsList);


            //JB5EZDSFSFBPAHHUUGORGZ7M['variations'][0]
            //JB5EZDSFSFBPAHHUUGORGZ7M






            return view('mainItemFeed', ['locations' => $dashLocations]);
        }else{
            return redirect('/auth/login');
        }
    }

    function setupAndSendInventoryUpdate() {
        $access_token = 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ';
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

    function deleteItemVariation(Request $request){
        $access_token = 'sq0atp-Pw3jbxOs3j4szZc7eUm1FQ';
        $client = new Client();
        
        
        //get the item
        $itemID = DB::table('inventoryList')->where('itemVariationID', $request -> variation)->where('itemLocationID', $request -> locationID) ->value('squareItemID');
        echo $itemID;
        
        $itemLocationID = $request -> locationID;
        //echo $itemLocationID;
        

        $itemVariationID = $request -> variation;
        //echo $itemVariationID;

        //Delete Item in DB
        $deletedRow = Item::where('itemVariationID', $request -> variation)->where('itemLocationID', $request -> locationID) ->delete();

        //set up response
        try{
            $client->delete('https://connect.squareup.com/v1/'.$itemLocationID.'/items/'.$itemID.'/variations/'.$itemVariationID, [
                'headers' => ['Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                        ]
            ]);
        }catch(ClientException $e){
            $reason = $e->getResponse()->getReasonPhrase();

            //last item variation
            if($reason == 'Bad Request'){
                $client->delete('https://connect.squareup.com/v1/'.$itemLocationID.'/items/'.$itemID, [
                    'headers' => ['Authorization' => 'Bearer '.$access_token ,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                            ]
                ]);
            }
        }
        return json_encode($request);
    }
}