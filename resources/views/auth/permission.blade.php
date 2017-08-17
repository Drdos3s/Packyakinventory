@extends('main')

@section('pagespecificstyles')
    

@stop

@section('fullPage')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
				
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!--setting up OAuth login after registering new-->
                    <?php 
                        $scope = rawurlencode('MERCHANT_PROFILE_READ ITEMS_READ ITEMS_WRITE');
                        $url = 'https://connect.squareup.com/oauth2/authorize?client_id='.$appID.'&scope='.$scope;
                        
                    ?>
                    <a href="<?php echo $url; ?>">Click here</a> to authorize the application.";


                </div>
            </div>
        </div>
    </div>
</div>

@endsection