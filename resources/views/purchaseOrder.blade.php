@extends('admin_template')

@section('content')

<!-- Button trigger modal -->
<div class='row'>
    <!-- Modal Button for creating a new purchase order -->
    <div class='col-md-12'>            
        <button type="button" class="btn btn-primary btn-md packyakNewPOButton" data-toggle="modal" data-target="#myModal">
            New Purchase Order
        </button>
    </div>
</div>

<div class='row'>
    <div class="col-md-12">
        <div class="pad margin no-print">
          <div class="callout callout-warning" style="margin-bottom: 0!important;">
            <h4><i class="fa fa-info"></i> Attention:</h4>
            This could be a way of notifications in the future with different things happening for things that stack up.
          </div>
        </div>
        <div class="pad margin no-print">
          <div class="callout callout-info" style="margin-bottom: 0!important;">
            <h4><i class="fa fa-info"></i> Note:</h4>
            This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
          </div>
        </div>


        @foreach($existingPurchaseOrders as $purchaseOrder)
            <!-- Main content -->
            <section class="invoice packyakPOHeader">
              <!-- title row -->
              <div class="row">
                <div class="col-xs-12">
                  <h2 class="page-header">
                    <i class="fa fa-globe"></i><span class='packYakPOName'>{{ $purchaseOrder['po_name'] }}</span>
                    <small class="pull-right packYakPOCreated">Date Created: <?php echo ' '.date('m-d-Y',strtotime($purchaseOrder['created_at']));?></small>
                  </h2>
                </div><!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  For
                  <address>
                    <strong><span class='packYakPOVendor'>{{ $purchaseOrder['po_vendor'] }}</span></strong><br>
                    <!--should populate these dynamically with vendor address and info. Phone number at the least-->
                    One Day, Suite 600<br>
                    This will populate, CA 94107<br>
                    Phone: (804) 123-5432<br>
                    Email: info@almasaeedstudio.com
                  </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  To
                  <address>
                    <strong><span class='packYakPOLocation'>{{ $purchaseOrder['po_location'] }}</span></strong><br>
                    795 Folsom Ave, Suite 600<br>
                    San Francisco, CA 94107<br>
                    Phone: (555) 539-1037<br>
                    Email: john.doe@example.com
                  </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>Status:</b> <span class='packYakPOStatus'>{{ $purchaseOrder['po_status'] }}</span><br>
                  <br>
                  <b>Invoice #</b> One day<br>
                  <b>PO # </b><span class='pypoid'><?php echo $purchaseOrder['id'] ?></span><br> 
                  <b>Payment Due:</b> This will<br>
                  <b>Account:</b> be another piece of info
                </div><!-- /.col -->
              </div><!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-xs-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Category </th>
                        <th>Item </th>
                        <th>Variation </th>
                        <th>Current Inventory</th>
                        <th>Order Quantity</th>
                        <th>Unit Cost </th>
                        <th>Total</th>
                        <th>Delete</th> 
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder['po_items'] as $item)
                            <tr class='packyakPOItemListItem'>
                                <td class=" poitemid hidden">{{ $item['itemVariationID'] }}</td>
                                <td><h5>{{ $item['itemCategoryName'] }}</h5></td>
                                <td><h5>{{ $item['itemName'] }}</h5></td>
                                <td><h5>{{ $item['itemVariationName'] }}</h5></td>
                                <td><h5>{{ $item['itemVariationInventory'] }}</h5></td>
                                <td class='packyakOrderQuantityText'><?php echo Form::input('number','orderQuantity', 0, array('class' => 'packyakOrderQuantityInput', 'type' => 'number', 'min' => '0', 'step' => '1')); ?></td>
                                <td><h5>${{ $item['itemVariationUnitCost']/100 }}</h5></td>
                                <td><h5>0</h5></td>
                                <td><i class="fa fa-times-circle fa-2 btn btn-default packyakRemoveFromPO"></i></td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div><!-- /.col -->
              </div><!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                  <!--<p class="lead">Payment Methods:</p>
                  <img src="../../dist/img/credit/visa.png" alt="Visa">
                  <img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
                  <img src="../../dist/img/credit/american-express.png" alt="American Express">
                  <img src="../../dist/img/credit/paypal2.png" alt="Paypal">
                  <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    This is in the PENDING phase which means items can still be added to this purchase order. 
                  </p>-->
                </div><!-- /.col -->
                <div class="col-xs-6">
                  <p class="lead">Status: {{ $purchaseOrder['po_status'] }}</p>
                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%">Subtotal:</th>
                        <td>$250.30</td>
                      </tr>
                      <tr>
                        <th>Tax (9.3%)</th>
                        <td>$10.34</td>
                      </tr>
                      <tr>
                        <th>Shipping:</th>
                        <td>$5.80</td>
                      </tr>
                      <tr>
                        <th>Total:</th>
                        <td>$265.24</td>
                      </tr>
                    </table>
                  </div>
                </div><!-- /.col -->
              </div><!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-xs-12">
                  <a href="invoice-print.html" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
                  <button class="btn btn-success pull-right">Confirm</button>
                  <button class="btn btn-primary" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
                  <button class="btn btn-info pull-right packyackPOEdit" style="margin-right: 5px;"><i class="fa fa-pencil"></i> Edit</button>
                  <button type="button" class="btn bg-maroon pull-right packyakNewItemButton" style="margin-right: 5px;" data-toggle="modal" data-target="#createItemModal">Create New Item</button>
                </div>
              </div>
            </section><!-- /.content -->
        @endforeach
        <div class="clearfix"></div>
    </div>
