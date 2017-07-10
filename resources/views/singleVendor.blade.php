@extends('adminLayout')

@section('pagespecificstyles')
    <!-- vendor page speciic styles-->

@stop

@section('content')
<div class='row'>
	THis is where a particular vendor can show what items is has associated with it
	There also needs to be a button to edit the info and how to delete the vendor here
</div>
@endsection

@section('pagespecificscripts')
    <!-- purchaseOrder page speciic styles-->
    <script src="{{ asset ("/js/vendorCenter.js") }}" type="text/javascript"></script>
@stop