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
<title>Circles</title>

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
                                <a href="#add_new_circle_info" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Add New Circle</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

		<div  class="col-md-12">
			<h3>Circles</h3>
			<table id='tieuptable' class='table-hover table-striped table-bordered circle_list' style="width:100%">
				<thead>
					<tr>
					    <th>Id</th>
					    <th>Circle Code</th>
					    <th>Circle Name</th>
					    <th>Edit</th>
					</tr>
				</thead>

			</table>
		</div>
	</div>
		
<!-- Modal New Circle Addition -->
<div class="modal fade" id="add_new_circle_info" role="dialog">
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
                <h3 class="text-center">Add New Circle</h3>
                <form>
			        <div class="form-group">
			            Circle Code: <input type="text" class="form-control form-control-sm circlecode" name="circlecode" placeholder="Circle Code" id="emailid" >        
			        </div>
			        <div class="form-group">
			            Circle Name: <input type="text" class="form-control form-control-sm circlevalue" name="circlevalue" placeholder="Circle Name" id="circlevalue" >   
			        </div>  
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>New circle info added successfully!</div>
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


<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
	$('.circle_list').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :"circleinfo_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".circle_list_processing").css("display","none");
            }
        }
    });
	// add button click
	$('.btn_submit').click(function(){               // btn_submit click     
		event.preventDefault();
		var circlecode = $('.circlecode').val();
		var circlevalue = $('.circlevalue').val();
		var task = 'add_circle_info';
		if(circlecode=='' || circlecode==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle code.</div>");
			return false;
		}
		else
		if(circlevalue=='' || circlevalue==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle name.</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'addition_helper.php',
				data : 'circlecode='+circlecode+'&circlevalue='+circlevalue+'&task='+task,
				success : function(res)
				{
					if(res == 'duplicate')
					{
						$('.status').html("<div class='alert alert-danger'><strong>Duplicate record!</strong> Circle info already exists.</div>");
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
						return false;
					}
				}
			});
		}
	});

	//edit icon clicked => to load data
	$('.edit_circle_info').click(function(){
		var circleinfoid = $(this).data('id');
		var task = 'circle_info';
		$.ajax({
			type : 'post',
			url : 'get_edit_data_helper.php',
			data : 'circleinfoid='+circleinfoid+'&task='+task,
			success : function(res)
			{
				var result = res.split(',');
				$('.edit_circleinfoid').attr("value",result[0]);
				$('.edit_circlecode').attr("value",result[1]);
				$('.edit_circlevalue').attr("value",result[2]);
			}
		});
	});


	//update button clicked
	$('.btn_update').click(function(){
		var edit_circleinfoid = $('.edit_circleinfoid').val();
		var edit_circlecode = $('.edit_circlecode').val();
		var edit_circlevalue = $('.edit_circlevalue').val();
		var task = 'update_circle_info';

		if(edit_circlecode=='' || edit_circlecode==null)
		{
			$('.edit_status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle code.</div>");
			return false;
		}
		else
		if(edit_circlevalue=='' || edit_circlevalue==null)
		{
			$('.edit_status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter circle name.</div>");
			return false;
		}
		else
		{
			$.ajax({
			type : 'post',
			url : 'updation_helper.php',
			data : 'edit_circleinfoid='+edit_circleinfoid+'&edit_circlecode='+edit_circlecode+'&edit_circlevalue='+edit_circlevalue+'&task='+task,
			success : function(res)
			{
				if(res == 'success')
				{
					$('.edit_success_status').show();
					window.setTimeout(function () {
					    $(".edit_success_status").fadeTo(500, 0).slideUp(500, function () {
					        $(this).remove();
					    window.location.reload();    
					    });
					}, 5000);

				}
				else
				{
					$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
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
