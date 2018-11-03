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
<title>Admins</title>

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
                        	<?php
					          	$superadmin = $_SESSION['superadmin']; 
					          	if($superadmin=='1')     // if admin is superadmin
					          	{	
					          		echo "<li class='nav-item'>";
					          		echo "<a href='#add_new_admin_info' data-toggle='modal' style='color:blue;text-align:right;' class='nav-link'>Add New Admin</a>";
					          		echo "</li>";
					          	}
					          ?>
					      
		            		<li class="nav-item">
                                <a href="userinfo.php" style="color:blue;text-align:right;"  class="nav-link">App Users</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  

        <div  class="col-md-12">
            <h3>Admins</h3>
            <table  id='tieuptable' class='table-hover table-striped table-bordered admin_list' style="width:100%">
                <thead>
                    <tr>
                        <th>Id</th>
						<th>First name</th>
						<th>Last Name</th>
						<th>Email-ID</th>
						<th>Address</th>
						<th>Contact</th>
						<th>Super Admin</th>
						<?php
							if($superadmin=='1')        // if admin is superadmin
						  	{
						  		echo "<th>Edit</th>";
						    	echo "<th>Change Password</th>";
						  	}
						?>
                    </tr>
                </thead>

            </table>
        </div>

	</div>
		
<!-- Modal New admininfo Addition -->
<div class="modal fade" id="add_new_admin_info" role="dialog">
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
                <h3 class="text-center">Add New Admin</h3>
                <form>
                	<div class="form-group">
                		<center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
                	</div>

			        <div class="form-group">
			            First Name: <input type="text" class="form-control form-control-sm firstname" name="firstname" placeholder="First Name" id="firstname" >   
			        </div> 
			        <div class="form-group">
			            Last Name: <input type="text" class="form-control form-control-sm lastname" name="lastname" placeholder="Last Name" id="lastname" >   
			        </div>
			        <div class="form-group">
			            Email id: <input type="email" class="form-control form-control-sm emailid" name="emailid" placeholder="Email Id" id="emailid" >   
			        </div>
			        <div class="form-group">
			            Address: <input type="text" class="form-control form-control-sm address" name="address" placeholder="Address" id="address" >   
			        </div>
			        <div class="form-group">
			            Contact Number: <input type="text" class="form-control form-control-sm contactnumber" name="contactnumber" placeholder="First Name" id="contactnumber" >   
			        </div>
			        <div class="form-group">
			        	Is Super Admin?
                      			<input type='checkbox' data-toggle='toggle' name='superadmin' class='superadmin' id='superadmin' data-on='On' data-off='Off' data-size="small">                      	
			        </div>				        
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success - Email Sent.</h5>
					    <div>New admin info added successfully!</div>
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

<?php include 'footer.php'; pg_close($conn);
}?>


<script>
$(document).ready(function(){
	
	$('.admin_list').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :"admin_list_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                    $(".admin_list_processing").css("display","none");
                }
            }
        });
	// add button click
	$('.btn_submit').click(function(){
		event.preventDefault();
		$('.loading_img').show();
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
		var task = 'add_admin_info';
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
				url : 'addition_helper.php',
				data : 'firstname='+firstname+'&lastname='+lastname+'&address='+address+'&contactnumber='+contactnumber+'&emailid='+emailid+'&superadmin='+superadmin+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
				success : function(res)
				{
					$('.loading_img').hide();
					if(res == 'duplicate')
					{
						$('.status').html("<div class='alert alert-danger'><strong>Duplicate record!</strong> Email id already in use. Please choose different email Id.</div>");
						return false;
					}
					else
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
						$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
						//$('.status').html("<div class='alert alert-danger'><strong>Query Failed!</strong> Something went wrong.</div>");
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
