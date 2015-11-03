<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>{{ $page_title or "PackYak Dashboard" }}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.2 -->
        <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
        <link href="{{ asset("/bower_components/admin-lte/dist/css/skins/skin-green.min.css")}}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
<body class="skin-green">
    <div class="wrapper">

        <!-- Header -->
        @include('header')

        <!-- Sidebar -->
        @include('sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    {{ $page_title or "Page Title" }}
                    <small>{{ $page_description or null }}</small>
                </h1>
                <!-- You can dynamically generate breadcrumbs here -->
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Your Page Content Here -->
                
<?php

//require_once base_path('vendor/mashape/unirestphp/src/Unirest.php');

$access_token = 'KI0ethBHis2N76q1jyYung';

function listLocations() {
  $access_token = 'KI0ethBHis2N76q1jyYung';
  $connectHost = 'https://connect.squareup.com';
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $access_token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );

  $response = Unirest\Request::get($connectHost . '/v1/me/locations', $requestHeaders);

  echo json_encode($response->body, JSON_PRETTY_PRINT);
}
//listLocations();


# Creates a "Milkshake" item.
function createItem() {
  $access_token = 'KI0ethBHis2N76q1jyYung';
  $connectHost = 'https://connect.squareup.com';
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $access_token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );
  

  $request_body = array(
    "name"=>"Milkshake",
    "variations"=>array(
      array(
        "name"=>"Small",
        "pricing_type"=>"FIXED_PRICING",
        "price_money"=>array(
          "currency_code"=>"USD",
          "amount"=>400
        )
      )
    )
  );
  $response = Unirest\Request::post($connectHost . '/v1/me/items/', $requestHeaders, json_encode($request_body));
  echo $response->code;
  echo json_encode($response->body, JSON_PRETTY_PRINT);
  if ($response->code == 200) {
    error_log('Successfully created item:');
    error_log(json_encode($response->body, JSON_PRETTY_PRINT));
    return $response->body;
  } else {
    error_log('Item creation failed');
    return NULL;
  }
}
//createItem();

function checkItemNum() {
  $access_token = 'KI0ethBHis2N76q1jyYung';
  $connectHost = 'https://connect.squareup.com';
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $access_token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );

  //DEN -> 1H5A5ZGP2T4DA
  //PHX -> 3526BMVFNJZZX
  //OUT -> 9SQD525GSB3T3


  $items = array();
  $response = Unirest\Request::get($connectHost . '/v1/3526BMVFNJZZX/items/0063a336-8260-4455-b489-1dba4da0859b', $requestHeaders);

  $json = json_encode($response->body);

  $test = json_decode($json, true);

  var_dump($test);


  echo $test['category']['id'];

  //$items = array_merge($items, $response->body);

  //echo count($items);
}
//checkItemNum();


function getInventory() {
  $access_token = 'KI0ethBHis2N76q1jyYung';
  $connectHost = 'https://connect.squareup.com';
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $access_token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );

  //DEN -> 1H5A5ZGP2T4DA
  //PHX -> 3526BMVFNJZZX
  //OUT -> 9SQD525GSB3T3


  $inventory = array();
  $response = Unirest\Request::get($connectHost . '/v1/3526BMVFNJZZX/inventory', $requestHeaders);

  echo json_encode($response->body, JSON_PRETTY_PRINT);

  $items = array_merge($inventory, $response->body);

  //echo count($inventory);
}
//getInventory();

#Deletes the Malted Milkshake item.
function deleteItem($itemId) {
  $access_token = 'KI0ethBHis2N76q1jyYung';
  $connectHost = 'https://connect.squareup.com';
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $access_token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );


  $response = Unirest\Request::delete($connectHost . '/v1/me/items/' . $itemId, $requestHeaders);
  if ($response->code == 200) {
    error_log('Successfully deleted item');
    return $response->body;
  } else {
    error_log('Item deletion failed');
    return NULL;
  }
}
//deleteItem('e9407738-314a-4b22-950a-a6ba95331f2e');
?> 
                @yield('content')
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Footer -->
        @include('footer')

    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>

    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
    </body>
</html>