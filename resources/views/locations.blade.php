@extends('admin_template')

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

@endsection