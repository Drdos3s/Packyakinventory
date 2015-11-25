@extends('admin_template')

@section('content')


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->

            @foreach($data as $location)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $location['location']['business_address']['locality']?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                

                
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Item Name</th>
                                <th>Variation</th>
                                <th>Inventory</th>
                                <th>Price</th>
                                <th>SKU</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($location['itemDescription'] as $item)
                        <?php //var_dump($item); //loops through each item and variation
                            if($item['variations'][0]['track_inventory'] == true){
                                for($i=0; $i < count($item['variations']); $i++){
                                    if(array_key_exists('category' , $item)){
                        ?>   
                                <tr>
                                    <th class='packyakCategory' scope="row"><?php echo $item['category']['name'];?></th>
                        <?php
                                    }else{
                        ?>
                                        <th class='packyakCategory' scope="row"><?php echo 'No Category';?></th>
                        <?php                 
                                    }            
                        ?>
                                    <td class='packyakItemName'> {{ $item['name'] }} </td>
                                    <td class='packyakVarName'> {{ $item['variations'][$i]['name'] }} </td>

                        <?php 
                                    for($j=0; $j < count($location['inventoryLevel']); $j++){
                                        if($item['variations'][$i]['id'] == $location['inventoryLevel'][$j]['variation_id']){
                        ?>
                                            <td class='packyakInventory'> {{ $location['inventoryLevel'][$j]['quantity_on_hand'] }} </td>
                                            <td class='packyakInventoryText hidden'><?php echo Form::text('newInventoryLevel'); ?></td>
                        <?php
                                        }
                                
                                    }
                                    if(array_key_exists('price_money', $item['variations'][$i])){
                        ?>
                                        <td class='packyakPrice'><?php echo '$'.number_format($item['variations'][$i]['price_money']['amount']/100, 2, '.', ' '); ?> </td>
                        <?php
                                    }
                                    if(array_key_exists('sku' , $item['variations'][$i])){
                        ?>       
                                        <td class='packyakSKU'>{{ $item['variations'][$i]['sku'] }}</td>
                                       
                        <?php
                                    }else{
                        ?>
                                        <td class='packyakSKU' scope="row"><?php echo 'No SKU';?></td>
                        <?php
                                    }
                        ?>
                                        <td class='packyakSubmit'><?php echo Form::button('Submit'); ?></td>
                                        <td class='packyakCancel'><?php echo Form::button('Cancel'); ?></td>
                        <?php
                                }
                            };
                        ?>  
                            </tr>

                            @endforeach
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
                <div class="box-footer">

                </div><!-- /.box-footer-->
                @endforeach
            </div><!-- /.box -->
        </div><!-- /.col -->

@endsection