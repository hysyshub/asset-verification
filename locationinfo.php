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
<title>Locations</title>

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
                                <a href="#add_new_location_info" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Add New Location</a>
                            </li>
                            <li class="nav-item">
                                <a href="import_locations.php" style="color:blue;text-align:right;" class="nav-link">Import Locations</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<h3>Locations</h3>
			<table  id='tieuptable' class='table-hover table-striped table-bordered location_list' style="width:100%">
				<thead>
					<tr>
					    <th>Id</th>
						<th>Site Code</th>
						<th>Site Name</th>
						<th>Longitude</th>
						<th>Lattitude</th>
						<th>Address</th>
						<th>City</th>
						<th>District</th>
						<th>Pin</th>
						<th>Circle</th>
						<th>Vendor</th>
						<th>Technician</th>
						<th>Technician Contact</th>
						<th>Supervisor</th>
						<th>Supervisor Contact</th>
						<th>Cluster</th>
						<th>Cluster Manager</th>
						<th>Cluster Contact</th>
						<th>Zone</th>
						<th>Zonal Manager</th>
						<th>Zonal Contact</th>
						<th>Edit</th>
					</tr>
				</thead>

			</table>
		</div>
	</div>
		
<!-- Modal New Location Addition -->
<div class="modal fade" id="add_new_location_info" role="dialog">
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
                <h2 class="text-center">Add New Location</h2>
                <form>
			        <div class="form-group">
			            Sitecode: <input type="text" class="form-control form-control-sm sitecode" name="sitecode" placeholder="Sitecode" id="sitecode" >   
			        </div> 
			        <div class="form-group">
			            Site Name: <input type="text" class="form-control form-control-sm sitename" name="sitename" placeholder="Site Name" id="sitename" >   
				</div> 
			        <div class="form-group">
	        		    Longitude: <input type="text" class="form-control form-control-sm longitude" name="longitude" placeholder="Longitude" id="longitude" >   
			        </div> 
	        		<div class="form-group">
			            Lattitude: <input type="text" class="form-control form-control-sm lattitude" name="lattitude" placeholder="Lattitude" id="lattitude" > 
	        		</div> 
			        <div class="form-group">
			            Address: <input type="text" class="form-control form-control-sm address" name="address" placeholder="Address" id="address" >   
			        </div> 
			        <div class="form-group">
			            City: <input type="text" class="form-control form-control-sm towncitylocation" name="towncitylocation" placeholder="City" id="towncitylocation" >   
			        </div> 
			        <div class="form-group">
			            District: <input type="text" class="form-control form-control-sm district" name="district" placeholder="District" id="district" >   
			        </div>
			        <div class="form-group">
			            Pin Code: <input type="text" class="form-control form-control-sm pincode" name="pincode" placeholder="Pin Code" id="pincode" >   
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
			        <div class="form-group">
			            Technician: <input type="text" class="form-control form-control-sm technician_name" name="technician_name" placeholder="Technician" id="technician_name" >   
			        </div>
			        <div class="form-group">
			            Technician Contact: <input type="text" class="form-control form-control-sm technician_contact" name="technician_contact" placeholder="Technician Contact" id="technician_contact" >   
			        </div> 
			        <div class="form-group">
			            Supervisior: <input type="text" class="form-control form-control-sm supervisor_name" name="supervisor_name" placeholder="Supervisior" id="supervisor_name" >   
			        </div> 
			        <div class="form-group">
			            Supervisior Contact: <input type="text" class="form-control form-control-sm supervison_contact" name="supervison_contact" placeholder="Supervisior Contact" id="supervison_contact" >   
			        </div> 
			        <div class="form-group">
			            Cluster: <input type="text" class="form-control form-control-sm cluster" name="cluster" placeholder="Cluster" id="cluster" >   
			        </div>
			        <div class="form-group">
			            Cluster Manager: <input type="text" class="form-control form-control-sm cluster_manager_name" name="cluster_manager_name" placeholder="Cluster Manager" id="cluster_manager_name" >   
			        </div>
			        <div class="form-group">
			            Cluster Contact: <input type="text" class="form-control form-control-sm cluster_manager_contact" name="cluster_manager_contact" placeholder="Cluster Contact" id="cluster_manager_contact" >   
			        </div>
			        <div class="form-group">
			            Zone: <input type="text" class="form-control form-control-sm zone" name="zone" placeholder="Zone" id="zone" >   
			        </div>
			        <div class="form-group">
			            Zonal Manager: <input type="text" class="form-control form-control-sm zonal_manager_name" name="zonal_manager_name" placeholder="Zonal Manager" id="zonal_manager_name" >   
			        </div>
			        <div class="form-group">
			            Zonal Contact: <input type="text" class="form-control form-control-sm zonal_manager_contact" name="zonal_manager_contact" placeholder="Zonal Contact" id="zonal_manager_contact" >   
			        </div>
			        <div class="form-group status">
			                                
			        </div>
			        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>New location added successfully!</div>
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
include 'footer.php'; }?>




