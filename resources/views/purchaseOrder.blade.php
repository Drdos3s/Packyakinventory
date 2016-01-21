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
        <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo 'Locations'?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <?php //var_dump($places); ?>
                <div class="box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td> 
                        </tbody>
                        
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->




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
          <input class="form-control" id="inputPassword3" placeholder="(Optional)">
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
            <select id="purchaseOrderLocationSelect" class="form-control col-sm-10">
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