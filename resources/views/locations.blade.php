@extends('adminLayout')

@section('pagespecificstyles')
    <!-- vendor page speciic styles-->

@stop

@section('content')

<div class='row'>
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
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                            </tr>
                        </thead>
                        @foreach($places as $place)
                        <tbody>
                            <td>{{ $place['businessName'] }}</td>
                            <td>{{ $place['locationPhone'] }}</td>
                            <td>{{ $place['businessEmail'] }}</td>
                            <td>{{ $place['locationAddressLine1'] }}</td>
                            <td>{{ $place['locationCity'] }}</td>
                            <td>{{ $place['locationState'] }}</td>
                            <td>{{ $place['locationZip'] }}</td> 
                        </tbody>
                        @endforeach
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        </div><!-- /.col --> 

</div>


<!--This is eventurally going to the the items table-->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="box-group">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <div class="row">
                                <!--header row for item info-->
                                <ul class="col-sm-12 headerRow">
                                    <li class="col-sm-3 headerInfo">
                                        <h4 class="mainItemTitleInfo">
                                          <a data-toggle="collapse" href="#collapseOne">
                                            <strong>LOCATION:</strong> Denver
                                          </a>
                                        </h4>
                                    </li>
                                    <li class="col-sm-3 headerInfo">
                                        <h4 class="mainItemTitleInfo">
                                          <a data-toggle="collapse" href="#collapseOne">
                                            <strong>NAME:</strong> Otis Spunkmeyer Muffins
                                          </a>
                                        </h4>
                                    </li>
                                    <li class="col-sm-3 headerInfo">
                                        <h4 class="mainItemTitleInfo">
                                          <a data-toggle="collapse" href="#collapseOne">
                                            <strong>VARIATION:</strong> Blueberry
                                          </a>
                                        </h4>
                                    </li>
                                    <li class="col-sm-3 headerInfo">
                                        <ul class="itemOptionButtons pull-right">
                                            <li class="packyakPurchaseOrderMenuButton optionButton">
                                                <div class="dropdown">
                                                    <i class="fa fa-chevron-circle-down fa-2 btn btn-default dropdown-toggle packyakPurchaseOrderList" type="button" id="packyakPurchaseOrderList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                                                    <ul class="dropdown-menu dropdown-menu-right packyakAddItemToPOWrapper" aria-labelledby="packyakPurchaseOrderList">
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="packyakSubmitButton optionButton">
                                                <i class="fa fa-check-circle-o fa-2 btn btn-default"></i>
                                            </li>
                                            <li class="packyakCancel optionButton">
                                                <i class="fa fa-times-circle fa-2 btn btn-default"></i>
                                            </li>
                                            <li class="packyakCancel optionButton">
                                                <i class="fa fa-trash-o fa-2 btn btn-default"></i>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <!--item details-->
                            <div class="row">
                                <div class="col-sm-3">
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Category:</strong> Chips & Food                       
                                    </h5>
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Item SKU:</strong> 6734543335434K                      
                                    </h5>
                                </div>
                                <div class="col-sm-3">
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Current Inventory:</strong> 67                      
                                    </h5>
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Margin:</strong> $10.00                     
                                    </h5>
                                </div>
                                <div class="col-sm-6">
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Price:</strong> $22.00                      
                                    </h5>
                                    <h5 class="mainItemTitleInfo">
                                        <strong>Cost:</strong> $12.00                    
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                                <div class="box-body">
                                  Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3
                                  wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum
                                  eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla
                                  assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                                  nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                  farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus
                                  labore sustainable VHS.
                                </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection