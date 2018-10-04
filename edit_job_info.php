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
<title>Edit Job Info</title>

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

$query = "SELECT * FROM circleinfo ORDER BY circleinfoid";
$result = pg_query($conn, $query);

if (!$result)
{
	echo "ERROR : " . pg_last_error($conn);
	exit;
}

?>
<!-- Page Content start -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style='width:1100px'>
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#6a1b9a;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        	<li class="nav-item pull-left">
                                <a href="jobinfo.php" style="color:blue;">Job Info</a>
                            </li>&nbsp;&nbsp;| &nbsp;&nbsp;
                            <li class="nav-item pull-left">
                                <a href="visits.php"style="color:blue;">Job Data Fields</a>
                            </li>&nbsp;&nbsp;| &nbsp;&nbsp;
                            <li class="nav-item pull-left">
                                <a  href="jobdropdown.php" style="color:blue;">Job Dropdown Values</a>
                            </li>&nbsp;&nbsp;| &nbsp;&nbsp;
                            <li class=" nav-item dropdown style="background:white;"">
                            <a href="#" id="nbAcctDD" class="dropdown-toggle" data-toggle="dropdown"  style="background:transparent;color:black;">&nbsp;Hi&nbsp; <?php echo $_SESSION['user']; ?></a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="index.php"  class="user_event"><span class="glyphicon glyphicon-home"></span>&nbsp;Home</a>
                                </li>
                                <li>
                                    <a href="admin-self-change-password.php"  class="user_event"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Change Password</a>
                                </li>
                                <li>
                                    <a href="logout.php?logout" class="user_event"><span class="glyphicon glyphicon-off"></span>&nbsp;Sign Out</a>
                                </li>
                            </ul>
                        </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
		<h2>Edit Job Info</h2>
			
			<?php
		$jobinfoid = $_GET['jobinfoid'];
		$query = "SELECT J.jobinfoid, L.sitecode, L.locationid,L.sitename, J.jobno, J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.userid,U.firstname, J.starttime, J.endtime, J.createdon, C.circleinfoid, C.circlevalue,V.vendorinfoid,V.vendorname
	    	FROM jobinfo AS J 
			INNER JOIN location AS L ON J.locationid=L.locationid 
			LEFT JOIN userinfo AS U ON J.userid=U.userid
			LEFT JOIN circleinfo AS C ON J.circleinfoid=C.circleinfoid
			LEFT JOIN vendorinfo AS V ON J.vendorinfoid=V.vendorinfoid
		 	WHERE J.jobinfoid='$jobinfoid'";
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

		<div class="col-md-12">
		    <form>
		    <input type='hidden' class='jobinfoid' value="<?php echo $row['jobinfoid'];?>">
		        <div class="form-group">
		            Job Number: <input type="text" class="form-control jobno" name="jobno" value="<?php echo $row['jobno'];?>" >   
		        </div> 
		        <div class="form-group">
		            Circle: 
		            <?php 
		            	if($row['status']=='0')
		            	{
		            		echo "<select class='form-control circleinfoid'>";
		            	}
		            	else
		            	{
		            		echo "<select class='form-control circleinfoid' disabled>";
		            	}
		            ?>
		            
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
		            Location: 
		            <?php 
		            	if($row['status']=='0')
		            	{
		            		echo "<select class='form-control locationid'>";
		            	}
		            	else
		            	{
		            		echo "<select class='form-control locationid' disabled>";
		            	}
		            ?>
		            <option selected value="<?php echo $row['locationid'];?>"><?php echo $row['sitename'];?></option>
		            
		            <option value='0'>-- Select Location --</option>
		            <?php
		            	$sql_location_info = "SELECT * FROM location WHERE circleinfoid=".$row['circleinfoid'];
		            	$location_info_result = pg_query($conn, $sql_location_info);
		            	if(pg_num_rows($location_info_result)>0)
		            	{
		            		while($row_location_info = pg_fetch_array($location_info_result))
		            		{
		            			echo "<option value='".$row_location_info['locationid']."'>".$row_location_info['sitename']."</option>";
		            		}
		            		
		            	}
		            ?>
		            </select>   
		        </div>

		        <div class="form-group">
		            Vendor info: <select class="form-control vendorinfoid">
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

		        
		        <div class="form-group">
		            Accuracy : <input type="text" class="form-control accurdistance" name="accurdistance" value="<?php echo $row['accurdistance'];?>"  id="accurdistance" >   
		        </div>
		        <div class="form-group">
		        	Strict Location :
		          	<input type='checkbox' data-toggle='toggle' name='errorflg' class='errorflg' id='errorflg' checked data-on='On' data-off='Off'>
		          	
		        </div>
		        
		        <div class="form-group status">
		                                
		        </div>
		        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
				    <h5>Success</h5>
				    <div>Job info updated successfully!</div>
				</div>

				<div>
	                <button type="button" class="btn btn-info update_submit">Update</button>
	            </div>
		    </form>
		</div>
			</div>
			<div  class="col-md-3">
			</div>
		</div>
	</div>
		
<?php include 'footer.php';} ?>

<?php
	pg_close($conn);
?>
<script>
$(document).ready(function(){
	// get locations of perticular circle
	$('.circleinfoid').change(function(){
		event.preventDefault();
		var circleinfoid = $(this).val();
		var task = 'fetch_locations';
		$.ajax({
			type : 'post',
			url : 'fetch_data_helper.php',
			data : 'circleinfoid='+circleinfoid+'&task='+task,
			success : function(res)
			{
				$('.locationid').html(res);
				return false;
			}
		});
	});

	

	// add button click
	$('.update_submit').click(function(){               // update_submit click
		event.preventDefault();
		var jobinfoid = $('.jobinfoid').val();
		var jobno = $('.jobno').val();
		var circleinfoid = $('.circleinfoid').val();
		var locationid = $('.locationid').val();
		var accurdistance = $('.accurdistance').val();
		var errorflg = $('.errorflg').val();
		var vendorinfoid =$('.vendorinfoid').val();
		var task = 'update_job_info';
		if(jobno=='' || jobno==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter job number.</div>");
			return false;
		}
		else
		if(circleinfoid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select circle first.</div>");
			return false;
		}
		else
		if(locationid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select location & then submit.</div>");
			return false;
		}
		else
		if(vendorinfoid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select vendor & then submit.</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'jobinfoid='+jobinfoid+'&jobno='+jobno+'&circleinfoid='+circleinfoid+'&locationid='+locationid+'&accurdistance='+accurdistance+'&errorflg='+errorflg+'&vendorinfoid='+vendorinfoid+'&task='+task,
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