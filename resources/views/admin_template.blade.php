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
        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
              page. However, you can choose any other skin. Make sure you
              apply the skin class to the body tag so the changes take effect.
        -->
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


$access_token = 'KI0ethBHis2N76q1jyYung';
$curl = curl_init();
curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'Accept: application/json'));
curl_setopt($curl, CURLOPT_URL, 'https://connect.squareup.com/v1/me/payments?begin_time=2013-01-15T00:00:00Z&end_time=2013-01-31T00:00:00Z');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

$json = curl_exec($curl);
curl_close($curl);
echo $json;


/*require_once base_path('vendor/autoload.php');



require_once base_path('vendor/mashape/unirest-php/src/Unirest.php');

                $access_token = 'KI0ethBHis2N76q1jyYung';

                # The base URL for every Connect API request
                $connectHost = 'https://connect.squareup.com';

# Helper function to convert cent-based money amounts to dollars and cents
function formatMoney($money) {
  return money_format('%+.2n', $money / 100);
}
# Retrieves all of a merchant's payments from 2014
function get2014Payments() {
  global $accessToken, $connectHost;
  # Restrict the request to the 2014 calendar year, eight hours behind UTC
  # Make sure to URL-encode all parameters
  $parameters = http_build_query(
    array(
      'begin_time' => '2015-01-01T00:00:00-08:00',
      'end_time'   => '2015-01-02T00:00:00-08:00'
    )
  );
  # Standard HTTP headers for every Connect API request
  $requestHeaders = array (
    'Authorization' => 'Bearer ' . $accessToken,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
  );
  $payments = array();
  $requestPath = $connectHost . '/v1/me/payments?' . $parameters;
  $moreResults = true;
  while ($moreResults) {
    # Send a GET request to the List Payments endpoint
    $response = Unirest\Request::get($requestPath, $requestHeaders);
    # Read the converted JSON body into the cumulative array of results
    $payments = array_merge($payments, $response->body);
    # Check whether pagination information is included in a response header, indicating more results
    if (array_key_exists('Link', $response->headers)) {
      $paginationHeader = $response->headers['Link'];
      if (strpos($paginationHeader, "rel='next'") !== false) {
        # Extract the next batch URL from the header.
        #
        # Pagination headers have the following format:
        # <https://connect.squareup.com/v1/MERCHANT_ID/payments?batch_token=BATCH_TOKEN>;rel='next'
        # This line extracts the URL from the angle brackets surrounding it.
        $requestPath = explode('>', explode('<', $paginationHeader)[1])[0];
      } else {
        $moreResults = false;
      }
    } else {
      $moreResults = false;
    }
  }
  # Remove potential duplicate values from the list of payments
  $seenPaymentIds = array();
  $uniquePayments = array();
  foreach ($payments as $payment) {
    if (array_key_exists($payment->id, $seenPaymentIds)) {
      continue;
    }
    $seenPaymentIds[$payment->id] = true;
    array_push($uniquePayments, $payment);
  }
  return $uniquePayments;
}
# Prints a sales report based on an array of payments
function printSalesReport($payments) {
  # Variables for holding cumulative values of various monetary amounts
  $collectedMoney = $taxes = $tips = $discounts = $processingFees = 0;
  $returned_processingFees = $netMoney = $refunds = 0;
  # Add appropriate values to each cumulative variable
  foreach ($payments as $payment) {
    $collectedMoney  = $collectedMoney  + $payment->total_collected_money->amount;
    $taxes           = $taxes           + $payment->tax_money->amount;
    $tips            = $tips            + $payment->tip_money->amount;
    $discounts       = $discounts       + $payment->discount_money->amount;
    $processingFees  = $processingFees  + $payment->processing_fee_money->amount;
    $netMoney        = $netMoney        + $payment->net_total_money->amount;
    $refunds         = $refunds         + $payment->refunded_money->amount;
    # When a refund is applied to a credit card payment, Square returns to the merchant a percentage 
    # of the processing fee corresponding to the refunded portion of the payment. This amount
    # is not currently returned by the Connect API, but we can calculate it as shown:
    # If a processing fee was applied to the payment AND some portion of the payment was refunded...
    if ($payment->processing_fee_money->amount < 0 and $payment->refunded_money->amount < 0) {
      # ...calculate the percentage of the payment that was refunded...
      $percentage_refunded = $payment->refunded_money->amount / (float)$payment->total_collected_money->amount;
      # ...and multiply that percentage by the original processing fee
      $returned_processingFees = $returned_processingFees + ($payment->processing_fee_money->amount * $percentage_refunded);
    }
  }
  # Calculate the amount of pre-tax, pre-tip money collected
  $basePurchases = $collectedMoney - $taxes - $tips;
  # Print a sales report similar to the Sales Summary in the merchant dashboard.
  echo '<pre>';
  echo '==SALES REPORT FOR 2014==' . '<br/>';
  echo 'Gross Sales:       ' . formatMoney($basePurchases - $discounts) . '<br/>';
  echo 'Discounts:         ' . formatMoney($discounts) . '<br/>';
  echo 'Net Sales:         ' . formatMoney($basePurchases) . '<br/>';
  echo 'Tax collected:     ' . formatMoney($taxes) . '<br/>';
  echo 'Tips collected:    ' . formatMoney($tips) . '<br/>';
  echo 'Total collected:   ' . formatMoney($basePurchases + $taxes + $tips) . '<br/>';
  echo 'Fees:              ' . formatMoney($processingFees) . '<br/>';
  echo 'Refunds:           ' . formatMoney($refunds) . '<br/>';
  echo 'Fees returned:     ' . formatMoney($returned_processingFees) . '<br/>';
  echo 'Net total:         ' . formatMoney($netMoney + $refunds + $returned_processingFees) . '<br/>';
  echo '</pre>';
}
# Call the functions defined above
$payments = get2014Payments();
printSalesReport($payments);*/





                ?> 
                @yield('content')
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Footer -->
        @include('footer')

    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.3.min.js") }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>

    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
    </body>
</html>