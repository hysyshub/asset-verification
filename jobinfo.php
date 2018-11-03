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
	
	if(isset($_POST['export_data']))                           //export_data click
	{

		$status_jobs = quote_smart($_POST['status_jobs']);
		if(quote_smart($_POST['start_date'])=='' || quote_smart($_POST['start_date'])==null)
		{
			$start_date = '0';
		}
		else
		{
			$start_date = quote_smart($_POST['start_date']);
		}

		if(quote_smart($_POST['end_date'])=='' || quote_smart($_POST['end_date'])==null)
		{
			$end_date = '0';
		}
		else
		{
			$end_date = quote_smart($_POST['end_date']);
		}
		
		
		$where = '';
		$sql = "SELECT J.jobinfoid, L.sitecode, L.sitename, J.jobno, J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.emailid, J.starttime, J.endtime, J.createdon
	    		FROM jobinfo AS J 
			INNER JOIN location AS L ON J.locationid=L.locationid 
			LEFT JOIN userinfo AS U ON J.userid=U.userid WHERE 1=1 ";
		
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

		if (isset($_POST['datatable_search']) && quote_smart($_POST['datatable_search']) != '')
		{
			$dbtable_search = quote_smart($_POST['datatable_search']);

			$where .=" AND ( CAST(jobinfoid AS text) ILIKE '%".$dbtable_search."%' ";    
			$where .=" OR sitecode ILIKE '%".$dbtable_search."%' ";
			$where .=" OR sitename ILIKE '%".$dbtable_search."%' ";
			$where .=" OR jobno ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(accurdistance AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR J.tokenid ILIKE '%".$dbtable_search."%' ";
			$where .=" OR emailid ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(starttime AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(endtime AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(createdon AS text) ILIKE '%".$dbtable_search."%' )";
		}

		$where .=" ORDER BY jobinfoid";
		$sql .= $where;

		$result1 = pg_query($conn, $sql);

		if (!$result1)
		{
		    echo "ERROR : " . pg_last_error($conn);
		    exit;
		}

		if(pg_num_rows($result1) > 0)
		{
		    $filename = "job_info_data_" . date('Y-m-d') . ".csv";
		    
		    //set headers to download file rather than displayed
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename="' . $filename . '";');

		    // do not cache the file	
		    header('Pragma: no-cache');
		    header('Expires: 0');

		    //create a file pointer
		    $f = fopen('php://output', 'w');
		    
		    //set csv column headers
		    $fields = array('Job ID', 'Site Code', 'Site Name', 'Job Code', 'Accuracy', 'Strict Location', 'Token ID', 'Status','Taken By' , 'Start Time', 'End Time', 'Creation Time');
		    fputcsv($f, $fields);
		    
		    //output each row of the data, format line as csv and write to file pointer
		    while($row_jobinfo = pg_fetch_array($result1))
		    {
				/*$sql_fileinfo = "SELECT filename FROM imageinfo WHERE (filename LIKE 'Site_%' OR filename LIKE 'Hording_%') AND jobinfoid=".$row['0']."ORDER BY filename";
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

				$images = implode(', ', $img_files);*/
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

		        $lineData = array($row_jobinfo['jobinfoid'], $row_jobinfo['sitecode'], $row_jobinfo['sitename'], $row_jobinfo['jobno'], $accuracy, $strict_location, $row_jobinfo['tokenid'], $status, $row_jobinfo['emailid'], $new_starttime, $new_endtime, $new_createdon);
		        fputcsv($f, $lineData);
		    }
		    
		}
		exit;
	}
