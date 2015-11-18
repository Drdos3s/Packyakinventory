@extends('admin_template')

@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Randomly Generated Tasks</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">

                    @foreach($itemDescription as $item)
                        
                        <?php //var_dump($item); //loops through each item and variation
                            if($item['variations'][0]['track_inventory'] == true){
                                for($i=0; $i < count($item['variations']); $i++){


                        ?>
                            
                            <h5>{{ $item['name'] }} <?php echo ' - '; ?> {{ $item['variations'][$i]['name'] }}  
                            <?php echo ' - '; 
                                for($j=0; $j < count($inventoryLevel); $j++){
                                    if($item['variations'][$i]['id'] == $inventoryLevel[$j]['variation_id']){
                            ?>

                            {{ $inventoryLevel[$j]['quantity_on_hand'] }}</h5>

                            <?php
                                    }
                                
                                };
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