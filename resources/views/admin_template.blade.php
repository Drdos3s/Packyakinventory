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

    <script src="{{ asset ("/js/packyakJS") }}" type="text/javascript"></script>
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
    </body>
@endsection
