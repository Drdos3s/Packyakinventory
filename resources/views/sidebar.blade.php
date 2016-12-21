<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset("/bower_components/admin-lte/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Kyle</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">Menu</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ Request::is('dashboard')? 'active': '' }}"><a href="{{ url('/dashboard') }}"><i class="fa fa-sitemap" aria-hidden="true"></i><span>Items & Inventory</span></a></li>
            <li class="{{ Request::is('purchaseOrders')? 'active': '' }}"><a href="{{ url('/purchaseOrders') }}"><i class="fa fa-leaf" aria-hidden="true"></i></i><span>Purchase Orders</span></a></li>
            <li class="{{ Request::is('vendors')? 'active': '' }}"><a href="{{ url('/vendors') }}""><i class="fa fa-briefcase" aria-hidden="true"></i><span>Vendors</span></a></li>
            <li class="{{ Request::is('locations')? 'active': '' }}"><a href="{{ url('/locations') }}"><i class="fa fa-map-marker" aria-hidden="true"></i><span>Locations</span></a></li>
            
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>