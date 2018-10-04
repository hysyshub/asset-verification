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
<title>Job Dropdown Values</title>

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

$jobinfoid = $_GET['jobinfoid'];
$query = "SELECT J.*,L.sitecode FROM jobinfo as J JOIN location as L ON J.locationid=L.locationid WHERE J.status='0' ORDER BY J.jobinfoid ";
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
                                <a href="jobinfo.php" style="color:blue;text-align:right;" class="nav-link">Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-info.php"style="color:blue;text-align:right;"  class="nav-link">Import Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a href="job-data-fields.php?jobinfoid=0"style="color:blue;text-align:right;" class="nav-link">Job Data Fields</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-data-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Data Fields</a>
                            </li>
                            <li class="nav-item">
                                <a href="import-job-dropdown-fields.php"style="color:blue;text-align:right;"  class="nav-link">Import Job Dropdown Values</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			
		<h2>Job Dropdown Values</h2>
		<?php
				echo "<h3 class='h4'>". pg_num_rows($result) . " jobs found</h3>";
				$jobinfoid = $_GET['jobinfoid'];
			?>
			<div class="col=md-12 form-group">
				<label for="select_job" class="col-sm-4 control-label">Select Job:</label>
		        <div>
		            <select name='jobinfoid' class='jobinfoid form-control form-control-sm' style='width:200px;'>
						<option value='0'>-- Select job --</option>
						<?php
							while($row = pg_fetch_array($result))
							{
								if($row['jobinfoid']==$jobinfoid)
								{
									echo "<option value='".$row['jobinfoid']."' selected>".$row['jobno']." [Sitecode - ".$row['sitecode']."]</option>";
								}
								else
								{
									echo "<option value='".$row['jobinfoid']."'>".$row['jobno']." [Sitecode - ".$row['sitecode']."]</option>";
								}
							}

						?>
					</select>
			</div>
			<hr/>
			<?php
			if($jobinfoid != '0')
			{
				$sql1 = "SELECT count(visittype) as count FROM visits  WHERE jobinfoid='$jobinfoid' AND visittype='5' GROUP BY visittype";
				$res1 = pg_query($conn,$sql1);
				$row1 = pg_fetch_array($res1);
				//echo $count = $row1['count'];
				$count = $row1['count'];
				if($count=='0')
				{
					echo "<h3 style='color:red;'>No job dropdown found for this JOB";
				}
				else
				{
					echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>";
						
						$select = 1;
						echo "<tr>";
							echo "<td>";
								echo "<h4 style='color:blue;'> Previous terms for jobdropdown ".$select."</h4>";
								$sql2 = "SELECT * FROM jobdropdown  WHERE jobinfoid='$jobinfoid' AND category='1' ORDER BY indx";
								$res2 = pg_query($conn,$sql2);
								//$row2 = pg_fetch_array($res2);
								
								if(pg_num_rows($res2)>0)
								{
									echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' style='width:500px;'>";
									$pos = 1;
									while($row2 = pg_fetch_array($res2))
									{
										
										echo "<tr>";
											echo "<td> Position $pos Term: <td>";
											echo "<td>";
												echo "<input type='text' id='".$row2['jobdropdownid']."' value='".$row2['term']."' class='form-control form-control-sm' style='width:250px;' disabled/>";
											echo "</td>";
											echo "<td>";
												echo "<a class='btn btn-sm btn-danger edit_term' href='#edit_term_".$row2['jobdropdownid']."' data-toggle='modal'>Edit</a><br/>";
											echo "</td>";
											
										echo "</tr>";
										$pos = $pos+1;
										echo "<div class='modal fade' id='edit_term_".$row2['jobdropdownid']."' role='dialog'>
										    <div class='modal-dialog'>
										        <div class='modal-content  col-md-12'>
										            <!-- Modal Header -->
										            <div class='modal-header'>
										                <button type='button' class='close' data-dismiss='modal'>
										                    <span aria-hidden='true'>&times;</span>
										                    <span class='sr-only'>Close</span>
										                </button>
										            </div>
										            
										            <!-- Modal edit term -->
										            <div class='modal-body'>
										                <h2 class='text-center'>Update Term</h2>
										                <form>
													        <div class='form-group'>
													        	<label for='term' class='control-label' style='color:blue;'>Previous term : ".$row2['term']."</label><br/>
													            New term: <input type='text' class='form-control form-control-sm new_term_".$row2['jobdropdownid']."' name='new_term' placeholder='New term value' id='new_term' >
													            <input type='hidden' class='form-control indx_val_".$row2['jobdropdownid']."' id='indx_val_".$row2['jobdropdownid']."' value='".$row2['indx']."'>       
													        </div>
													        
										                </form>
										                <div class='update_status_term'>
										            </div>
										            
										            
										            <!-- Modal Footer -->
										            <div class='modal-footer'>
										                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
										                <button type='button' class='btn btn-sm btn-danger btn_update' value='".$row2['jobdropdownid']."'>Update</button>
										            </div>
										        </div>
										    </div>";
									}
									echo "</table>";
									echo "<div class='update_status' style='width:500px;'>";
									echo "</td>";
								}
								

							echo "</td>";
							echo "<td>";
								echo "<h4 style='color:red;'> Enter new terms for jobdropdown ".$select."</h4>";
								echo "<input type='text' id='select1_0' placeholder='Term at position 1 ' class='form-control form-control-sm'/><br/>";
								echo "<input type='text' id='select2_0'  placeholder='Term at position 2 ' class='form-control form-control-sm'/><br/>";
								echo "<input type='text' id='select3_0'  placeholder='Term at position 3 ' class='form-control form-control-sm'/><br/>";
								echo "<input type='text' id='select4_0'  placeholder='Term at position 4 ' class='form-control form-control-sm'/><br/>";
								echo "<input type='text' id='select5_0'  placeholder='Term at position 5 ' class='form-control form-control-sm'/><br/>";
							echo "</td>";
						echo "</tr>";
						
						if($count=='2')
						{
							$select = $select + 1;
							
							echo "<tr>";
								echo "<td>";
								echo "<h4 style='color:blue;'> Previous terms for jobdropdown ".$select."</h4>";
								$sql2 = "SELECT * FROM jobdropdown  WHERE jobinfoid='$jobinfoid' AND category='2' ORDER BY indx";
								$res2 = pg_query($conn,$sql2);
								//$row2 = pg_fetch_array($res2);
								
								if(pg_num_rows($res2)>0)
								{
									echo "<table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' style='width:500px;'>";
									$pos = 1;
									while($row2 = pg_fetch_array($res2))
									{
										
										echo "<tr>";
											echo "<td> Position $pos Term: <td>";
											echo "<td>";
												echo "<input type='text' id='".$row2['jobdropdownid']."' value='".$row2['term']."' class='form-control form-control-sm' style='width:250px;' disabled/>";
											echo "</td>";
											echo "<td>";
												echo "<a class='btn btn-sm btn-danger edit_term' href='#edit_term_".$row2['jobdropdownid']."' data-toggle='modal'>Edit</a><br/>";
											echo "</td>";
											
										echo "</tr>";
										$pos = $pos+1;
										echo "<div class='modal fade' id='edit_term_".$row2['jobdropdownid']."' role='dialog'>
										    <div class='modal-dialog'>
										        <div class='modal-content  col-md-12'>
										            <!-- Modal Header -->
										            <div class='modal-header'>
										                <button type='button' class='close' data-dismiss='modal'>
										                    <span aria-hidden='true'>&times;</span>
										                    <span class='sr-only'>Close</span>
										                </button>
										            </div>
										            
										            <!-- Modal edit term -->
										            <div class='modal-body'>
										                <h2 class='text-center'>Update Term</h2>
										                <form>
													        <div class='form-group'>
													        <label for='term' class='control-label' style='color:blue;'>Previous term : ".$row2['term']."</label><br/>
													            New term: <input type='text' class='form-control form-control-sm new_term_".$row2['jobdropdownid']."' name='new_term' placeholder='New term' id='new_term' >
													            <input type='hidden' class='form-control indx_val_".$row2['jobdropdownid']."' id='indx_val_".$row2['jobdropdownid']."' value='".$row2['indx']."'>       
													        </div>
										                </form>
										                <div class='update_status_term'>
										            </div>
										            
										            
										            <!-- Modal Footer -->
										            <div class='modal-footer'>
										                <button type='button' class='btn btn-sm btn-default' data-dismiss='modal'>Close</button>
										                <button type='button' class='btn btn-sm btn-danger btn_update' value='".$row2['jobdropdownid']."'>Update</button>
										            </div>
										        </div>
										    </div>";
									}
									echo "</table>";
									echo "<div class='update_status' style='width:500px;'>";
									echo "</td>";
								}
								

							echo "</td>";
								echo "<td>";
									echo "<h4 style='color:red;'> Enter new terms for jobdropdown ".$select."</h4>";
								
									echo "<input type='text' id='select1_1' placeholder='Term at position 1 ' class='form-control form-control-sm'/><br/>";
									echo "<input type='text' id='select2_1'  placeholder='Term at position 2 ' class='form-control form-control-sm'/><br/>";
									echo "<input type='text' id='select3_1'  placeholder='Term at position 3 ' class='form-control form-control-sm'/><br/>";
									echo "<input type='text' id='select4_1'  placeholder='Term at position 4 ' class='form-control form-control-sm'/><br/>";
									echo "<input type='text' id='select5_1'  placeholder='Term at position 5 ' class='form-control form-control-sm'/><br/>";
								echo "</td>";
							echo "</tr>";
							
						}
						
						echo "<tr>";
							echo "<td>";
								echo "<center><input type='button' class='btn btn-sm btn-success btn-save' value='Save'></center>"; 
							echo "</td>";
							echo "<td class='status'>";
							echo "</td>";
						echo "</tr>";

					echo "</table>";

					echo "<input type='hidden' class='jobinfoid_val' value='".$jobinfoid."'>";
					echo "<input type='hidden' class='count' value='".$count."'>";
				}
			}
		?>
			</div>
			
		</div>
	</div>
		
