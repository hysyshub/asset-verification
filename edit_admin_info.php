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
<title>Edit Admin</title>

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
                                <a href="userinfo.php" style="color:blue;text-align:right;" class="nav-link">App Users</a>
                            </li>
                            <li class="nav-item">
                                <a href="admininfo.php" style="color:blue;text-align:right;" class="nav-link">Admins</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-3">
            </div>
            <div  class="col-md-6">
        <h3>Edit Admin</h3>
            <?php
				$admininfoid = $_GET['admininfoid'];
				$query = "SELECT * from admininfo WHERE admininfoid='$admininfoid'";
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
				$row = pg_fetch_array($result)
			?>
            <form>
                <input type='hidden' class='admininfoid' value="<?php echo $admininfoid;?>">
		        <div class="form-group">
		            First Name: <input type="text" class="form-control form-control-sm firstname" name="firstname" value="<?php echo $row['firstname'];?>" >   
		        </div>
		        <div class="form-group">
		            Last Name : <input type="text" class="form-control form-control-sm lastname" name="lastname" value="<?php echo $row['lastname'];?>" >   
		        </div>

			        <div class="form-group">
			            Email Id : <input type="text" class="form-control form-control-sm emailid" name="emailid" value="<?php echo $row['emailid'];?>" >   
			        </div>
			        <div class="form-group">
			            Address : <input type="text" class="form-control form-control-sm address" name="address" value="<?php echo $row['address'];?>" >   
			        </div>
			        <div class="form-group">
			            Contact number : <input type="text" class="form-control form-control-sm contactnumber" name="contactnumber" value="<?php echo $row['contactnumber'];?>" >   
			        </div>
			        
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>Admin info updated successfully!</div>
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
		var admininfoid = $('.admininfoid').val();
		var firstname = $('.firstname').val();
		var lastname = $('.lastname').val();
		var emailid = $('.emailid').val();
		var address = $('.address').val();
		var contactnumber = $('.contactnumber').val();
		var task = 'update_admin_info';
		if(firstname=='' || firstname==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter first name.</div>");
			return false;
		}
		else
		if(lastname=='' || lastname==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter last name.</div>");
			return false;
		}
		else
		if(emailid=='' || emailid==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter email address.</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'admininfoid='+admininfoid+'&firstname='+firstname+'&lastname='+lastname+'&address='+address+'&contactnumber='+contactnumber+'&emailid='+emailid+'&task='+task,
				success : function(res)
				{
					if(res == 'success')
					{
						$('.success_status').show();
					}
					else
					{
						$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
						//$('.status').html("<div class='alert alert-danger'><strong>Query Failed!</div> Something went wrong.");
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