?>
<html>
<head>
<title>Jobs</title>

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
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="custom_form">
				<input type="hidden" name='export_data' id='export_data' value='1' />
				<input type="hidden" name="datatable_search" id="datatable_search" value="" />
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
						<input class='btn btn-success btn-sm' value='Export Result' name='export_button' id='export_button' type='button'>
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
						<th>Reset Job</th>
						<th>Edit Job</th>
						<th>Event Logs</th>
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
			            Location: <select class="form-control form-control-sm locationid">
			            <option value='0'>-- Select Location --</option>
			            <?php
			            	$sql_location_info = "SELECT * FROM location ORDER BY locationid";
			            	$location_info_result = pg_query($conn, $sql_location_info);
			            	if(pg_num_rows($location_info_result)>0)
			            	{
			            		while($row_location_info = pg_fetch_array($location_info_result))
			            		{
			            			echo "<option value='".$row_location_info['locationid']."'>" . $row_location_info['sitecode'] . " - " . $row_location_info['sitename'] . "</option>";
			            		}
			            	}
			            ?>
			            </select>   
			        </div>
			        <div class="form-group">
			      		Job Submission Token: <input type="text" class="form-control form-control-sm jobtoken" name="jobtoken" id="jobtoken" >
			        </div>
			        <div class="form-group">
			      		Accuracy in Meters (Eg. 200): <input type="text" class="form-control form-control-sm accurdistance" name="accurdistance" value="200" id="accurdistance" >   
			        </div>
			        <div class="form-group">
		        		Strict Location? <input type='checkbox' data-toggle='toggle' name='errorflg' class='errorflg form-control form-control-sm' id='errorflg' data-on='On' data-off='Off' data-size="small" >
			        </div>
			        <div class="form-group status">
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert"></a>
					    <h5>Success</h5>
					    <div>New job added successfully!</div>
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

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
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

	$('.job_list tbody').on( 'click', 'button', function () {
	var btnrow = dataTable.row( $(this).parents('tr') );
        var btnrowidx = btnrow.index();
        var data = btnrow.data();
        var btnspan = $(this).parents('span');
        var btnloading = $(this).next();
        var jobinfoid = data[0];
	var jobname = data[3];
	var jobsitename = data[2];
        var task = "reset_jobinfo";
        var r = confirm("Do you want to reset the job " + jobinfoid + " # " + jobname + " at site " + jobsitename  + "?");
		if (r == true) 
		{
			$(btnloading).css("display", "inline");
			$(this).attr("disabled",false);
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'jobinfoid='+jobinfoid+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
				success : function(res)
				{
					if(res == 'success')
					{
                                                dataTable.cell(btnrowidx, 7).data('Not Started');
                                                dataTable.cell(btnrowidx, 8).data('');
                                                dataTable.cell(btnrowidx, 9).data('');
                                                dataTable.cell(btnrowidx, 10).data('');
						$(btnspan).html("Reset successfully");
						return false; 
					}
					else
					{
						alert(res);
						$(btnloading).css("display", "none");
						return false;
					}
				}
			});
		}
		else
		{
			return false;
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

	$('#export_button').click( function (){
		$("#datatable_search").val($("#tieuptable_filter input").val());
		$("#custom_form").submit();
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
		var task = 'add_job_info';
		if(jobno=='' || jobno==null)
		{
			$('.status').html("<div class='alert alert-danger'>Please enter Job Code</div>");
			return false;
		}
		else
		if(locationid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Select select Location</div>");
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
				url : 'addition_helper.php',
				data : 'jobno='+jobno+'&locationid='+locationid+'&jobtoken='+jobtoken+'&accurdistance='+accurdistance+'&errorflg='+errorflg+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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

	$('.job_list tbody').on( 'click', 'a', function () {

	var button = $(this);
        var btnrow = dataTable.row( $(this).parents('tr') );
        var btnrowidx = btnrow.index();
        var data = btnrow.data();
        
	var jobinfoid = data[0];
	var jobcode = data[3];

	var modal = $("#exampleModal");
	modal.find('.modal-title').html("Events Logs for Job # <span style='font-family: monospace;'>" + jobinfoid + "</span> with Job Code # <span style='font-family: monospace;'>" + jobcode + "</span>")

	var task = "jobeventlogs_info";
        var task_data = 'jobinfoid='+jobinfoid+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>');
        	
	$.ajax({
		type : 'post',
		url : 'fetch_data_helper.php',
		data : task_data,
		success : function(res)
		{
			var resary = res.split("@#@");
			if(resary[0] == 'success')
			{
				modal.find('.modal-body').html(resary[1]);
			}
			else
			{
				alert('Error while fetching Job event logs');
				return false;
			}
		}
	});

    });	

});
</script>
</body>
</html>
