<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

class TestController extends Controller {

    function checkItemNum() {
        $uri = 'https://connect.squareup.com/v1';
        $access_token = 'KI0ethBHis2N76q1jyYung';
        $requestHeaders = array (
            'Authorization' => 'Bearer '.$access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        );

        $client = new Client();

        $request = $client->request('GET', 'https://connect.squareup.com/v1/9SQD525GSB3T3/items', [
        'headers' => [
            'Authorization' => 'Bearer KI0ethBHis2N76q1jyYung' ,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]
        ]);
        //echo $res->getStatusCode();
        // // "200"
        // echo $res->getHeader('content-type');
        // // 'application/json; charset=utf8'
        // echo $res->getBody();
        // // {"type":"User"...'
        echo 'it works.';
    }







    // {
    //       $access_token = 'KI0ethBHis2N76q1jyYung';
    //       $connectHost = 'https://connect.squareup.com';
    //       $requestHeaders = array (
    //         'Authorization' => 'Bearer ' . $access_token,
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json'
    //       );

    //       //DEN -> 1H5A5ZGP2T4DA
    //       //PHX -> 3526BMVFNJZZX
    //       //OUT -> 9SQD525GSB3T3


    //       $items = array();
    //       $response = Unirest\Request::get($connectHost . '/v1/3526BMVFNJZZX/items/0063a336-8260-4455-b489-1dba4da0859b', $requestHeaders);

    //       $json = json_encode($response->body);

    //       $test = json_decode($json, true);

    //       //var_dump($test);


    //       //echo $test['category']['id'];

    //       //$items = array_merge($items, $response->body);

    //       echo count($items);
    //     }


        

    public function index() {
        $data['tasks'] = [
                [
                        'name' => 'Design New Dashboard',
                        'progress' => '87',
                        'color' => 'danger'
                ],
                [
                        'name' => 'Create Home Page',
                        'progress' => '76',
                        'color' => 'warning'
                ],
                [
                        'name' => 'Some Other Task',
                        'progress' => '32',
                        'color' => 'success'
                ],
                [
                        'name' => 'Start Building Website',
                        'progress' => '56',
                        'color' => 'info'
                ],
                [
                        'name' => 'Develop an Awesome Algorithm',
                        'progress' => '60',
                        'color' => 'success'
                ],
                                [
                        'name' => 'Design New Dashboard',
                        'progress' => '87',
                        'color' => 'danger'
                ],
                [
                        'name' => 'Create Home Page',
                        'progress' => '76',
                        'color' => 'warning'
                ],
                [
                        'name' => 'Some Other Task',
                        'progress' => '32',
                        'color' => 'success'
                ],
                [
                        'name' => 'Start Building Website',
                        'progress' => '56',
                        'color' => 'info'
                ],
                [
                        'name' => 'Develop an Awesome Algorithm',
                        'progress' => '60',
                        'color' => 'success'
                ],
                                [
                        'name' => 'Design New Dashboard',
                        'progress' => '87',
                        'color' => 'danger'
                ],
                [
                        'name' => 'Create Home Page',
                        'progress' => '76',
                        'color' => 'warning'
                ],
                [
                        'name' => 'Some Other Task',
                        'progress' => '32',
                        'color' => 'success'
                ],
                [
                        'name' => 'Start Building Website',
                        'progress' => '56',
                        'color' => 'info'
                ],
                [
                        'name' => 'Develop an Awesome Algorithm',
                        'progress' => '60',
                        'color' => 'success'
                ]

        ];
        
        return view('test')->with($data);
    }

}