<script>
$(document).ready(function(){

	$('.location_list').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
            url :"locationinfo_response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
                $(".location_list_processing").css("display","none");
            }
        }
    });
	// add button click
	$('.btn_submit').click(function(){                   //btn_submit click
		event.preventDefault();
		var sitecode = $('.sitecode').val();
		var sitename = $('.sitename').val();
		var longitude = $('.longitude').val();
		var lattitude = $('.lattitude').val();
		var address = $('.address').val();
		var towncitylocation = $('.towncitylocation').val();
		var district = $('.district').val();
		var pincode = $('.pincode').val();
		var circleinfoid = $('.circleinfoid').val();
		var vendorinfoid = $('.vendorinfoid').val();
		var technician_name = $('.technician_name').val();
		var technician_contact = $('.technician_contact').val();
		var supervisor_name = $('.supervisor_name').val();
		var supervison_contact = $('.supervison_contact').val();
		var cluster = $('.cluster').val();
		var cluster_manager_name = $('.cluster_manager_name').val();
		var cluster_manager_contact = $('.cluster_manager_contact').val();
		var zone = $('.zone').val();
		var zonal_manager_name = $('.zonal_manager_name').val();
		var zonal_manager_contact = $('.zonal_manager_contact').val();
		var task = 'add_location_info';
		if(sitecode=='' || sitecode==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter site code</div>");
			return false;
		}
		else
		if(sitename=='' || sitename==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter site name</div>");
			return false;
		}
		else
		if(longitude=='' || longitude==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter site longitude</div>");
			return false;
		}
		else
		if(lattitude=='' || lattitude==null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter site lattitude</div>");
			return false;
		}
		else
		if(circleinfoid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Please select Circle</div>");
			return false;
		}
		else
		if(vendorinfoid=='0')
		{
			$('.status').html("<div class='alert alert-danger'>Please select Vendor</div>");
			return false;
		}
		else
		{
			$.ajax({
				type : 'post',
				url : 'addition_helper.php',
				data : 'sitecode='+sitecode+'&sitename='+sitename+'&longitude='+longitude+'&lattitude='+lattitude+'&address='+address+'&towncitylocation='+towncitylocation+'&district='+district+'&pincode='+pincode+'&circleinfoid='+circleinfoid+'&vendorinfoid='+vendorinfoid+'&technician_name='+technician_name+'&technician_contact='+technician_contact+'&supervisor_name='+supervisor_name+'&supervison_contact='+supervison_contact+'&cluster='+cluster+'&cluster_manager_name='+cluster_manager_name+'&cluster_manager_contact='+cluster_manager_contact+'&zone='+zone+'&zonal_manager_name='+zonal_manager_name+'&zonal_manager_contact='+zonal_manager_contact+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
				success : function(res)
				{
					if(res == 'duplicate')
					{
						$('.status').html("<div class='alert alert-danger'><strong>Duplicate record!</strong> Location already exists.</div>");
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
						$('.status').html("<div class='alert alert-danger'><strong>Query Failed!</div> Something went wrong.");
						return false;
					}
				}
			});
		}
	});

	//edit icon clicked => to load data
	$('.edit_location_info').click(function(){                   //edit_location_info click
		var locationid = $(this).data('id');
		var task = 'location_info';
		$.ajax({
			type : 'post',
			url : 'get_edit_data_helper.php',
			data : 'locationid='+locationid+'&task='+task,
			success : function(res)
			{
				var result = res.split(',');
				$('.edit_locationid').attr("value",result[0]);
				$('.edit_sitecode').attr("value",result[1]);
				$('.edit_sitename').attr("value",result[2]);
				$('.edit_address').attr("value",result[3]);
				$('.edit_towncitylocation').attr("value",result[4]);
				$('.edit_district').attr("value",result[5]);
				$('.edit_pincode').attr("value",result[6]);
				$('.edit_circleinfoid').append("<option value='"+result[7]+"' selected>"+result[19]+"</option>");
				$('.edit_vendorinfoid').append("<option value='"+result[8]+"' selected>"+result[20]+"</option>");
				$('.edit_technician_name').attr("value",result[9]);
				$('.edit_technician_contact').attr("value",result[10]);
				$('.edit_supervisor_name').attr("value",result[11]);
				$('.edit_supervison_contact').attr("value",result[12]);
				$('.edit_cluster').attr("value",result[13]);
				$('.edit_cluster_manager_name').attr("value",result[14]);
				$('.edit_cluster_manager_contact').attr("value",result[15]);
				$('.edit_zone').attr("value",result[16]);
				$('.edit_zonal_manager_name').attr("value",result[17]);
				$('.edit_zonal_manager_contact').attr("value",result[18]);
			}
		});
	});


	//update button clicked
	$('.btn_update').click(function(){                   //btn_update click
		var edit_locationid = $('.edit_locationid').val();
		var edit_sitecode = $('.edit_sitecode').val();
		var edit_sitename = $('.edit_sitename').val();
		var edit_address = $('.edit_address').val();
		var edit_towncitylocation = $('.edit_towncitylocation').val();
		var edit_district = $('.edit_district').val();
		var edit_pincode = $('.edit_pincode').val();
		var edit_circleinfoid = $('.edit_circleinfoid').val();
		var edit_vendorinfoid = $('.edit_vendorinfoid').val();
		var edit_technician_name = $('.edit_technician_name').val();
		var edit_technician_contact = $('.edit_technician_contact').val();
		var edit_supervisor_name = $('.edit_supervisor_name').val();
		var edit_supervison_contact = $('.edit_supervison_contact').val();
		var edit_cluster = $('.edit_cluster').val();
		var edit_cluster_manager_name = $('.edit_cluster_manager_name').val();
		var edit_cluster_manager_contact = $('.edit_cluster_manager_contact').val();
		var edit_zone = $('.edit_zone').val();
		var edit_zonal_manager_name = $('.edit_zonal_manager_name').val();
		var edit_zonal_manager_contact = $('.edit_zonal_manager_contact').val();
		var task = 'update_location_info';

		if(edit_sitecode=='' || edit_sitecode==null)
		{
			$('.edit_status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter location/site code.</div>");
			return false;
		}
		else
		if(edit_sitename=='' || edit_sitename==null)
		{
			$('.edit_status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter location/site name.</div>");
			return false;
		}
		else
		{
			$.ajax({
			type : 'post',
			url : 'updation_helper.php',
			data : 'edit_locationid='+edit_locationid+'&edit_sitecode='+edit_sitecode+'&edit_sitename='+edit_sitename+'&edit_address='+edit_address+'&edit_towncitylocation='+edit_towncitylocation+'&edit_district='+edit_district+'&edit_pincode='+edit_pincode+'&edit_circleinfoid='+edit_circleinfoid+'&edit_vendorinfoid='+edit_vendorinfoid+'&edit_technician_name='+edit_technician_name+'&edit_technician_contact='+edit_technician_contact+'&edit_supervisor_name='+edit_supervisor_name+'&edit_supervison_contact='+edit_supervison_contact+'&edit_cluster='+edit_cluster+'&edit_cluster_manager_name='+edit_cluster_manager_name+'&edit_cluster_manager_contact='+edit_cluster_manager_contact+'&edit_zone='+edit_zone+'&edit_zonal_manager_name='+edit_zonal_manager_name+'&edit_zonal_manager_contact='+edit_zonal_manager_contact+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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

<?php
	pg_close($conn);
?>
</body>
</html>
