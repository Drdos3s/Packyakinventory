@extends('master_template')

@section('body')
<body class="skin-green sidebar-collapse sidebar-mini">

    <div class="wrapper">

        <!-- Header -->
        @include('header')

        <!-- Sidebar -->
        @include('sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    {{ $page_title or "LOCATION ITEMS AND INVENTORY" }}
                    <small>{{ $page_description or null }}</small>
                </h1>
                <!-- You can dynamically generate breadcrumbs here -->
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Your Page Content Here -->
                @yield('content')
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Footer -->
        @include('footer')

    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>

    <!--select 2 JS 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js" type='text/javascript'></script>-->

    <script src="{{ asset ("/js/packyakJS") }}" type="text/javascript"></script>
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
<!-- DataTables -->
<script src="{{ asset ('/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset ('/bower_components/admin-lte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

<!-- InputMask -->
<script src="{{ asset ('/bower_components/admin-lte/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset ('/bower_components/admin-lte/plugins/input-mask/jquery.inputmask.phone.extensions.js') }}"></script>
          
    </body>
@endsection
