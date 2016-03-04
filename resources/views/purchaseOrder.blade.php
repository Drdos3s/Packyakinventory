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

        
    <div class='col-md-12'>
        @foreach($existingPurchaseOrders as $purchaseOrder)
            <div class="box box-primary packyakPOHeader">
                <div class="box-header with-border ">

                    <div class="col-sm-3"><h4><strong>Name:  </strong></h4><h5 class="packYakPOName"><?php echo ' '.$purchaseOrder['po_name'] ?></h5></div>
                    <div class="pypoid hidden"><?php echo $purchaseOrder['id'] ?></div>
                    <div class="col-sm-2"><h4><strong>Status: </strong></h4><h5 class="packYakPOStatus"><?php echo ' '.$purchaseOrder['po_status'] ?></h5></div>
                    <div class="col-sm-2"><h4><strong>Vendor: </strong></h4><h5 class="packYakPOVendor"><?php echo ' '.$purchaseOrder['po_vendor'] ?></h5></div>
                    <div class="col-sm-2"><h4><strong>Location: </strong></h4><h5 class="packYakPOLocation"><?php echo ' '.$purchaseOrder['po_location'] ?></h5></div>
                    <div class="col-sm-2"><h4><strong>Created: </strong></h4><h5 class="packYakPOCreated"><?php echo ' '.date('m-d-Y',strtotime($purchaseOrder['created_at']));?></h5></div>
                    <div class="box-tools pull-right">
                        <i class="fa fa-pencil packyackPOEdit"></i>
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <?php //var_dump($places); ?>
                <div class="box-body">
                    <table class="table">
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
                                <td><h5>{{ $item['itemVariationUnitCost'] }}</h5></td>
                                <td><h5>0</h5></td>
                                <td><i class="fa fa-times-circle fa-2 btn btn-default packyakRemoveFromPO"></i></td>
                            </tr>
                            @endforeach
                            <?php //var_dump($purchaseOrder['po_items']); ?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                
                <div class="box-footer">
                    <button type="button" class="btn btn-primary btn-md packyakNewItemButton" data-toggle="modal" data-target="#createItemModal">
                        Create New Item
                    </button>
                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        @endforeach
        <?php //var_dump($existingLocations);?>
        <!-- Modal for create and edit PO-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">New Purchase Order</h4>
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
                                <button type="button" class="btn btn-primary packyakPurchaseOrderCreateButton">Create</button>
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
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create New Item</h4>
                    </div>
                    <div class="modal-body">
                        <!-- This is where the content of the modal will go -->
                        <form class="form-horizontal newItemForm">
                            <div class="form-group">
                                <label for="createNewItemCategory" class="col-sm-2 control-label">Category</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="createNewItemCategory" placeholder="Category">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="createNewItemName" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="createNewItemName" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group createNewItemVariationsWrapper">
                                <div class='singleVariationWrapper'>
                                    <label for="createNewItemVariation" class="col-sm-2 control-label">Variation</label>
                                    <div class="col-sm-10 input-group">   
                                        <div class="col-sm-7">
                                            <input type="email" class="form-control" class="createNewItemVariation" placeholder="Regular">
                                        </div>
                                        <label for="createNewItemSku" class="col-sm-1 control-label">SKU</label>
                                        <div class="col-sm-4">
                                            <input type="email" class="form-control" class="createNewItemSku" placeholder="SKU">
                                        </div>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default addNewItemVariation" type="button"><i class = "fa fa-plus fa-2"></i></button>
                                        </span>
                                    </div>

                                    <label for="createNewItemInventoryLine" class="col-sm-2 col-sm-offset-1 control-label">Inventory</label>
                                    <div class="col-sm-8 input-group createNewItemInventoryLine">   
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" class="createNewItemInventoryLevel" placeholder='Current' min="0", step='1'>
                                        </div>

                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" class="createNewItemInventoryAlert" placeholder='Alert At' min="0", step='1'>
                                        </div>
                                    </div>

                                    <label for="createNewItemPriceCost" class="col-sm-2 col-sm-offset-1 control-label">Price/Cost</label>
                                    <div class="col-sm-8 input-group createNewItemPriceCost">   
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" class="createNewItemPrice" placeholder='Price' min=".00", step='.01'>
                                        </div>

                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" class="createNewItemUnitCost" placeholder='Unit Cost' min=".00", step='.01'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="createNewItemLocationSelect" class="col-sm-2 control-label">Sold At</label>
                                <div class="btn-group">
                                    @foreach($existingLocations as $existingLocation)
                                        <input type="checkbox" aria-label="..." class = "createNewItemLocationSelect">
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
                        <!-- ___________________END OF MODAL FORM FOR CREATE NEW ITEM______________________ -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary packyakCreateNewItemButton">Create</button>
                    </div>
                </div>
            </div>
        </div><!--END MODAL-->
    </div><!-- /.col -->
</div>


@endsection