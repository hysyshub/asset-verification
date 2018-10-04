<?php 
session_start();
if($_SESSION['user']=='')
{
    header('Location: login.php');
}
else
{
    error_reporting(0);
    date_default_timezone_set('Asia/Calcutta');

?>
<html>
<head>
<title>Edit Vendor</title>

</head>
<body>
<?php

include 'header.php';
include 'php/config.php';

$conn = pg_connect($conn_string);

if(!$conn)
{
    echo "ERROR : Unable to open database";
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
                                <a href="vendorinfo.php" style="color:blue;text-align:right;" class="nav-link">Vendors</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-3">
            </div>
            <div  class="col-md-6">
        <h3>Edit Vendor</h3>
            <?php
				$vendorinfoid = $_GET['vendorinfoid'];
				$query = "SELECT * FROM vendorinfo WHERE vendorinfoid=$vendorinfoid";
				$result = pg_query($conn, $query);
				
				if (!$result)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}	
				if(!pg_num_rows($result)>0)
				{
					echo "No record found : " ;
					exit;
				}
				$row = pg_fetch_array($result);

			?>
            <form>
                 <input type='hidden' class='edit_vendorinfoid' value="<?php echo $vendorinfoid;?>">
		        <div class="form-group">
		            Vendor Name: <input type="text" class="form-control form-control-sm edit_vendorname" name="edit_vendorname" value="<?php echo $row['vendorname'];?>" >   
		        </div>
		        
		        <div class="form-group status">
		                                
		        </div>
		        <div class="alert alert-success edit_success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
				    <h5>Success</h5>
				    <div>Vendor info updated successfully!</div>
				</div>

				<div>
		            <button type="button" class="btn btn-sm btn-info update_submit">Update</button>
		        </div>
            </form>
            </div>
            <div  class="col-md-3">
            </div>
        </div>
    </div>
        

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
	
	// update button click
	$('.update_submit').click(function(){               // update_submit click
		event.preventDefault();
		var edit_vendorinfoid = $('.edit_vendorinfoid').val();
		var edit_vendorname = $('.edit_vendorname').val();
		var task = 'update_vendor_info';

		if(edit_vendorname=='' || edit_vendorname==null)
		{
			$('.edit_status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter vendor name.</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'edit_vendorinfoid='+edit_vendorinfoid+'&edit_vendorname='+edit_vendorname+'&task='+task,
				success : function(res)
				{
					if(res == 'success')
					{
						$('.edit_success_status').show();
						window.setTimeout(function () {
						    $(".edit_success_status").fadeTo(500, 0).slideUp(500, function () {
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
