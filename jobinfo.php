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
	if(isset($_POST['export_data']))                           //export_data click
	{
		include 'php/config.php';

		$conn = pg_connect($conn_string);

		if(!$conn)
		{
		    echo "ERROR : Unable to open database";
		    exit;
		}
		$status_jobs = $_POST['status_jobs'];
		if($_POST['start_date']=='' || $_POST['start_date']==null)
		{
			$start_date = '0';
		}
		else
		{
			$start_date = $_POST['start_date'];
		}

		if($_POST['end_date']=='' || $_POST['end_date']==null)
		{
			$end_date = '0';
		}
		else
		{
			$end_date = $_POST['end_date'];
		}
		
		
		$where = '';
		$sql = "SELECT DISTINCT jobinfoid FROM jobinfo  WHERE 1 = 1 ";

		
		if($status_jobs == '1')
		{
			$where .= "";
		}
		else
		if($status_jobs == '2')
		{
			$where .=" AND status = '0' ";
		}	
		else
		if($status_jobs == '3')
		{
			$where .=" AND status = '1' ";
		}
		else
		if($status_jobs == '4')
		{
			$where .=" AND status = '2' ";
		}
		

		if( $start_date!='0') 
		{ 
			$where .=" AND createdon >= '".$start_date."' ";
		}

		if( $end_date!='0') 
		{ 
			$where .=" AND createdon <= '".$end_date." 23:59:59' ";
		}
		$where .=" ORDER BY jobinfoid";
		$sql .= $where;

		$result1 = pg_query($conn, $sql);

		if (!$result1)
		{
		    echo "ERROR : " . pg_last_error($conn);
		    exit;
		}

		if(pg_num_rows($result1) > 0){
		    $delimiter = ",";
		    $filename = "Job_info_data_" . date('Y-m-d') . ".csv";
		    
		    //create a file pointer
		    $f = fopen('php://memory', 'w');
		    
		    //set csv column headers
		    $fields = array('Job ID', 'Site Code', 'Site Name', 'Job Code', 'Accuracy', 'Strict Location', 'Token ID', 'Status','Taken By' , 'Start Time', 'End Time', 'Creation Time', 'Site Images');
		    fputcsv($f, $fields, $delimiter);
		    
		    //output each row of the data, format line as csv and write to file pointer
		    while($row = pg_fetch_row($result1)){
		    	$sql_jobinfo = "SELECT J.jobinfoid,L.sitecode, L.sitename,J.jobno,J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.emailid, J.starttime, J.endtime, J.createdon 
				FROM jobinfo AS J 
				INNER JOIN location AS L ON J.locationid=L.locationid 
				LEFT JOIN userinfo AS U ON J.userid=U.userid 
				WHERE J.jobinfoid=".$row['0'];
				$query_jobinfo = pg_query($conn, $sql_jobinfo);
				$row_jobinfo = pg_fetch_array($query_jobinfo);

				$sql_fileinfo = "SELECT filename FROM imageinfo WHERE (filename LIKE 'Site_%' OR filename LIKE 'Hording_%') AND jobinfoid=".$row['0']."ORDER BY filename";
				$query_fileinfo = pg_query($conn, $sql_fileinfo);
				while($row_fileinfo = pg_fetch_row($query_fileinfo))
				{
					if($row_fileinfo['0'] != '')
					{
						//$img_files[] .= $row_fileinfo['0'];
						$img_files[] = $row_fileinfo['0'] ;
					}
					else
					{
						$img_files[] = "";
					}
				}

				$images = implode(', ', $img_files);
		    	$accuracy =  $row_jobinfo['accurdistance'].' '.$row_jobinfo['accurdistanceunit'];

		    	if ($row_jobinfo['errorflg'] == '0')
					$strict_location = "No";
				else if ($row_jobinfo['errorflg'] == '1')
					$strict_location = "Yes";

				if ($row_jobinfo['status'] == '0')
					$status = "Not Started";
				else if ($row_jobinfo['status'] == '1')
					$status = "Started";
				else if ($row_jobinfo['status'] == '2')
					$status = "Finished";
				

				if($row_jobinfo['starttime']!='')
				{
					$starttime = $row_jobinfo['starttime'];
					//$old_starttime_timestamp = strtotime($starttime);
					$new_starttime = date('Y-m-d H:i:s', strtotime($starttime)); 
				}
				else
				{
					$new_starttime = "";
				}  

				if($row_jobinfo['endtime']!='')
				{
					$endtime = $row_jobinfo['endtime'];
				//$old_endtime_timestamp = strtotime($endtime);
					$new_endtime = date('Y-m-d H:i:s', strtotime($endtime)); 
				}
				else
				{
					$new_endtime = "";
				}  

				if($row_jobinfo['createdon']!='')
				{
					$createdon = $row_jobinfo['createdon'];
					//$old_createdon_timestamp = strtotime($createdon);
					$new_createdon = date('Y-m-d H:i:s', strtotime($createdon)); 
				}
				else
				{
					$new_createdon = "";
				}  

		        $lineData = array(''.$row_jobinfo['jobinfoid'].'', ''.$row_jobinfo['sitecode'].'', ''.$row_jobinfo['sitename'].'', ''.$row_jobinfo['jobno'].'',''.$accuracy.'', ''.$strict_location.'', ''.$row_jobinfo['tokenid'].'', ''.$status.'', ''.$row_jobinfo['emailid'].'', ''.$new_starttime.'', ''.$new_endtime.'', ''.$new_createdon.'',''.$images.'');
		        fputcsv($f, $lineData, $delimiter);
		    }
		    
		    //move back to beginning of file
		    fseek($f, 0);
		    
		    //set headers to download file rather than displayed
		    header("Content-Type: text/csv");
		    header('Content-Disposition: attachment; filename="' . $filename . '";');
		    header('Content-Description: File Transfer');
		    header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');

		    //output all remaining data on a file pointer
		    fpassthru($f);
		}
		exit;
	}
