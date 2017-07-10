@extends('main')

@section('fullPage')
<body class="skin-green sidebar-collapse sidebar-mini">
        <div class="wrapper">

            <!-- Header Bar  THESE NEED TO BE INCLUDED ON ADMIN MAGES OR WITHIN ANOTHER TEMPLATE THAT EXTENDS THIS ONE-->
            @include('headerBar')

            <!-- Sidebar -->
            @include('sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <!-- You can dynamically generate breadcrumbs here -->
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Here</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    @yield('content')
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->



        </div><!-- ./wrapper -->
              
</body>
@endsection
