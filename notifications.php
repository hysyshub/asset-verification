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
<title>Notifications</title>
<script>

</script>


</head>
<body>
<?php

include 'header.php';
include 'php/config.php';
echo "<script src='https://cloud.tinymce.com/stable/tinymce.min.js'></script>";
$conn = pg_connect($conn_string);

if(!$conn)
{
	echo "ERROR : Unable to open database";
	exit;
}

$query = "SELECT * FROM usernotifications";
$result = pg_query($conn, $query);

if (!$result)
{
	echo "ERROR : " . pg_last_error($conn);
	exit;
}

?>
<!-- Page Content start -->
        <div id="content" style="width:100%">

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
                                <a href="#edit_notification" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Edit Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="#send_general_notification" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Send General Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="view_gen_notifications.php" style="color:blue;text-align:right;" class="nav-link">View General Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="#send_job_notification" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Send Job Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="view_job_notification.php" style="color:blue;text-align:right;" class="nav-link">View Job Notification</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
		<h2>Notifications</h2>
			
			<?php
			echo "<table id='tieuptable' style='width:100%; border: 1px solid #000;'>";
			echo "<thead>";
			echo "<tr>";
				echo "<th>Notification Text</th>";
			echo "</tr>";
			echo "</thead>";
			while($row = pg_fetch_array($result))
			{
				$usernotificationsid = $row['usernotificationsid'];
				echo "<tr>";
					echo "<td>" . $row['notification'] . "</td>";
				echo "</tr>";
			}
			echo "</table>";
		?>
			</div>
			<div  class="col-md-3">
			</div>
		</div>
	</div>
<?php include 'footer.php'; }?>

<!-- Modal notification edit -->
<div class="modal fade" id="edit_notification" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content  col-md-12">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <?php
            	$query = "SELECT * FROM usernotifications";
				$result = pg_query($conn, $query);

				if (!$result)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}
				$row = pg_fetch_array($result);
            ?>
            <!-- Modal Add new circle -->
            <div class="modal-body edit_modal">
                <h2 class="text-center">Edit Notification Text</h2>
                <form>
                	<div class="form-group">
                		<textarea cols='70' rows='12' class='notification ed' id="mytxtarea">
			            <?php echo $row['notification'];?></textarea>        
			        </div>
			         
			        <div class="form-group edit_notification_status">
			                                
			        </div>
			        <div class="alert alert-success edit_about_notification_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>Notification updated successfully!</div>
					</div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info btn_update_notification">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal send general notification -->
