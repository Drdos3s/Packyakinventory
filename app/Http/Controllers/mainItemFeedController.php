<?php namespace App\Http\Controllers;

use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

class mainItemFeedController extends Controller {

    function index() {
        if (Auth::check()) {
            //echo "USER IS LOGGED IN"; // The user is logged in...

            $uri = 'https://connect.squareup.com/v1';
            //access token that is created through square
            $access_token = 'KI0ethBHis2N76q1jyYung';

            $mainItemFeedData = [];

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
            $locationsList['location'] = json_decode($locationsContents, true);

            //var_dump($locationsList);

            foreach($locationsList['location'] as $location){
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
                array_push($mainItemFeedData, $locationItemsInventory);
            }





            //DEN -> 1H5A5ZGP2T4DA
            //PHX -> 3526BMVFNJZZX
            //OUT -> 9SQD525GSB3T3

            //make request to get item list

            
            //make request to get inventory
            echo count($mainItemFeedData);
            var_dump($mainItemFeedData);

            return view('mainItemFeed')->with($mainItemFeedData);
            
        }else{
            return redirect('/auth/register');
        }
    }
}