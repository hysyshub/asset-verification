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
<title>App Users</title>

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
                                <a href="#add_new_user_info" data-toggle='modal' style="color:blue;text-align:right;"  class="nav-link">Add New App User</a>
                            </li>
                            <li class="nav-item" >
                                <a href="admininfo.php" style="color:blue;text-align:right;"  class="nav-link">Admins</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  

        <div  class="col-md-12">
            <h3>App Users</h3>
            <table id='tieuptable' class='table-hover table-striped table-bordered user_list' style="width:100%">
                <thead>
                    <tr>
                 		<th>Id</th>
						<th>Email Id</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Contact</th>
						<th>Circle</th>
						<th>Vendor</th>
						<th>Device Info</th>
						<th>Token Id</th>
						<th>Longitude</th>
						<th>Lattitude</th>
						<th>Loggedin On</th>
						<th>Edit</th>
						<th>Change Password</th>
						<th>Reset User</th>
                    </tr>
                </thead>
            </table>
            <div class="form-group">
        		<center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
        	</div>
        </div>
	</div>
		
<!-- Modal New userinfo Addition -->
<div class="modal fade" id="add_new_user_info" role="dialog">
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
                <h3 class="text-center">Add New App User</h3>
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
			            Vendor info: <select class="form-control form-control-sm vendorinfoid">
			            <option value='0'>-- Select Vendor --</option>
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
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success - Email Sent.</h5>
					    <div>New user info added successfully!</div>
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


<?php 
	include 'footer.php'; 
	pg_close($conn);
	}
?>

<script>
$(document).ready(function(){
	
	 var table = $('.user_list').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "columnDefs": [ {
	            "targets": -1,
	            "data": null,
	            "defaultContent": "<span><button class='btn btn-sm btn-info'>Reset User</button><img src='images/loading.gif' width='24' height='24' style='display:none;' /></span>"
	        } ],
            "ajax":{
                url :"user_list_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                    $(".user_list_processing").css("display","none");
                },
            }
        });

	$('.user_list tbody').on( 'click', 'button', function () {
	var btnrow = table.row( $(this).parents('tr') );
	var btnrowidx = btnrow.index();
        var data = btnrow.data();
	var btnspan = $(this).parents('span');
	var btnloading = $(this).next();
        var userid = data[0];
        var useremailid = data[1];
        var task = "reset_app_user";
        var r = confirm("Do you want to reset the user " + userid + " # " + useremailid + "?");
		if (r == true) 
		{
			$(btnloading).css("display", "inline");
			$.ajax({
				type : 'post',
				url : 'updation_helper.php',
				data : 'userid='+userid+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
				success : function(res)
				{
					if(res == 'success')
					{
						table.cell(btnrowidx, 7).data('');
						table.cell(btnrowidx, 8).data('');
						table.cell(btnrowidx, 9).data('');
						table.cell(btnrowidx, 10).data('');
						table.cell(btnrowidx, 11).data('');
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
    } );

	// add button click
	$('.btn_submit').click(function(){                        //btn_submit click
		event.preventDefault();
		$('.loading_img').show();
		var firstname = $('.firstname').val();
		var lastname = $('.lastname').val();
		var emailid = $('.emailid').val();
		var address = $('.address').val();
		var contactnumber = $('.contactnumber').val();
		var circleinfoid = $('.circleinfoid').val();
		var vendorinfoid = $(".vendorinfoid").val();
		var task = 'add_user_info';
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
				data : 'firstname='+firstname+'&lastname='+lastname+'&address='+address+'&vendorinfoid='+vendorinfoid+'&emailid='+emailid+'&circleinfoid='+circleinfoid+'&contactnumber='+contactnumber+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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
