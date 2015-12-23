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
                                <th>Submit</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $row)
                            <tr class='packYakItemFeedRow'>
                                <td>{{ $row['locationSoldAt'] }}</td>
                                <td>{{ $row['itemCategoryName'] }}</td>
                                <td>{{ $row['itemName'] }}</td>
                                <td>{{ $row['itemVariationName'] }}</td>
                                <td class='packyakInventory'>{{ $row['itemVariationInventory'] }}</td>
                                <td class='packyakInventoryText hidden'><?php echo Form::text('newInventoryLevel'); ?></td>
                                <td><?php echo '$'.number_format($row['itemVariationPrice']/100, 2, '.', ' '); ?></td>
                                <td>{{ $row['itemVariationSKU'] }}</td>
                                <td class='packyakSubmitButton'><?php echo Form::button('Submit'); ?></td>
                                <td class='packyakCancel'><?php echo Form::button('Cancel'); ?></td>
                                {{ csrf_field() }}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        </div><!-- /.col -->

@endsection