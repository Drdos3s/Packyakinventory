@extends('adminLayout')

@section('pagespecificstyles')
    <!-- vendor page speciic styles-->

    <!-- Select 2 for multiple tags -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    
@stop

@section('content')

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

<!--MODAL MENU FOR THAT ITEMS OPTIONS SUCH AS PICKING VENDOR AND PURCHASE ORDER TO ADD TOO OPTIONS-->
<div class="row">     
    <div class='col-md-12'>
        <div class="modal fade" id="itemOptionsModal" role="dialog" aria-labelledby="itemOptions">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="itemOptions">Options</h4>
                        <span class='pypoidModal hidden'></span>
                    </div>

                    <div class="modal-body">
                        <!-- This is where the content of the modal will go -->
                        <div col-sm-12>
                            <h1 class="itemName">None</h1>
                            <h3 class="itemVariation">None</h3>

                            <hr>

                            <h5 class="itemLocation">None</h5>
                            <h5 class="itemCategory">None</h5>
                            <h5 class="itemInventory">None</h5>
                            <h5 class="itemPrice">None</h5>
                            <h5 class="itemCost">None</h5>
                            <h5 class="itemMargin">None</h5>
                            <h5 class="itemSKU">None</h5>
                            <h5 class="variationID">None</h5>
                        </div>

                        <form class="form-horizontal itemOptionsForm">
                            <div class="dropdown">
                                <i class="fa fa-bars fa-2 btn btn-default dropdown-toggle packyakPurchaseOrderList" type="button" id="packyakPurchaseOrderList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                                <ul class="dropdown-menu packyakAddItemToPOWrapper" aria-labelledby="packyakPurchaseOrderList">
                                </ul>
                            </div> 

                            <div class="col-md-12">
                              <div class="form-group">
                                <label>Vendors</label>
                                <select class="form-control select2" id='multiVendorSelect' multiple="multiple" data-placeholder="Select a State" style="width: 100%;">
                                    @foreach($vendors as $vendor)
                                        <option value="{{$vendor['id']}}">{{ $vendor['company_name'] or 'Error' }}</option>
                                    @endforeach
                                </select>
                              </div>
                            </div><!-- /.col -->


                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary saveItemEditInfoButton">Save</button>
                            </div>
                            {{ csrf_field() }}  
                        </form>


                        <!-- ___________________END OF MODAL FORM FOR CREATE/ EDIT PURCHASE ORDER______________________ -->
                    </div>
                </div>
            </div>
        </div><!--END MODAL-->
    </div><!--END WRAPPER-->
</div><!--END ROW-->
@endsection

@section('pagespecificscripts')
    <!-- mainItemFeed page speciic scripts-->
    <script src="{{ asset ("/js/itemFeed.js") }}" type="text/javascript"></script>

    <!-- Select 2 for multiple tags -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

@stop