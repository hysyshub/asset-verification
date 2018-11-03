<?php 
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
	exit;
}
else
{
	//error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

?>
<html>
<head>
<title>Barcode Matching Log</title>

</head>
<body>
<?php

include 'header.php';

?>
<!-- Page Content  Start-->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        
                        </ul>
                    </div>
                </div>
            </nav> 

        <div  class="col-md-12">
            <h3>Barcode Matching Log</h3>
            <table  id='tieuptable' class='table-hover table-striped table-bordered barcode_matching_log_list' style="width:100%">
                <thead>
                    <tr>
                        <th>Matching Id</th>
                        <th>Site Code</th>
                        <th>Site Name</th>
                        <th>Job Id</th>
                        <th>Job Code</th>
                        <th>Barcode</th>
                        <th>Scan Time</th>
                    </tr>
                </thead>

            </table>
        </div>
	</div>
		
<?php include 'footer.php'; }?>
<script>
    $(document).ready(function(){

        $('.barcode_matching_log_list').DataTable({         // barcode_matching_log_list table  
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :"barcode_matching_log_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                    $(".barcode_matching_log_list_processing").css("display","none");
                }
            }
        });
    });
</script>
</body>
</html>