<?php 
pg_close($conn);

include 'footer.php'; 

	}
?>

<script>
$(document).ready(function(){
	$('.jobinfoid').change(function(){
		var jobinfoid = $(this).val();
		window.location.assign("jobdropdown.php?jobinfoid="+jobinfoid);
	});

	$('.btn-save').click(function(){                 //btn-save click
		
		var jobinfoid_val = $('.jobinfoid_val').val();
		var count = $('.count').val();
		var term_val1=[];
		var term_val2=[];
		var task = 'add_new_terms';
		var data = 'jobinfoid='+jobinfoid_val+'&task='+task;
		
		if(count==2)
		{
			for(var j=1;j<6;j++)
			{
				var val = $('#select'+j+'_'+0).val();
				if(val=='' || val==null)
				{
					continue;
				}
				else
				{
					term_val1.push($('#select'+j+'_'+0).val());
				}
			}
			for(var j=1;j<6;j++)
			{
				var val = $('#select'+j+'_'+1).val();
				if(val=='' || val==null)
				{
					continue;
				}
				else
				{
					term_val2.push($('#select'+j+'_'+1).val());
				}
			}
	
			
			if(term_val1.length>0)
			{
				data +='&term_val1='+term_val1;
			}
			if(term_val2.length>0)
			{
				data +='&term_val2='+term_val2;
			}
		}
		else
		if(count==1)
		{
			for(var j=1;j<6;j++)
			{
				var val = $('#select'+j+'_'+0).val();
				if(val=='' || val==null)
				{
					continue;
				}
				else
				{
					term_val1.push($('#select'+j+'_'+0).val());
				}
			}
			if(term_val1.length>0)
			{
				data +='&term_val1='+term_val1;
			}
		}
		
		$.ajax({
			type : 'post',
			url : 'jobdropdown_helper.php',
			data : data,
			success : function(res)
			{
				if(res=='conn_error')
				{
					$('.status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
					return false;
				}
				else
				if(res=='success')
				{
					$('.status').html("<div class='alert alert-success'><strong>Success!</strong> Job dropdown configured successfully.</div>");
					window.location.assign('jobdropdown.php?jobinfoid='+jobinfoid_val);
					return false;
				}
				else
				{
					$('.status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
					return false;
				}
			}
		});

	});

	$('.btn_update').click(function(){                 //btn_update click
		var jobinfoid_val = $('.jobinfoid_val').val();
		var jobdropdownid = $(this).val();
		var new_term_val = $('.new_term_'+jobdropdownid).val();
		var indx = $('.indx_val_'+jobdropdownid).val();
		
		if(new_term_val=='' || new_term_val==null)
		{
			$('.update_status_term').html("<div class='alert alert-danger'><strong>Empty!</strong> Please enter term value.</div>");
			$('#edit_term_'+jobdropdownid).modal('toggle');
				return false;
		}
		var task = 'update_term';

		$.ajax({
			type : 'post',
			url : 'jobdropdown_helper.php',
			data : 'jobdropdownid='+jobdropdownid+'&new_term_val='+new_term_val+'&indx='+indx+'&task='+task,
			success : function(res)
			{	
				if(res=='conn_error')
				{
					$('.update_status_term').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
					return false;
				}
				else
				if(res=='success')
				{
					$('.update_status_term').html("<div class='alert alert-success'><strong>Success!</strong> Term updated successfully.</div>");
					window.location.assign('jobdropdown.php?jobinfoid='+jobinfoid_val);
				}
				else
				{
					$('.update_status_term').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
					return false;
				}
			}
		});
	});
});
</script>
</body>
</html>
