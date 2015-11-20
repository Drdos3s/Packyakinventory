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
            $access_token = 'KI0ethBHis2N76q1jyYung';
            $requestHeaders = array (
                'Authorization' => 'Bearer '.$access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            );

            $client = new Client();

      //DEN -> 1H5A5ZGP2T4DA
      //PHX -> 3526BMVFNJZZX
      //OUT -> 9SQD525GSB3T3

            //make request to get item list
            $itemsRequest = $client->request('GET', 'https://connect.squareup.com/v1/1H5A5ZGP2T4DA/items/', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token ,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
            ]);
            
            //store response
            $itemContents = $itemsRequest->getBody();
            $itemList['itemDescription'] = json_decode($itemContents, true);
            
            //make request to get inventory
            $inventoryRequest = $client->request('GET', 'https://connect.squareup.com/v1/1H5A5ZGP2T4DA/inventory', [
            'headers' => [
                'Authorization' => 'Bearer '.$access_token ,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
            ]);

            //store response
            $inventoryContents = $inventoryRequest->getBody();
            $inventoryList['inventoryLevel'] = json_decode($inventoryContents, true);
            
            //combine inventory nad item description arrays to pass as big array
            $itemsInventory = $itemList+$inventoryList;
            //var_dump($itemsInventory);

            return view('mainItemFeed')->with($itemsInventory);
            
        }else{
            return redirect('/auth/register');
        }
    }
}