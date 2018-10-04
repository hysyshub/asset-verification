<?php 
	session_start();
	if($_SESSION['user']=='')
	{
		header('Location: login.php');
	}
	else
	{
	date_default_timezone_set('Asia/Calcutta');

	require_once('header.php');
	include 'php/config.php';
	
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "ERROR : Unable to open database";
		exit;
	}

	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "ERROR : Unable to open database";
		exit;
	}

	$query = "SELECT * FROM generalinfo";
	$result = pg_query($conn, $query);

	if (!$result)
	{
		echo "ERROR : " . pg_last_error($conn);
		exit;
	}

	$query2 = "SELECT * FROM generalinfo";
	$result2 = pg_query($conn, $query2);

	if (!$result2)
	{
		echo "ERROR : " . pg_last_error($conn);
		exit;
	}
?>



<!-- Page Content start -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style='width: 100%;'>
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
                                <a href="#edit_about_us" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Edit About Us</a>
                            </li>
                            <li class="nav-item">
                                <a href="#edit_contact_us" data-toggle='modal' style="color:blue;text-align:right;" class="nav-link">Edit Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> 
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<img src='images/av192x192.png' class="img-responsive" style='margin-right: auto;margin-left: auto;display: block;' /> 
			<h2 align='center'><strong> Welcome to Asset Verification System </strong></h2>
			<br/>
		</div>
	</div>

	<div class="row">
		<div class="col-md-1">
		</div>
		<div class="col-md-5">
			<?php
				echo "<table id='tieuptable' style='border: 1px solid #000;'>";
				echo "<thead>";
				echo "<tr>";
				    echo "<th>About Us</th>";
				echo "</tr>";
				echo "</thead>";
				while($row = pg_fetch_array($result))
				{
					echo "<tr>";
						echo "<td>" . $row['about'] . "</td>";
					echo "</tr>";
				}

				echo "</table>";

			?>
		</div>

		<div class="col-md-5">
			<?php
				echo "<table id='tieuptable' style='border: 1px solid #000;'>";
				echo "<thead>";
				echo "<tr>";
				    echo "<th>Contact Us</th>";
				echo "</tr>";
				echo "</thead>";
				while($row2 = pg_fetch_array($result2))
				{
					echo "<tr>";
						echo "<td>" . $row2['contactdetails'] . "</td>";
					echo "</tr>";
				}

				echo "</table>";

				

			?>
		</div>
	</div>

<br/>

<!-- Modal about us edit -->
<div class="modal fade" id="edit_about_us" role="dialog">
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
            	$query = "SELECT * FROM generalinfo";
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
                <h3 class="text-center">Edit About Us</h3>
                <form>
                	<div class="form-group">
			            <textarea cols='70' rows='12' class='edit_about' id="edit_about"><?php echo $row['about'];?></textarea>        
			        </div>
			         
			        <div class="form-group edit_about_status">
			                                
			        </div>
			        <div class="alert alert-success edit_about_success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>About us updated successfully!</div>
					</div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info btn_update_about_us">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal contact us edit -->
<div class="modal fade" id="edit_contact_us" role="dialog">
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
                <h3 class="text-center">Edit Contact Us</h3>
                <form>
                	<div class="form-group">
			            <textarea cols='70' rows='12' class='edit_contact' id='edit_contact'><?php echo $row['contactdetails'];?></textarea>        
			        </div>
			         
			        <div class="form-group edit_contact_status">
			                                
			        </div>
			        <div class="alert alert-success edit_contact_success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
					    <h5>Success</h5>
					    <div>Contact us updated successfully!</div>
					</div>
                </form>
            </div>
            
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info btn_update_contact_us">Update</button>
            </div>
        </div>
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
$('.edit_about').richText();
$('.edit_contact').richText();
$('.btn_update_about_us').click(function(){           //btn_update_about_us click
	var edit_about = $('.edit_about').val();
	var task = 'update_about_us';
	if(edit_about == '' || edit_about == null)
	{
		$('.edit_about_status').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter about us details</div>");
        return false;
	}
	$.ajax({
		type : 'post',
		url : 'index-update-helper.php',
		data : 'edit_about='+edit_about+'&task='+task,
		success : function(res)
		{
			if(res=='conn_error')
            {
                $('.edit_about_status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.edit_about_success_status').show();
                return false;
            }
            else
            {
                $('.edit_about_status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
		}
	});
});


$('.btn_update_contact_us').click(function(){           //btn_update_contact_us click
	var edit_contact = $('.edit_contact').val();
	var task = 'update_contact_us';
	if(edit_contact == '' || edit_contact == null)
	{
		$('.edit_contact_status').html("<div class='alert alert-danger'><strong>Empty field</strong> Please enter contact us details</div>");
        return false;
	}
	$.ajax({
		type : 'post',
		url : 'index-update-helper.php',
		data : 'edit_contact='+edit_contact+'&task='+task,
		success : function(res)
		{
			if(res=='conn_error')
            {
                $('.edit_contact_status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.edit_contact_success_status').show();
                return false;
            }
            else
            {
                $('.edit_contact_status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
		}
	});
});
</script>
