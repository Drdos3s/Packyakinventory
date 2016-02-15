@extends('admin_template')

@section('content')

<!-- Button trigger modal -->


<div class='row'>

        <!-- Modal Button for creating a new purchase order -->
        <div class='col-md-12'>            
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#myModal">
                New Purchase Order
            </button>
        </div>

        
        <div class='col-md-12'>
        @foreach($existingPurchaseOrders as $purchaseOrder)
        <!-- Box -->
            <div class="box box-primary packyakPOHeader">
                <div class="box-header with-border ">

                    <div class="col-sm-3"><h3 class="box-title"><strong>Name:  </strong><?php echo $purchaseOrder['po_name'] ?></h3></div>
                    <div class="pypoid hidden"><?php echo $purchaseOrder['id'] ?></div>
                    <div class="col-sm-2"><h3 class="box-title"><strong>Status: </strong><?php echo $purchaseOrder['po_status'] ?></h3></div>
                    <div class="col-sm-2"><h3 class="box-title"><strong>Vendor: </strong><?php echo $purchaseOrder['po_vendor'] ?></h3></div>
                    <div class="col-sm-2"><h3 class="box-title"><strong>Location: </strong><?php echo $purchaseOrder['po_location'] ?></h3></div>
                    <div class="col-sm-2"><h3 class="box-title"><strong>Created: </strong><?php echo date('m-d-Y',strtotime($purchaseOrder['created_at']));?></h3></div>
                    <div class="box-tools pull-right">
                        <i class="fa fa-pencil"></i>
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

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
            @endforeach
<!-- Modal -->
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
                  <option>Denver</option>
                  <option>Phoenix</option>
                  <option>Brighton</option>
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

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <div class="checkbox">
            <label>
              <input type="checkbox"> Remember me
            </label>
          </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary packyakPurchaseOrderCreateButton">Create</button>
    </div>

    {{ csrf_field() }}
    
</form>

<!-- _________________________________________ -->



      </div>
    </div>
  </div>
</div>




        </div><!-- /.col -->
</div>


@endsection