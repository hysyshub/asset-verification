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
<title>General Notifications</title>

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
                                <a href="#send_general_notification" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Send General Notification</a>
                            </li>
                            <li class="nav-item pull-left">
                                <a href="view_job_notification.php" style="color:blue;text-align:right;" class="nav-link">View Job Notification</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

		<div  class="col-md-12">
			<h2>General Notifications</h2>
			<table  id='tieuptable' class='table-hover table-striped table-bordered gen_notification_list' style="width:100%">
				<thead>
					<tr>
					    <th>Id</th>
					    <th>User Name</th>
					    <th>Title</th>
					    <th>Message</th>
					    <th>Url</th>
					    <th>Notifiedon</th>
					</tr>
				</thead>

			</table>
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
                        Notification Subject: <input type="text" class="form-control form-control-sm gen_notify_sub" name="gen_notify_sub" placeholder="Notification Subject" id="gen_notify_sub" >        
                    </div>
                    <div class="form-group"> 
                        Notification Message: <textarea cols='70' rows='9' class='gen_notify_message  form-control form-control-sm' id="gen_notify_message"></textarea> 
                    </div>

                    
                    <div class="form-group"> 
                        Users: <select class='form-control form-control-sm userid' name='userid[]' multiple='yes'   id="userid" multiple="multiple" style="width:200px;">
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
                        URL: <input type="text" class="form-control form-control-sm gen_notify_url" name="gen_notify_url" placeholder="Notification Url" id="gen_notify_url" >      
                    </div>

                    <div class="form-group">
                        <center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
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
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-info gen_notification_add">Send</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){

	$('.gen_notification_list').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :"gen_notification_list_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".gen_notification_list_processing").css("display","none");
            }
        }
    });

});

$('.gen_notification_add').click(function(){                        //gen_notification_add click
    $(this).attr("disabled",true);
    $('.loading_img').show();
    var gen_notify_sub = $('.gen_notify_sub').val();
    var gen_notify_message = $('.gen_notify_message').val();
    var gen_notify_url = $('.gen_notify_url').val();
    var task = "add_general_notification";
    var userid = $('.userid').val();
    
    if(gen_notify_sub == '' || gen_notify_sub == null)
    {
        $(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification subject</div>");
        return false;
    }

    if(gen_notify_message == '' || gen_notify_message == null)
    {
        $(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter notification message</div>");
        return false;
    }

    if(userid == '' || userid == null)
    {
        $(this).attr("disabled",false);
        $('.loading_img').hide();
        $('.gen_notification_status_error').html("<div class='alert alert-danger'><strong>Empty field</strong> Please select users.</div>");
        return false;
    }

    $.ajax({
        type : 'post',
        url : 'notification_helper.php',
        data : 'gen_notify_sub='+gen_notify_sub+'&gen_notify_message='+gen_notify_message+'&gen_notify_url='+gen_notify_url+'&userid='+userid+'&task='+task,
        success : function(res)
        {
            $(this).attr("disabled",false);
            $('.loading_img').hide();
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

</script>
</body>
</html>
