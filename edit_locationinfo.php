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

?>
<html>
<head>
<title>Edit Location</title>

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
                                <a href="locationinfo.php" style="color:blue;text-align:right;" class="nav-link">Locations</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-3">
            </div>
            <div  class="col-md-6">
        <h3>Edit Location</h3>
            <?php
				$locationid = quote_smart($_GET['locationid']);

				if (!is_numeric($locationid))
				{
					echo "ERROR : Invalid parameter value";
					exit;
				}

				$query = "SELECT * FROM location AS L, circleinfo AS C, vendorinfo AS V WHERE L.circleinfoid=C.circleinfoid AND L.vendorinfoid=V.vendorinfoid AND L.locationid='$locationid' ORDER BY L.locationid";

				$result = pg_query($conn, $query);
				
				if (!$result)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}	
				if(!pg_num_rows($result)>0)
				{
					echo "No record found : " ;
					exit;
				}
				$row = pg_fetch_array($result);
				
				/*$query2 = "SELECT COUNT(locationid) as count FROM jobinfo WHERE locationid='$locationid'";
				$result2 = pg_query($conn, $query2);
				
				if (!$result2)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}	
				if(!pg_num_rows($result2)>0)
				{
					echo "No record found : " ;
					exit;
				}
				$row2 = pg_fetch_array($result2);*/
				//echo $row2['count'];
			?>
            <form>
                 <input type="hidden" class="locationid"  value="<?php echo $locationid;?>">
	        <div class="form-group">
	            Site Code: <input type="text" class="form-control form-control-sm sitecode" name="sitecode"  id="edit_sitecode" value="<?php echo $row['sitecode'];?>" >   
	        </div> 
	        <div class="form-group">
	            Site Name: <input type="text" class="form-control form-control-sm sitename" name="sitename" id="sitename" value="<?php echo $row['sitename'];?>" >   
	        </div> 
	        <div class="form-group">
	            Longitude: <input type="text" class="form-control form-control-sm longitude" name="longitude" id="longitude" value="<?php echo $row['longitude'];?>" >   
	        </div> 
	        <div class="form-group">
	            Lattitude: <input type="text" class="form-control form-control-sm lattitude" name="lattitude" id="lattitude" value="<?php echo $row['lattitude'];?>" >   
	        </div> 
	        <div class="form-group">
	            Address: <input type="text" class="form-control form-control-sm address" name="address" id="address" value="<?php echo $row['address'];?>" >   
	        </div> 
	        <div class="form-group">
	            City: <input type="text" class="form-control form-control-sm towncitylocation" name="towncitylocation" id="towncitylocation" value="<?php echo $row['towncitylocation'];?>" >   
	        </div> 
	        <div class="form-group">
	            District: <input type="text" class="form-control form-control-sm district" name="district" id="district" value="<?php echo $row['district'];?>" >   
	        </div>
	        <div class="form-group">
	            Pin Code: <input type="text" class="form-control form-control-sm pincode" name="pincode" id="pincode" value="<?php echo $row['pincode'];?>" >   
	        </div>
	        <?php
	        	/*if($row2['count']>0)
	        	{
	        		echo "<div class='form-group'>
	            		Circle: <select class='form-control form-control-sm circleinfoid' disabled>
	            			<option selected value='".$row['circleinfoid']."'>".$row['circlevalue']."</option>";
	            		echo "</select><span style='color: red;'>Job already configured. Can't edit.</span>";
	            	echo "</div>";

	            	echo "<div class='form-group'>
	            		Vendor: <select class='form-control form-control-sm vendorinfoid' disabled>
	            			<option selected value='".$row['vendorinfoid']."'>".$row['vendorname']."</option>";
	            		echo "</select><span style='color: red;'>Job already configured. Can't edit.</span>";
	            	echo "</div>";
	        	}
	        	else
			{*/
	        ?>
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
					if ($row_circle_info['circleinfoid'] == $row['circleinfoid'])
						echo "<option value='".$row_circle_info['circleinfoid']."' selected>".$row_circle_info['circlevalue']."</option>";
					else
		            			echo "<option value='".$row_circle_info['circleinfoid']."'>".$row_circle_info['circlevalue']."</option>";
	            		}
	            		
	            	}
	            ?>
	            </select>   
	        </div>
	        
	        <div class="form-group">
	            Vendor: <select class="form-control form-control-sm vendorinfoid">
	            <option value='0'>-- Select user --</option>
	            <?php
	            	$sql_vendor_info = "SELECT * FROM vendorinfo ORDER BY vendorinfoid";
	            	$vendor_info_result = pg_query($conn, $sql_vendor_info);
	            	if(pg_num_rows($vendor_info_result)>0)
	            	{
	            		while($row_vendor_info = pg_fetch_array($vendor_info_result))
				{
					if ($row_vendor_info['vendorinfoid'] == $row['vendorinfoid'])
	            				echo "<option value='".$row_vendor_info['vendorinfoid']."' selected>".$row_vendor_info['vendorname']."</option>";
					else
		            			echo "<option value='".$row_vendor_info['vendorinfoid']."'>".$row_vendor_info['vendorname']."</option>";
	            		}
	            		
	            	}
	            ?>
	            </select>   
	        </div>
	        <?php
	        	//}
	        ?>
	        <div class="form-group">
	            Technician: <input type="text" class="form-control form-control-sm technician_name" name="technician_name" id="technician_name" value="<?php echo $row['technician_name'];?>" >   
	        </div>
	        <div class="form-group">
	            Technician Contact: <input type="text" class="form-control form-control-sm technician_contact" name="technician_contact" id="technician_contact" value="<?php echo $row['technician_contact'];?>" >   
	        </div> 
	        <div class="form-group">
	            Supervisor: <input type="text" class="form-control form-control-sm supervisor_name" name="supervisor_name" id="supervisor_name" value="<?php echo $row['supervisor_name'];?>" >   
	        </div> 
	        <div class="form-group">
	            Supervisor Contact: <input type="text" class="form-control form-control-sm supervison_contact" name="supervison_contact" id="supervison_contact" value="<?php echo $row['supervison_contact'];?>" >   
	        </div> 
	        <div class="form-group">
	            Cluster: <input type="text" class="form-control form-control-sm cluster" name="cluster" id="cluster" value="<?php echo $row['cluster'];?>" >   
	        </div>
	        <div class="form-group">
	            Cluster Manager: <input type="text" class="form-control form-control-sm cluster_manager_name" name="cluster_manager_name" id="cluster_manager_name" value="<?php echo $row['cluster_manager_name'];?>" >   
	        </div>
	        <div class="form-group">
	            Cluster Contact: <input type="text" class="form-control form-control-sm cluster_manager_contact" name="cluster_manager_contact" id="cluster_manager_contact" value="<?php echo $row['cluster_manager_contact'];?>" >   
	        </div>
	        <div class="form-group">
	            Zone: <input type="text" class="form-control form-control-sm zone" name="zone" id="zone" value="<?php echo $row['zone'];?>" >   
	        </div>
	        <div class="form-group">
	            Zonal Manager: <input type="text" class="form-control form-control-sm zonal_manager_name" name="zonal_manager_name" id="zonal_manager_name" value="<?php echo $row['zonal_manager_name'];?>" >   
	        </div>
	        <div class="form-group">
	            Zonal Contact: <input type="text" class="form-control form-control-sm zonal_manager_contact" name="zonal_manager_contact" id="zonal_manager_contact" value="<?php echo $row['zonal_manager_contact'];?>" >   
	        </div>
	        
	        <div class="form-group status">
	                                
	        </div>
	        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert"></a>
			    <h5>Success</h5>
			    <div>Location updated successfully!</div>
			</div>

			<div>
                <button type="button" class="btn btn-sm btn-info update_submit">Update</button>
            </div>
            </form>
            </div>
            <div  class="col-md-3">
            </div>
        </div>
    </div>
        

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
	
	// update button click
	$('.update_submit').click(function(){               // update_submit click
		event.preventDefault();
		var locationid = $('.locationid').val();
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
		var task = 'update_location_info';
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
			url : 'updation_helper.php',
			data : 'locationid='+locationid+'&sitecode='+sitecode+'&sitename='+sitename+'&longitude='+longitude+'&lattitude='+lattitude+'&address='+address+'&towncitylocation='+towncitylocation+'&district='+district+'&pincode='+pincode+'&circleinfoid='+circleinfoid+'&vendorinfoid='+vendorinfoid+'&technician_name='+technician_name+'&technician_contact='+technician_contact+'&supervisor_name='+supervisor_name+'&supervison_contact='+supervison_contact+'&cluster='+cluster+'&cluster_manager_name='+cluster_manager_name+'&cluster_manager_contact='+cluster_manager_contact+'&zone='+zone+'&zonal_manager_name='+zonal_manager_name+'&zonal_manager_contact='+zonal_manager_contact+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
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