?>
<html>
<head>
<title>Jobs</title>

</head>
<body>
<?php

//include connection file
include 'php/config.php';
$conn = pg_connect($conn_string);

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
                                <a href="#add_new_job_info" data-toggle='modal' style="color:blue;text-align:right;"  class="nav-link">Add New Job</a>
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
			
			<div class='col-md-12'>
			<h3>Jobs</h3>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<div class='row'>
			        	<div class="col-md-4">
						Status:<br/>
						<label><input type='radio' name='status_jobs' class='status_jobs' value='1' checked data-column="1"> All</label>
						<label><input type='radio' name='status_jobs'  class='status_jobs' value='2'  data-column="1"> Not started</label>
						<label><input type='radio' name='status_jobs' class='status_jobs' value='3'  data-column="1"> Started</label>
						<label><input type='radio' name='status_jobs' class='status_jobs' value='4'  data-column="1"> Finished</label>
						
					</div>
					<div class="col-md-2">
						<label>
						Jobs Created From Date: <input class="form-control form-control-sm start_date" id="start_date" name="start_date" placeholder="YYYY-MM-DD" type="text" data-column="2"/>
						</label>
						
					</div>
					<div class="col-md-2">
						<label>
						Jobs Created To Date: <input class="form-control form-control-sm end_date" id="end_date" name="end_date" placeholder="YYYY-MM-DD" type="text" data-column="3"/>
						</label>
					</div>
					<div class="col-md-2"><br/>
						<input class='btn btn-success btn-sm' value='Export Result' name='export_data' type='submit'>
					</div>
				</div>
			</div>
			</form>
	        <div  class="col-md-12">
			<table  id='tieuptable' class='table-hover table-striped table-bordered job_list' style="width:100%">
				<thead>
					<tr>
					    <th>Job Id</th>
						<th>Site Code</th>
						<th>Site Name</th>
						<th>Job Code</th>
						<th>Accuracy</th>
						<th>Strict Location</th>
						<th>Token Id</th>
						<th>Status</th>
						<th>Taken By</th>
						<th>Start Time</th>
						<th>End Time</th>
						<th>Creation Time</th>
						<th>Site Images</th>
					</tr>
				</thead>

			</table>
			</div>
	</div>
		
