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
<title>Vendors</title>

</head>
<body>
<?php

include 'header.php';

$query = "SELECT * FROM vendorinfo ORDER BY vendorinfoid";
$result = pg_query($conn, $query);

if (!$result)
{
	echo "ERROR : " . pg_last_error($conn);
	exit;
}

?>
<!-- Page Content start -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse pull-right" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        	<li class="nav-item">
                                <a href="#add_new_vendor_info" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Add New Vendor</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-vendor-info.php" style="color:blue;text-align:right;" class="nav-link">Import Vendors</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<h3>Vendors</h3>
			<table id='tieuptable' class='table-hover table-striped table-bordered vendor_list' style="width:100%">
				<thead>
					<tr>
					    <th>Id</th>
					    <th>Vendor Name</th>
					    <th>Edit</th>
					</tr>
				</thead>

			</table>
		</div>
	</div>
		
<!-- Modal New vendor Addition -->
<div class="modal fade" id="add_new_vendor_info" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content  col-md-12">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            
            <!-- Modal Add new circle -->
            <div class="modal-body">
                <h3 class="text-center">Add New Vendor</h3>
                <form>
			        <div class="form-group">
			            Vendor Name: <input type="text" class="form-control form-control-sm vendorname" name="vendorname" placeholder="Vendor Name:" id="vendorname" >        
			        </div>
			         
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>New vendor info added successfully!</div>
					</div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-info btn_submit">Add</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){

	$('.vendor_list').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :"vendorinfo_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".vendor_list_processing").css("display","none");
            }
        }
    });

	// add button click
	$('.btn_submit').click(function(){                        //btn_submit click
		event.preventDefault();
		var vendorname = $('.vendorname').val();
		var task = 'add_vendor_info';
		if(vendorname=='' || vendorname==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter vendor name.</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'addition_helper.php',
				data : 'vendorname='+vendorname+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
				success : function(res)
				{
					if(res == 'duplicate')
					{
						$('.status').html("<div class='alert alert-danger'><strong>Duplicate record!</strong> Vendor info already exists.</div>");
						return false;
					}
					else
					if(res == 'success')
					{
						$('.success_status').show();
						window.setTimeout(function () {
						    $(".success_status").fadeTo(500, 0).slideUp(500, function () {
						        $(this).remove();
						    window.location.reload();    
						    });
						}, 5000);

					}
					else
					{
						$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
						return false;
					}
				}
			});
		}
	});
});
</script>
</body>
</html>
