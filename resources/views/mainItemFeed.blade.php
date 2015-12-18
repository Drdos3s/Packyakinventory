@extends('admin_template')

@section('content')


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->

            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo 'Main Item Feed'?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <div class="box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Item Name</th>
                                <th>Variation</th>
                                <th>Inventory</th>
                                <th>Price</th>
                                <th>SKU</th>
                            </tr>
                        </thead>
                        @foreach($items as $row)
                        <tbody>
                            <td>{{ $row['locationSoldAt'] }}</td>
                            <td>{{ $row['itemCategoryName'] }}</td>
                            <td>{{ $row['itemName'] }}</td>
                            <td>{{ $row['itemVariationName'] }}</td>
                            <td>{{ $row['itemVariationInventory'] }}</td>
                            <td><?php echo '$'.number_format($row['itemVariationPrice']/100, 2, '.', ' '); ?></td>
                            <td>{{ $row['itemVariationSKU'] }}</td> 
                        </tbody>
                        @endforeach
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        </div><!-- /.col -->

@endsection