<?php 
session_start();

// Quote variable to make safe
function quote_smart($value)
{
    // Strip HTML & PHP tags & convert all applicable characters to HTML entities
    $value = trim(htmlentities(strip_tags($value)));    

    // Stripslashes
    if ( get_magic_quotes_gpc() )
    {
        $value = stripslashes( $value );
    }
    // Quote if not a number or a numeric string
    if ( !is_numeric( $value ) )
    {
         $value = pg_escape_string($value);
    }
    return $value;
}

if($_SESSION['user']=='' || $_SESSION['superadmin'] != 1)
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
<title>Edit Admin</title>

<style>
.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc !important;
}

.btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active {
    color: #333;
    background-color: #e6e6e6;
    border-color: #adadad;
}

.btn:active, .btn.active {
    background-image: none;
    outline: 0;
    -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
    box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
}

</style>

</head>
<body>
<?php

include 'header.php';

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
				$admininfoid = quote_smart($_GET['admininfoid']);

				if (!is_numeric($admininfoid))
				{
					echo "ERROR : Invalid parameter value";
					exit;
				}

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

			        <div class="form-group">
			        	Is Super Admin?
					<input type='checkbox' data-toggle='toggle' name='superadmin' class='superadmin' id='superadmin' <?php if ($row['superadmin'] == '1') echo "checked"; ?> data-on='On' data-off='Off' data-size="small">
		          	
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
		var superadmin = $(".superadmin").is(":checked");
		if(superadmin==true)
		{
			superadmin='1';
		}
		else
		{
			superadmin='0';
		}

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
				data : 'admininfoid='+admininfoid+'&firstname='+firstname+'&lastname='+lastname+'&address='+address+'&contactnumber='+contactnumber+'&emailid='+emailid+'&superadmin='+superadmin+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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
