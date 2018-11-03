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
<title>Edit App User</title>

</head>
<body>
<?php

include 'header.php';

$query = "SELECT * FROM circleinfo ORDER BY circleinfoid";
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
                                <a href="userinfo.php" style="color:blue;text-align:right;" class="nav-link">App Users</a>
                            </li>
                            <li class="nav-item">
                                <a href="admininfo.php" style="color:blue;text-align:right;" class="nav-link">Admins</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
			<?php
			$userid = quote_smart($_GET['userid']);

			if (!is_numeric($userid))
			{
				echo "ERROR : Invalid parameter value";
				exit;
			}

			$query = "SELECT U.*,C.circlevalue,V.vendorname from userinfo as U JOIN circleinfo as C ON U.circleinfoid=C.circleinfoid JOIN vendorinfo as V ON U.vendorinfoid=V.vendorinfoid WHERE U.userid='$userid'";
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

			$query2 = "SELECT COUNT(jobinfoid) as count FROM jobinfo WHERE userid='$userid' AND status='1'";
			$result2 = pg_query($conn, $query2);
			
			if (!$result2)
			{
				echo "ERROR : " . pg_last_error($conn);
				exit;
			}	
			if(!pg_num_rows($result2)>0)
			{
				echo "No record found : " ;
				exit;
			}
			$row2 = pg_fetch_array($result2);
			
		?>
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
		<h3>Edit App User</h3>
		   		<form>
			    	<input type='hidden' class='userid' value="<?php echo $userid;?>">
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
			        <?php
			        	if($row2['count']>0)
			        	{
			        		echo "<div class='form-group'>
			            		Circle: <select class='form-control form-control-sm circleinfoid' disabled>
			            			<option selected value='".$row['circleinfoid']."'>".$row['circlevalue']."'</option>";
			            		echo "</select>";
			            	echo "</div>";

			            	echo "<div class='form-group'>
			            		Vendor info: <select class='form-control form-control-sm vendorinfoid' disabled>
			            			<option selected value='".$row['vendorinfoid']."'>".$row['vendorname']."'</option>";
			            		echo "</select>";
			            	echo "</div>";
			        	}
			        	else
			        	{
			        ?>
			        <div class="form-group">
			            Circle: <select class="form-control form-control-sm circleinfoid">
			            <option selected value="<?php echo $row['circleinfoid'];?>"><?php echo $row['circlevalue'];?></option>
			            <option value='0'>-- Select Circle --</option>
			            <?php
			            	$sql_circle_info = "SELECT * FROM circleinfo ORDER BY circleinfoid";
			            	$circle_info_result = pg_query($conn, $sql_circle_info);
			            	if(pg_num_rows($circle_info_result)>0)
			            	{
			            		while($row_circle_info = pg_fetch_array($circle_info_result))
			            		{
			            			echo "<option value='".$row_circle_info['circleinfoid']."'>".$row_circle_info['circlevalue']."</option>";
			            		}
			            		
			            	}
			            ?>
			            </select>   
			        </div>
			        
			        <div class="form-group">
			            Vendor info: <select class="form-control form-control-sm vendorinfoid">
			            <option selected value="<?php echo $row['vendorinfoid'];?>"><?php echo $row['vendorname'];?></option>
			            <option value='0'>-- Select user --</option>
			            <?php
			            	$sql_vendor_info = "SELECT * FROM vendorinfo ORDER BY vendorinfoid";
			            	$vendor_info_result = pg_query($conn, $sql_vendor_info);
			            	if(pg_num_rows($vendor_info_result)>0)
			            	{
			            		while($row_vendor_info = pg_fetch_array($vendor_info_result))
			            		{
			            			echo "<option value='".$row_vendor_info['vendorinfoid']."'>".$row_vendor_info['vendorname']."</option>";
			            		}
			            		
			            	}
			            ?>
			            </select>   
			        </div>
			        <?php
			        	}
			        ?>
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>User info updated successfully!</div>
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
		
<?php include 'footer.php';} pg_close($conn);?>


<script>
$(document).ready(function(){
	
	// update button click
	$('.update_submit').click(function(){               // update_submit click
		event.preventDefault();
		var userid = $('.userid').val();
		var firstname = $('.firstname').val();
		var lastname = $('.lastname').val();
		var emailid = $('.emailid').val();
		var address = $('.address').val();
		var contactnumber = $('.contactnumber').val();
		var circleinfoid = $('.circleinfoid').val();
		var vendorinfoid = $('.vendorinfoid').val();
		var task = 'update_user_info';
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
				data : 'userid='+userid+'&firstname='+firstname+'&lastname='+lastname+'&address='+address+'&contactnumber='+contactnumber+'&emailid='+emailid+'&circleinfoid='+circleinfoid+'&vendorinfoid='+vendorinfoid+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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
