@extends('adminLayout')

@section('pagespecificstyles')
    <!-- vendor page speciic styles-->

    <!--Head specific scripts-->
    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyQgS9XSBC5xfHxrHtpY_Zwec2COylYzs&libraries=places"></script>-->

@stop

@section('content')
<div class='row'>
		
		<div class="col-md-2 col-md-offset-10">
			<button type="button" class="btn btn-block btn-primary btn-flat createVendorButton" data-toggle="modal" data-target="#createVendorModal">Create Vendor</button>
		</div>

        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Vendors</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <?php //var_dump($places); ?>
                <div class="box-body">
                    <table id="vendorsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Ext.</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                            </tr>
                        </thead>
                        
                        <tbody>

                        @foreach($vendors as $vendor)

                          <tr data-vendor="{{ $vendor['id'] or 'Error' }}" class="vendorRow">
                            <td class="companyName"><a href="/vendors/{{$vendor['id']}}">{{ $vendor['company_name'] or 'None' }}</a></td>
                            <td class="phoneNumber">{{ $vendor['phone_number'] or 'None' }}</td>
                            <td class="phoneExtension">{{ $vendor['phone_extension'] or 'None' }}</td>
                            <td class="email">{{ $vendor['email'] or 'None' }}</td>
                            <td class="address">{{ $vendor['address'] or 'None' }}</td>
                            <td class="city">{{ $vendor['city'] or 'None' }}</td>
                            <td class="state">{{ $vendor['state'] or 'None' }}</td> 
                            <td class="zip">{{ $vendor['zip'] or 'None' }}</td> 
                          </tr>
                        
                        @endforeach
                        </tbody>

                        <tfoot>
                        	<tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Ext.</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                            </tr>
                        </tfoot>
                        
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        </div><!-- /.col --> 

</div>

<div class="modal modal-primary fade" id="createVendorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">New Vendor</h4>
        </div>
        <div class="modal-body">

        <div id="form-errors"></div>

          <!-- form start -->
            <form role="form">
              <div class="box-body">
        
        		    <!-- Company Name -->
              	<div class="form-group">
                  <label for="createVendorCompanyName">Company Name*</label>
                  <input type="email" class="form-control" id="createVendorCompanyName" placeholder="Enter company name">
                </div>

                
                <!-- Contact Name -->
              	<div class="form-group">
                  <label for="createVendorContactName">Contact Name*</label>
                  <input type="email" class="form-control" id="createVendorContactName" placeholder="Enter contact name">
                </div>

                <div class="form-group">
                  <label>Contact Phone Number*:</label>

                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-phone"></i>
                    </div>
                    <input type="text" class="form-control" data-inputmask='"mask": "(999) 999-9999 [ext. 99999]"' data-mask placeholder='Enter phone number' id="createVendorPhoneNumber">
                  </div>
                  <!-- /.input group -->
                </div>
                <!-- /.form group -->

                <!-- Contact Email -->
                <div class="form-group">
                  <label for="createVendorContactEmail">Contact Email*</label>
                  <input type="email" class="form-control" id="createVendorContactEmail" placeholder="Enter contact email">
                </div>

                <!-- Address Name -->
              	<div class="form-group">
                  <label for="createVendorAddressName">Address</label>
                  <input type="text" class="form-control" id="createVendorAddressName" placeholder="Enter address">
                 </div>
                 <!-- Address Name -->
              	<div class="form-group">
                  <label for="createVendorCityName">City</label>
                  <input type="email" class="form-control" id="createVendorCityName" placeholder="Enter City">
                </div>
                  <!-- Address Name -->
              	<div class="form-group">
                  <label for="createVendorStateName">State</label>
                  <input type="email" class="form-control" id="createVendorStateName" placeholder="Enter State">
                </div>
                  <!-- Address Name -->
              	<div class="form-group">
                   <label for="createVendorZipName">Zip</label>
                  <input type="email" class="form-control" id="createVendorZipName" placeholder="Enter Zip">
                </div>
              </div>
              <!-- /.box-body -->
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-outline createVendorModalButton">Create Vendor</button>
        </div>
      </div>
  </div>
</div>

@endsection

@section('pagespecificscripts')
    <!-- vendor page speciic styles-->
    <script src="{{ asset ("/js/vendorCenter.js") }}" type="text/javascript"></script>
@stop
