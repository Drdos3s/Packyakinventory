@extends('main')

@section('pagespecificstyles')
    <!-- flot charts css-->
    <link rel="stylesheet" href="{{ asset('assets/lib/owl-carousel/flot.css') }}">

@stop

@section('fullPage')
<body class="skin-green sidebar-collapse sidebar-mini">
        <div class="wrapper">

            <!-- Header Bar  THESE NEED TO BE INCLUDED ON ADMIN MAGES OR WITHIN ANOTHER TEMPLATE THAT EXTENDS THIS ONE-->
            @include('headerBar')

            <!-- Sidebar -->
            @include('sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <!-- You can dynamically generate breadcrumbs here -->
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Here</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

<!------------------------------------------------>

                    <div class="row">
                        <!--
                            *
                            **** START FILTER BOX ****
                            *
                        -->
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Filters</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body"> 
                                    <div class="locationsContainer">
                                        @foreach($locations as $location)
                                            <div class="selectLocation">
                                                <input type="checkbox" class="locationCheckbox" data-locationID="{{ $location['squareID'] }}">
                                                <span>{{ $location['locationCity'] }}</span>
                                            </div>
                                        @endforeach
                                        <button type="button" class="btn btn-primary searchLocationsButton col-sm-2">Get Items</button>
                                    </div>

                                    <div class="col-sm-12 input-group searchContainer">
                                      <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                                      <input type="text" id="packyakInventoryDashSearch" class="form-control" placeholder="Search Items" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                            *
                            **** END FILTER BOX ***
                        -->

                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Items</h3>
                                </div>
                                <div class="box-body"> 
                                    <!--Starting warning-->
                                    <div class="callout callout-info noItemCallout">
                                        <h4><i class="icon fa fa-warning"></i> No items are selected at this time!</h4>
                                        Start by selecting a location or searching for an item.
                                    </div>
                                    <!--loading spinner-->
                                    <div id="loadingImage" class="col-sm-2 col-sm-offset-5 hidden">
                                      <i class="fa fa-cog fa-spin fa-3x fa-fw margin-bottom"></i>
                                    </div>
                                    <table id="packyakInventoryDashTable" class="table hidden">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Category</th>
                                                <th>Item Name</th>
                                                <th>Variation</th>
                                                <th>Inventory</th>
                                                <th>Price</th>
                                                <th>Unit Cost</th>
                                                <th>Margin</th>
                                                <th>SKU</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            {{ csrf_field() }}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Start Delete Item Modal-->
                    <div class="modal modal-danger" id="deleteItemModal" data-controls-modal="#deleteItemModal" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                                <h4 class="modal-title">Danger Modal</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this variation in both here AND Square?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-outline deleteItemConfirmButton"><span class="innerText">Delete Variation</span><i class="fa fa-cog fa-spin fa-fw hidden deleteItemSpinner"></i></button>

                            </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                      <!-- /.modal-dialog -->
                    </div>


<!------------------------------------------------>

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->
        </div><!-- ./wrapper -->
              
</body>

<!------------------------------------------------>


@endsection