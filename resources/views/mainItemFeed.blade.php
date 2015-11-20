@extends('admin_template')

@section('content')

    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Denver</h3>
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
                                <th>SKU</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                    @foreach($itemDescription as $item)  
                        <?php //var_dump($item); //loops through each item and variation
                            if($item['variations'][0]['track_inventory'] == true){
                                for($i=0; $i < count($item['variations']); $i++){
                                    if(array_key_exists('category' , $item)){
                        ?>   
                                <th scope="row"><?php echo $item['category']['name'];?></th>
                        <?php
                                    }//else{
                                    //     echo 'NO CATEGORY >>> ';
                                    // }
                        ?>
                                <td> {{ $item['name'] }} </td>
                                <td> {{ $item['variations'][$i]['name'] }} </td>
                        <?php 
                            for($j=0; $j < count($inventoryLevel); $j++){
                                if($item['variations'][$i]['id'] == $inventoryLevel[$j]['variation_id']){
                        ?>
                                <td> {{ $inventoryLevel[$j]['quantity_on_hand'] }} </td>
                        <?php
                                }
                                
                            }
                                if(array_key_exists('sku' , $item['variations'][$i])){
                        ?>       
                                    <td>{{ $item['variations'][$i]['sku'] }}</td>
                        <?php
                                    }
                                }
                            }
                        ?>
                        @endforeach
                            </tr>
                        </tbody>
                    </table>








                    @foreach($itemDescription as $item)  
                        <?php //var_dump($item); //loops through each item and variation
                            if($item['variations'][0]['track_inventory'] == true){
                                for($i=0; $i < count($item['variations']); $i++){
                                    if(array_key_exists('category' , $item)){
                        ?>
                                    <?php echo $item['category']['name'];?>
                                    }else{
                                        echo 'NO CATEGORY >>> ';
                                    }
                        ?>
                            
                            {{ $item['name'] }} <?php echo ' >>> '; ?> {{ $item['variations'][$i]['name'] }}
                        <?php 
                            echo ' >>>  '; 
                            for($j=0; $j < count($inventoryLevel); $j++){
                                if($item['variations'][$i]['id'] == $inventoryLevel[$j]['variation_id']){
                        ?>

                            {{ $inventoryLevel[$j]['quantity_on_hand'] }}

                        <?php
                                }
                                
                            }
                            if(array_key_exists('sku' , $item['variations'][$i])){
                                echo ' >>> '.$item['variations'][$i]['sku'];
                            }else{
                                echo ' >>> NO SKU';
                            }
                        ?>
                            <div class="progress progress-xxs">
                                <div class="progress-bar"></div>
                            </div>
                        
                        <?php    
                                }
                            };
                        ?>
                    @endforeach

                </div><!-- /.box-body -->
                <div class="box-footer">
                    <form action='#'>
                        <input type='text' placeholder='New task' class='form-control input-sm' />
                    </form>
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
        
@endsection