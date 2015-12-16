<?php namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use DB;
use Schema;

class mainItemFeedController extends Controller {

    function index() {
        if (Auth::check()) {
            //echo "USER IS LOGGED IN"; // The user is logged in...

            //-------------------If locations table does not exist, create table---//
            if (!Schema::hasTable('locations')){
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

                //-------------------Get Locations from square--------------//
                $uri = 'https://connect.squareup.com/v1';
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

                var_dump($locationsList);

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
            }else{
                echo 'already created';
            }



            $mainItemFeedStorage = [];
            /*foreach($locationsList['location'] as $location){
                $itemsRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location['id'].'/items', [
                    'headers' => [
                        'Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
                ]);
            
                //store response
                $itemContents = $itemsRequest->getBody();
                $itemList['itemDescription'] = json_decode($itemContents, true);
            

                $inventoryRequest = $client->request('GET', 'https://connect.squareup.com/v1/'.$location['id'].'/inventory', [
                    'headers' => [
                        'Authorization' => 'Bearer '.$access_token ,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
                ]);


                //store response
                $inventoryContents = $inventoryRequest->getBody();
                $inventoryList['inventoryLevel'] = json_decode($inventoryContents, true);

                //saves location to place in same level index as item inventory and item description
                $indexPerLocation['location'] = $location;
                
                //combine inventory nad item description arrays to pass as big array
                $locationItemsInventory = $indexPerLocation+$itemList+$inventoryList;
                //var_dump($itemsInventory);
                array_push($mainItemFeedStorage, $locationItemsInventory);
            }*/




            $mainItemFeed['data'] = $mainItemFeedStorage;
            //DEN -> 1H5A5ZGP2T4DA
            //PHX -> 3526BMVFNJZZX
            //OUT -> 9SQD525GSB3T3

            //make request to get item list

            
            //make request to get inventory

            //var_dump($test);
            return view('mainItemFeed', $mainItemFeed);
            
        }else{
            return redirect('/auth/register');
        }
    }
}