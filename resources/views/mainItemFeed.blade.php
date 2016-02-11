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
                                <th>Unit Cost</th>
                                <th>Margin</th>
                                <th>SKU</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php //var_dump($dashboardDataFinal); ?>
                            @foreach($dashboardDataFinal['items'] as $row)
                            <tr class='packYakItemFeedRow'>
                                <td class='packyakLocationSold'>{{ $row['locationSoldAt'] }}</td>
                                <td>{{ $row['itemCategoryName'] }}</td>
                                <td>{{ $row['itemName'] }}</td>
                                <td>{{ $row['itemVariationName'] }}</td>
                                
                                <td class='packyakInventory'>{{ $row['itemVariationInventory'] }}</td>
                                <td class='packyakInventoryText hidden'><?php echo Form::input('number','newInventoryLevel', $row['itemVariationInventory'], array('class' => 'packyakInventoryTextInput', 'type' => 'number', 'min' => '-5', 'step' => '1')); ?></td>
                                
                                <td><?php echo '$'.number_format($row['itemVariationPrice']/100, 2, '.', ' '); ?></td>
                                <td class='packyakUnitPrice'><?php echo '$'.number_format($row['itemVariationUnitCost']/100, 2, '.', ' '); ?></td>
                                <td class='packyakUnitPriceText hidden'>$<?php echo Form::input('number','currency', number_format($row['itemVariationUnitCost']/100, 2, '.', ' '), array('class' => 'packyakUnitPriceTextInput', 'min' => '.00', 'step' => '.01')); ?></td>
                                <td class='packyakProfitMargin'><?php echo '$'.number_format(($row['itemVariationPrice']-$row['itemVariationUnitCost'])/100, 2, '.', ' '); ?></td>
                                <td>{{ $row['itemVariationSKU'] }}</td>                                
                                <td class='packyakPurchaseOrderMenuButton'>
                                    <div class="dropdown">
                                        <i class="fa fa-chevron-circle-down fa-2 btn btn-default dropdown-toggle" type="button" id="packyakPurchaseOrderList" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="packyakPurchaseOrderList">
                                        @foreach($dashboardDataFinal['purchaseOrders'] as $pendingPO)
                                            <li>
                                                <a class="packyakPurchaseOrderListItem">{{ $pendingPO['po_name'] }}</a>
                                                <div class="packyakPurchaseOrderID hidden">{{ $pendingPO['id'] }}</div>
                                            </li>
                                        @endforeach
                                      </ul>
                                    </div>        
                                </td>
                                <td class='packyakSubmitButton'><i class="fa fa-check-circle-o fa-2 btn btn-default"></i></td>
                                <td class='packyakCancel'><i class="fa fa-times-circle fa-2 btn btn-default"></i></td>
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