<div class="modal fade" id="send_general_notification" role="dialog">
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
            <div class="modal-body edit_modal">
                <h2 class="text-center">Send General Notification</h2>
                <form>
                    <div class="form-group">
                        Notification Subject: <input type="text" class="form-control gen_notify_sub" name="gen_notify_sub" placeholder="Notification Subject" id="gen_notify_sub" >        
                    </div>
                    <div class="form-group"> 
                        Notification Message: <textarea cols='70' rows='9' class='gen_notify_message' id="gen_notify_message"></textarea> 
                    </div>

                    
                    <div class="form-group"> 
                        Users: <select class='form-control userid' name='userid[]' multiple='yes'   id="userid" multiple="multiple" style="width:200px;">
                            <?php
                                $query = "SELECT * FROM userinfo";
                                $result = pg_query($conn, $query);

                                if (!$result)
                                {
                                    echo "ERROR : " . pg_last_error($conn);
                                    exit;
                                }
                                
                                while($row = pg_fetch_array($result))
                                {
                                    echo "<option value='".$row['userid']."'>".$row['firstname']." ".$row['lastname']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        URL: <input type="text" class="form-control gen_notify_url" name="gen_notify_url" placeholder="Notification Url" id="gen_notify_url" >      
                    </div>

                    <div class="form-group gen_notification_status_error">
                                            
                    </div>
                    <div class="alert alert-success gen_notification_status_success" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
                        <h5>Success</h5>
                        <div>Notification added successfully!</div>
                    </div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info gen_notification_add">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal send job notification -->
<div class="modal fade" id="send_job_notification" role="dialog">
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
            <div class="modal-body edit_modal">
                <h2 class="text-center">Send Job Specific Notification</h2>
                <form>
                    <div class="form-group"> 
                        Select Job: <select class='form-control job_jobinfoid' name='job_jobinfoid' id='job_jobinfoid'>
                            <option value='0' selected>-- Select Job --</option>
                            <?php
                                $query = "SELECT J.*,L.sitecode FROM jobinfo as J JOIN location as L ON J.locationid=L.locationid WHERE J.status='1'";
                                $result = pg_query($conn, $query);

                                if (!$result)
                                {
                                    echo "ERROR : " . pg_last_error($conn);
                                    exit;
                                }
                                
                                while($row = pg_fetch_array($result))
                                {
                                    echo "<option value='".$row['jobinfoid']."'>".$row['jobno']." [Sitecode - ".$row['sitecode']."]</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        Notification Subject: <input type="text" class="form-control job_notify_sub" name="job_notify_sub" placeholder="Notification Subject" id="job_notify_sub" >        
                    </div>
                    <div class="form-group"> 
                        Notification Message: <textarea cols='70' rows='9' class='job_notify_message' id="job_notify_message"></textarea> 
                    </div>

                    <div class="form-group">
                        URL: <input type="text" class="form-control job_notify_url" name="job_notify_url" placeholder="Notification Url" id="job_notify_url" >      
                    </div>

                    <div class="form-group job_notification_status_error">
                                            
                    </div>
                    <div class="alert alert-success job_notification_status_success" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
                        <h5>Success</h5>
                        <div>Notification added successfully!</div>
                    </div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info job_notification_add">Update</button>
            </div>
        </div>
    </div>
</div>

<?php 
	pg_close($conn);
?>

<script>
$('.notification').richText();

$('.btn_update_notification').click(function(){
	var notification = $('.notification').val();
	var task = 'update_notification';
	if(notification == '' || notification == null)
	{
		$('.edit_notification_status').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification text</div>");
        return false;
	}
	$.ajax({
		type : 'post',
		url : 'updation_helper.php',
		data : 'notification='+notification+'&task='+task,
		success : function(res)
		{
			if(res=='db_conn_error')
            {
                $('.edit_notification_status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.edit_about_notification_status').show();
                return false;
            }
            else
            {
                $('.edit_notification_status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
		}
	});
});

$('.gen_notification_add').click(function(){
    var gen_notify_sub = $('.gen_notify_sub').val();
    var gen_notify_message = $('.gen_notify_message').val();
    var gen_notify_url = $('.gen_notify_url').val();
    var task = "add_general_notification";
    var userid = $('.userid').val();
    
    if(gen_notify_sub == '' || gen_notify_sub == null)
    {
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification subject</div>");
        return false;
    }

    if(gen_notify_message == '' || gen_notify_message == null)
    {
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification message</div>");
        return false;
    }

    if(userid == '' || userid == null)
    {
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please select users.</div>");
        return false;
    }

    $.ajax({
        type : 'post',
        url : 'notification_helper.php',
        data : 'gen_notify_sub='+gen_notify_sub+'&gen_notify_message='+gen_notify_message+'&gen_notify_url='+gen_notify_url+'&userid='+userid+'&task='+task,
        success : function(res)
        {
            if(res=='db_conn_error')
            {
                $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.gen_notification_status_success').show();
                window.setTimeout(function () {
                    $(".gen_notification_status_success").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    window.location.reload();    
                    });
                }, 5000);
            }
            else
            {
                $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
        }
    });

});


$('.job_notification_add').click(function(){
    var jobinfoid = $('.job_jobinfoid').val();
    var job_notify_sub = $('.job_notify_sub').val();
    var job_notify_message = $('.job_notify_message').val();
    var job_notify_url = $('.job_notify_url').val();
    var task = "add_job_notification";

    if(jobinfoid == '0')
    {
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please select job.</div>");
        return false;
    }

    if(job_notify_sub == '' || job_notify_sub == null)
    {
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification subject</div>");
        return false;
    }

    if(job_notify_message == '' || job_notify_message == null)
    {
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification message</div>");
        return false;
    }

    

    $.ajax({
        type : 'post',
        url : 'notification_helper.php',
        data : 'jobinfoid='+jobinfoid+'&job_notify_sub='+job_notify_sub+'&job_notify_message='+job_notify_message+'&job_notify_url='+job_notify_url+'&task='+task,
        success : function(res)
        {
            if(res=='db_conn_error')
            {
                $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.job_notification_status_success').show();
                window.setTimeout(function () {
                    $(".job_notification_status_success").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    window.location.reload();    
                    });
                }, 5000);
            }
            else
            {
                $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
        }
    });

});
</script>
