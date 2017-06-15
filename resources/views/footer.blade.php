<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        Dashboard
    </div>
    <!-- Default to the left -->
    <strong>Copyright © 2015 <a href="#">American Paintball Coliseum</a>.</strong> All rights reserved.


    <!--common scripts-->
    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>

    <!-- MakePDF -->
    <script src="{{ asset ("/bower_components/pdfmake/build/pdfmake.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/pdfmake/build/vfs_fonts.js") }}" type="text/javascript"></script>

    <!--select 2 JS 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js" type='text/javascript'></script>-->

    <script src="{{ asset ("/js/packyakJS.js") }}" type="text/javascript"></script>
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
    <!-- DataTables -->
    <script src="{{ asset ('/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset ('/bower_components/admin-lte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <!-- InputMask -->
    <script src="{{ asset ('/bower_components/admin-lte/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset ('/bower_components/admin-lte/plugins/input-mask/jquery.inputmask.phone.extensions.js') }}"></script>

    <!-- page specific scripts -->
	@yield('pagespecificscripts')
</footer>