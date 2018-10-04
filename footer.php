        </div>
        <div class="col-md-12 navbar-fixed-bottom" style="background-color:#030dcf; color:white; width:100%; height: 39px;">
            <p class="nav navbar-nav" style="padding-top: 10px;"><center> Copyright &copy; <?php echo date('Y'); ?> Asset Verification</center></p>
        </div>
 
    
    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>    
    
    <!-- jQuery CDN -->
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
     

    <!-- Bootstrap text editor -->
    <script src="js/jquery.richtext.js"></script>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- for export buttons in datatables-->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <!-- endof export datatable-->
    <script src="js/bootstrap-toggle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
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