<!-- Modal New job Addition -->
<div class="modal fade" id="add_new_job_info" role="dialog">
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
                <h3 class="text-center">Add New Job</h3>
                <form>
			        <div class="form-group">
			            Job Code: <input type="text" class="form-control form-control-sm jobno" name="jobno" placeholder="Job Code" id="jobno" >   
			        </div> 
			        <div class="form-group">
			            Circle: <select class="form-control form-control-sm circleinfoid">
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
			            Vendor: <select class="form-control form-control-sm vendorinfoid">
			            <option value='0' selected>-- Select Vendor --</option>
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
			            Location: <select class="form-control form-control-sm locationid">
			            <option value='0'>-- Select Location --</option>
			            
			            </select>   
			        </div>
			        
			        <div class="form-group">
			            Accuracy: <input type="text" class="form-control form-control-sm accurdistance" name="accurdistance" placeholder="Accuracy in Meters. Eg. 200" id="accurdistance" >   
			        </div>
			        <div class="form-group">
			        	Strict Location Accuracy:
                      		<input type='checkbox' data-toggle='toggle' name='errorflg' class='errorflg form-control form-control-sm' id='errorflg' checked data-on='On' data-off='Off'>
                      	
			        </div>
			        
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>New job info added successfully!</div>
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

<?php include 'footer.php';} ?>

<?php
	$script='1';
	pg_close($conn);
?>
<script>
$(document).ready(function(){

	var dataTable = $('.job_list').DataTable({
	"order": [[ 0, "asc" ]],
        "bProcessing": true,
        "serverSide": true,
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "ajax":{
            url :"jobinfo_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".job_list_processing").css("display","none");
            }
        }
    });

	//datatable filter on status
	$('.status_jobs').on( 'change', function () {
		var i =$(this).attr('data-column');
	    var v =$(this).val();
		dataTable.columns(i).search(v).draw();
	} );

	//datatable filter from date
	$('.start_date').on( 'change', function (){
		var i =$('.start_date').attr('data-column');  // getting column index
		var v =$('.start_date').val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	//datatable filter to date
	$('.end_date').on( 'change', function (){
		var i =$('.end_date').attr('data-column');  // getting column index
		var v =$('.end_date').val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});


	// get locations of perticular circle
	$('.circleinfoid').change(function(){
		event.preventDefault();
		var circleinfoid = $(this).val();
		var vendorinfoid = $('.vendorinfoid').val();
		var task = 'fetch_locations_circlewise';
		$.ajax({
			type : 'post',
			url : 'fetch_data_helper.php',
			data : 'circleinfoid='+circleinfoid+'&vendorinfoid='+vendorinfoid+'&task='+task,
			success : function(res)
			{
				$('.locationid').html(res);
				return false;
			}
		});
	});

	// get locations of perticular vendor
	$('.vendorinfoid').change(function(){
		event.preventDefault();
		var vendorinfoid = $(this).val();
		var circleinfoid = $('.circleinfoid').val();
		var task = 'fetch_locations_vendorwise';
		$.ajax({
			type : 'post',
			url : 'fetch_data_helper.php',
			data : 'vendorinfoid='+vendorinfoid+'&circleinfoid='+circleinfoid+'&task='+task,
			success : function(res)
			{
				$('.locationid').html(res);
				return false;
			}
		});
	});

	
	// add button click
	$('.btn_submit').click(function(){
		event.preventDefault();
		var jobno = $('.jobno').val();
		var circleinfoid = $('.circleinfoid').val();
		var locationid = $('.locationid').val();
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
		var vendorinfoid =$('.vendorinfoid').val();
		var task = 'add_job_info';
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
		if(vendorinfoid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select vendor & then submit.</div>");
			return false;
		}
		else
		if(locationid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select location & then submit.</div>");
			return false;
		}
		
		
		else
		{
			$.ajax({
				type : 'post',
				url : 'addition_helper.php',
				data : 'jobno='+jobno+'&circleinfoid='+circleinfoid+'&locationid='+locationid+'&accurdistance='+accurdistance+'&errorflg='+errorflg+'&vendorinfoid='+vendorinfoid+'&task='+task,
				success : function(res)
				{
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
						//$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
						$('.status').html("<div class='alert alert-danger'><strong>Query Failed!</div>"+res);
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
