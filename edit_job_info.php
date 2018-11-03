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
<title>Edit Job</title>

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
                                <a href="jobinfo.php" data-toggle='modal' style="color:blue;text-align:right;"  class="nav-link">Job Info</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-info.php"style="color:blue;text-align:right;"  class="nav-link">Import Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a href="job-data-fields.php?jobinfoid=0"style="color:blue;text-align:right;"  class="nav-link">Job Data Fields</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-data-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Data Fields</a>
                            </li>
                            <li class="nav-item">
                                <a href="jobdropdown.php?jobinfoid=0" style="color:blue;text-align:right;"  class="nav-link">Job Dropdown Values</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-dropdown-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Dropdown Values</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
		<h3>Edit Job</h3>
			
		<?php
		$jobinfoid = quote_smart($_GET['jobinfoid']);

		if (!is_numeric($jobinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$query = "SELECT * FROM jobinfo WHERE jobinfoid='$jobinfoid'";
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

		if ($row['status'] != 0)
		{
			echo "ERROR: Job already started or finished.";
			exit;
		}
	?>

		<div class="col-md-12">
		    <form>
		    <input type='hidden' class='jobinfoid' value="<?php echo $row['jobinfoid'];?>">
		        <div class="form-group">
		            Job Code: <input type="text" class="form-control form-control-sm jobno" name="jobno" value="<?php echo $row['jobno'];?>" >   
		        </div> 
		        <div class="form-group">
		            Location: 
		            <?php 
		            	if($row['status']=='0')
		            	{
		            		echo "<select class='form-control form-control-sm locationid'>";
		            	}
		            	else
		            	{
		            		echo "<select class='form-control form-control-sm locationid' disabled>";
		            	}
		            ?>	            
		            <option value='0'>-- Select Location --</option>
		            <?php
		            	$sql_location_info = "SELECT * FROM location ORDER BY locationid";
		            	$location_info_result = pg_query($conn, $sql_location_info);
		            	if(pg_num_rows($location_info_result)>0)
		            	{
		            		while($row_location_info = pg_fetch_array($location_info_result))
					{
						if ($row['locationid'] == $row_location_info['locationid'])
							echo "<option value='".$row_location_info['locationid']."' selected>".$row_location_info['sitename']."</option>";
						else
			            			echo "<option value='".$row_location_info['locationid']."'>".$row_location_info['sitename']."</option>";
		            		}
		            	}
		            ?>
		            </select>   
			</div>
		        <div class="form-group">
		      		Job Submission Token: <input type="text" class="form-control form-control-sm jobtoken" name="jobtoken" id="jobtoken" value="<?php echo $row['tokenid'];?>">
		        </div>
		        <div class="form-group">
		        	Accuracy in Meters (Eg. 200): <input type="text" class="form-control form-control-sm accurdistance" name="accurdistance" id="accurdistance" value="<?php echo $row['accurdistance'];?>">
		        </div>
		        <div class="form-group">
		        	Strict Location? <input type='checkbox' data-toggle='toggle' name='errorflg' class='errorflg' id='errorflg' <?php if ($row['errorflg'] == '1') echo "checked"; ?> data-on='On' data-off='Off' data-size="small">
		        </div>		        
		        <div class="form-group status">		                                
		        </div>
		        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert"></a>
				    <h5>Success</h5>
				    <div>Job updated successfully!</div>
				</div>

				<div>
	                <button type="button" class="btn btn-sm btn-info update_submit">Update</button>
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
		var locationid = $('.locationid').val();
		var jobtoken = $('.jobtoken').val();
		var accurdistance = $('.accurdistance').val();
		var errorflg = $(".errorflg").is(":checked");
		if(errorflg==true)
		{
			errorflg='1';
		}
		else
		{
			errorflg='0';
		}
		
		var task = 'update_job_info';
		if(jobno=='' || jobno==null)
		{
			$('.status').html("<div class='alert alert-danger'>Please enter Job Code</div>");
			return false;
		}
		else
		if(locationid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Please select Location</div>");
			return false;
		}
		else
		if(jobtoken=='' || jobtoken==null)
		{
			$('.status').html("<div class='alert alert-danger'>Please enter Job Submission Token</div>");
			return false;
		}
		else
		if(accurdistance=='' || accurdistance==null)
		{
			$('.status').html("<div class='alert alert-danger'>Please enter Accuracy in Meters</div>");
			return false;
		}		
		else
		{
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'jobinfoid='+jobinfoid+'&jobno='+jobno+'&locationid='+locationid+'&jobtoken='+jobtoken+'&accurdistance='+accurdistance+'&errorflg='+errorflg+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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
