@extends('master_template')

@section('body')
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
                    {{ $page_title or "LOCATION ITEMS AND INVENTORY" }}
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
@endsection