</div>



















<div class="row">     
    <div class='col-md-12'>
        <?php //var_dump($existingLocations);?>
        <!-- Modal for create and edit PO-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">New Purchase Order</h4>
                        <span class='pypoidModal hidden'></span>
                    </div>
                    <div class="modal-body">
                        <!-- This is where the content of the modal will go -->
                        <form class="form-horizontal newPurchaseOrderForm">
                            <div class="form-group">
                                <label for="newPurchaseOrderTitle" class="col-sm-2 control-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="newPurchaseOrderTitle" placeholder="Name your purchase order">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-2 control-label">Invoice #</label>
                                <div class="col-sm-6">
                                  <input class="form-control" id="purchaseOrderInvoiceNumber" placeholder="(Optional)">
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="purchaseOrderLocationSelect" class="col-sm-2 control-label">Location</label>
                                <div class="btn-group">
                                    <select id="purchaseOrderLocationSelect" class="form-control col-sm-10">
                                    <!-- Need to input dynamic location functionality -->
                                        @foreach($existingLocations as $existingLocation)
                                            <option>{{ $existingLocation['locationCity'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="purchaseOrderVendorSelect" class="col-sm-2 control-label">Vendor</label>
                                <div class="btn-group">
                                    <select id="purchaseOrderVendorSelect" class="form-control col-sm-10">
                                    <!-- Need to input dynamic location functionality -->
                                          <option>Valken</option>
                                          <option>Elite Force</option>
                                          <option>GI Sports</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="purchaseOrderStatusSelect" class="col-sm-2 control-label">Status</label>
                                <div class="btn-group">    
                                    <select id="purchaseOrderStatusSelect" class="form-control col-sm-10">
                                      <option>Pending</option>
                                      <option>Confirmed</option>
                                      <option>Recieved</option>
                                      <option>Completed</option>
                                      <option>Closed</option>
                                    </select>
                                </div>       
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary packYakPOButton"><span class='packYakPOCreateButtonLabel'>Create</span></button>
                            </div>

                            {{ csrf_field() }}
                            
                        </form>
                        <!-- ___________________END OF MODAL FORM FOR CREATE/ EDIT PURCHASE ORDER______________________ -->
                    </div>
                </div>
            </div>
        </div><!--END MODAL-->

        <!--START CREATE ITEM MODAL-->
        <div class="modal fade" id="createItemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content createNewItemModalContent">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create New Item</h4>
                    </div>
                    <div class="modal-body">
                        <!-- This is where the content of the modal will go -->
                        <form class="form-horizontal newItemForm">
                            <div class="box-body">
                            <div class="form-group">
                                <label for="createNewItemCategory" class="col-sm-2 control-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="form-control select2 createNewItemCategory" style="width: 100%;">
                                    @foreach($categoryList as $individualCategory)
                                        <option>{{ $individualCategory['categoryName'] }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div class="form-group">
                                <label for="createNewItemName" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control createNewItemName" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group createNewItemVariationsWrapper">
                                <div class='singleVariationWrapper'>
                                    <label for="createNewItemVariation" class="col-sm-2 control-label">Variation</label>
                                    <div class="col-sm-10 input-group">   
                                        <div class="col-sm-7">
                                            <input type="email" class="form-control createNewItemVariation" placeholder="Regular">
                                        </div>
                                        <label for="createNewItemSku" class="col-sm-1 control-label">SKU</label>
                                        <div class="col-sm-4 createNewItemSKUWrapper">
                                            <input type="email" class="form-control createNewItemSku" placeholder="SKU">
                                        </div>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default addNewItemVariation" type="button"><i class = "fa fa-plus fa-2"></i></button>
                                        </span>
                                    </div>

                                    <label for="createNewItemInventoryLine" class="col-sm-2 col-sm-offset-1 control-label">Alert</label>
                                    <div class="col-sm-8 input-group createNewItemInventoryLine">   
                                        <!--<div class="col-sm-4">
                                            <input type="number" class="form-control createNewItemInventoryLevel" placeholder='Current' min="0", step='1'>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control createNewItemUnitCost" placeholder='Unit Cost' min=".00", step='.01'>
                                        </div>
                                        -->
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control createNewItemInventoryAlert" placeholder='Alert At' min="0", step='1'>
                                        </div>
                                    </div>

                                    <label for="createNewItemPriceCost" class="col-sm-2 col-sm-offset-1 control-label">Price</label>
                                    <div class="col-sm-8 input-group createNewItemPriceCost">   
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control createNewItemPrice" placeholder='Price' min=".00", step='.01'>
                                        </div>

                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="createNewItemLocationSelect" class="col-sm-2 control-label">Sold At</label>
                                <div class="btn-group">
                                    @foreach($existingLocations as $existingLocation)
                                        <input type="checkbox" aria-label="..." class = "createNewItemLocationSelect" checked>
                                        <span class='createNewItemLocationSelect' >{{ $existingLocation['locationCity'] }}</span>
                                    @endforeach
                                </div>
                            </div>  

<!--                             <div class="form-group col-sm-12">
                                <label for="createNewItemPOAddSelect" class="col-sm-3 control-label">Add Item to PO</label>
                                <div class="btn-group">
                                    <select id="createNewItemPOAddSelect" class="form-control col-sm-9">
                                    Need to input dynamic location functionality
                                        @foreach($existingPurchaseOrders as $purchaseOrder)
                                            <option>{{ $purchaseOrder['po_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                            {{ csrf_field() }}
                        </form>

                        <form class="form-horizontal newItemInventoryAndInfoForm hidden">
                          <div class="box-body">
                              <div class="form-group">
                                <div class="form-group newItemInventoryAndInfoWrapper">

                                </div>
                              </div>
                          </div>
                          {{ csrf_field() }}
                        </form>
                        <!-- ___________________END OF MODAL FORM FOR CREATE NEW ITEM______________________ -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary packyakCreateNewItemButton">Create</button>
                        <button type="button" class="btn btn-primary packyakNewItemInventoryAndInfoButton hidden">Finish</button>
                    </div>
                </div>
            </div>
        </div><!--END MODAL-->
    </div><!-- /.col -->
</div>


@endsection