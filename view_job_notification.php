<?php 
session_start();
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
<title>Job Notifications</title>

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
                            <li class="nav-item pull-left">
                                <a href="view_gen_notifications.php" style="color:blue;text-align:right;" class="nav-link">View General Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="#send_job_notification" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Send Job Notification</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

		<div  class="col-md-12">
			<h2>Job Specific Notifications</h2>
			<table  id='tieuptable' class='table-hover table-striped table-bordered job_notification_list' style="width:100%">
				<thead>
					<tr>
					    <th>Id</th>
		                <th>User Name</th>
					    <th>Job id</th>
					    <th>Title</th>
					    <th>Message</th>
					    <th>Url</th>
					    <th>Notifiedon</th>
					</tr>
				</thead>

			</table>
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
                        Select Job: <select class='form-control form-control-sm job_jobinfoid' name='job_jobinfoid' id='job_jobinfoid'>
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
                        Notification Subject: <input type="text" class="form-control form-control-sm job_notify_sub" name="job_notify_sub" placeholder="Notification Subject" id="job_notify_sub" >        
                    </div>
                    <div class="form-group"> 
                        Notification Message: <textarea cols='70' rows='9' class='job_notify_message  form-control form-control-sm' id="job_notify_message"></textarea> 
                    </div>

                    <div class="form-group">
                        URL: <input type="text" class="form-control form-control-sm job_notify_url" name="job_notify_url" placeholder="Notification Url" id="job_notify_url" >      
                    </div>

                    <div class="form-group">
                        <center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
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
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-info job_notification_add">Send</button>
            </div>
        </div>
    </div>
</div>
		

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){

	$('.job_notification_list').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :"job_notification_list_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".job_notification_list_processing").css("display","none");
            }
        }
    });

});

$('.job_notification_add').click(function(){                        //job_notification_add click
    $(this).attr("disabled",true);
    $('.loading_img').show();
    var jobinfoid = $('.job_jobinfoid').val();
    var job_notify_sub = $('.job_notify_sub').val();
    var job_notify_message = $('.job_notify_message').val();
    var job_notify_url = $('.job_notify_url').val();
    var task = "add_job_notification";

    if(jobinfoid == '0')
    {
		$(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please select job.</div>");
        return false;
    }

    if(job_notify_sub == '' || job_notify_sub == null)
    {
		$(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification subject</div>");
        return false;
    }

    if(job_notify_message == '' || job_notify_message == null)
    {
		$(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.job_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification message</div>");
        return false;
    }


    $.ajax({
        type : 'post',
        url : 'notification_helper.php',
        data : 'jobinfoid='+jobinfoid+'&job_notify_sub='+job_notify_sub+'&job_notify_message='+job_notify_message+'&job_notify_url='+job_notify_url+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
        success : function(res)
        {
            $(this).attr("disabled",false);
            $('.loading_img').hide();
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
</body>
</html>
