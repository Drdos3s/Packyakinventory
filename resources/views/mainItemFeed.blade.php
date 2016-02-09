@extends('admin_template')

@section('content')


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->

            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo 'Last inventory update was at '.date('h:i A'); ?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <div class="box-body">
                    <table id='packyakInventoryDashTable' class="table">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Item Name</th>
                                <th>Variation</th>
                                <th>Inventory</th>
                                <th>Price</th>
                                <th>SKU</th>
                                <th>Unit Price</th>
                                <th>Submit</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php //var_dump($items); ?>
                            @foreach($items as $row)
                            <tr class='packYakItemFeedRow'>
                                <td class='packyakLocationSold'>{{ $row['locationSoldAt'] }}</td>
                                <td>{{ $row['itemCategoryName'] }}</td>
                                <td>{{ $row['itemName'] }}</td>
                                <td>{{ $row['itemVariationName'] }}</td>
                                
                                <td class='packyakInventory'>{{ $row['itemVariationInventory'] }}</td>
                                <td class='packyakInventoryText hidden'><?php echo Form::input('number','newInventoryLevel', $row['itemVariationInventory'], array('class' => 'packyakInventoryTextInput', 'type' => 'number', 'min' => '-5', 'pattern' => '[1-8][0-9]')); ?></td>
                                
                                <td><?php echo '$'.number_format($row['itemVariationPrice']/100, 2, '.', ' '); ?></td>
                                <td>{{ $row['itemVariationSKU'] }}</td>

                                <td class='packyakUnitPrice'>${{ $row['itemVariationUnitCost'] }}</td>
                                <td class='packyakUnitPriceText hidden'>$<input type="text" class="packyakUnitPriceTextInput" name="currency" pattern="^\d*(\.\d{2}$)?" value = {{ $row['itemVariationUnitCost'] }}></td>
                                
                                <td class='packyakSubmitButton'><?php echo Form::button('Submit'); ?></td>
                                <td class='packyakCancel'><?php echo Form::button('Cancel'); ?></td>
                                {{ csrf_field() }}
                                <td class='packyakInventoryItemID hidden'>{{ $row['itemVariationID'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
                
                <div class="box-footer">

                </div><!-- /.box-footer-->
                
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div>

@endsection