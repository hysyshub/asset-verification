        </div>
        <div class="col-md-12 navbar-fixed-bottom" style="background-color:#030dcf; color:white; width:100%; height: 39px;">
            <p class="nav navbar-nav" style="padding-top: 10px;"><center> Copyright &copy; <?php echo date('Y'); ?> Asset Verification</center></p>
        </div>
 
    
    <!-- jQuery -->
    <script src="js/jquery-3.3.1.min.js"></script>
     
    <!-- Popper.JS -->
    <script src="js/popper.js-1.14.4/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap-4.1.0/js/bootstrap.min.js"></script>

    <!-- Bootstrap text editor -->
    <script src="js/jquery.richtext.js"></script>

    <!-- datatables cdn-->
    <script src="js/DataTables-1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="js/DataTables-1.10.18/js/dataTables.bootstrap4.min.js"></script>

    <!-- for export buttons in datatables-->
    <script src="js/Buttons-1.5.4/js/dataTables.buttons.min.js"></script>
    <script src="js/Buttons-1.5.4/js/buttons.bootstrap4.min.js"></script>
    <script src="js/Buttons-1.5.4/js/buttons.colVis.min.js"></script>
    <script src="js/Buttons-1.5.4/js/buttons.flash.min.js"></script>
    <script src="js/Buttons-1.5.4/js/buttons.html5.min.js"></script>
    <script src="js/Buttons-1.5.4/js/buttons.print.min.js"></script>
    <script src="js/JSZip-3.1.5/jszip.min.js"></script>
    <script src="js/pdfmake-0.1.36/pdfmake.min.js"></script>
    <script src="js/pdfmake-0.1.36/vfs_fonts.js"></script>

    <!-- endof export datatable-->
    <script src="js/bootstrap-toggle.min.js"></script>
    <link rel="stylesheet" href="js/jquery.fancybox.min.css" />
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap-datepicker3.css"/>

    <script type="text/javascript">
        $(document).ready(function () {
            $("a.single_image").fancybox({'titlePosition' : 'over'});
            //$('.circle_list').DataTable();
            //$('.vendor_list').DataTable();
            //$('.location_list').DataTable();
            //$('.user_list').DataTable();
            $('.job_list1').DataTable();
            $('.notifications_list').DataTable();
            $('.contact_list').DataTable();
            //$('.user_event_log_list').DataTable();
            //$('.barcode_inventory_list').DataTable();
            //$('.barcode_matching_log_list').DataTable();
            //$('.items_pending_list').DataTable();
            //$('.admin_list').DataTable();
            //$('#example').DataTable();
            //$('.help_desk_list').DataTable();
            
            $('#sidebarCollapse').on('click', function () {
                 $('#sidebar').toggleClass('active');
            });

            $(function () {
                $('#userid').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: 'Search users...'
                }); 
                

              });

            var date_input=$('input[name="start_date"],input[name="end_date"]'); //our date input has the name "date"
            var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
              format: 'yyyy-mm-dd',
              container: container,
              todayHighlight: true,
              autoclose: true,
            })
        });
    </script>
</body>
</html>
