<?php 
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
	exit;
}
else
{
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

	$errorMessage = '';
	$successMessage = '';
	if (isset($_POST['submit'])) 
	{
	    $i=0; //so we skip first row
	    $file_ext = null;

	    $filename = $_FILES['fileToUpload']['name'];
	    if(empty($filename))
	    {
	    	$errorMessage = 'Please select file to upload';        //error if file not selected
	    }	
	    else
	    {
	    	$file_ext=strtolower(end(explode('.',$_FILES['fileToUpload']['name'])));
	    	
	    	if($file_ext!='csv')
	    	{
	    		$errorMessage = "Only csv files can be uploaded! Download sample file for your refrence!<br/>
	    			To download sample file <a href='sample_csv/2_sample_vendors [vendorinfo].csv' style='color:blue;'> Click here</a>";
	    	}
	    	else
	    	{
	    		$handle = fopen($_FILES['fileToUpload']['tmp_name'], "r");

				$i=0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
				{
				    $i++;
				    if($i==1) continue;

				    $sql = "INSERT INTO vendorinfo(vendorinfoid,vendorname) VALUES($data[0],'$data[1]')";
					$result = pg_query($conn, $sql);

					if (!$result)
					{
						$errorMessage = 'Error in executing query';
					}
					else
					{
						$successMessage = 'File data imported successfully to database';
					}
					//break;
				}
			    fclose($handle);
			    pg_close($conn);
	    	}
	    	
	    }
	    
	}
	

?>
<html>
<head>
<title>Upload New Vendors</title>

</head>
<body>
<?php

include 'header.php';
?>
<!-- Page Content  -->
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
                                <a href="vendorinfo.php" style="color:blue;text-align:right;" class="nav-link">Vendors List</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
		<h3>Import Vendors</h3>
		<?php
			$sql = "SELECT SUM(last_value+1) FROM seqvendorinfo";
			$result = pg_query($conn, $sql);
			$row_next_vendorinfo_id = pg_fetch_array($result);
			$next_vendorinfo_id = $row_next_vendorinfo_id[0];
		?>	
			<form  method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
		    
			    For sample csv file <a href='sample_csv/2_sample_vendors [vendorinfo].csv' style='color:red;'>click here</a><br>
			    Please start <b>circleinfoid</b> from <strong style="color:red;"><?php echo $next_vendorinfo_id;?></strong>
			    <br/><br/>
			    <table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>
			    	<tr>
			    		<td>
			    			<input type="file" name="fileToUpload" id="fileToUpload"  class='form-control form-control-sm'>
			    		</td>
			    		<td>
			    			<input type="submit" value="Upload File" name="submit" class='btn btn-sm btn-info submit_btn'>
			    		</td>
			    	</tr>
			    	
			    </table>
			    <center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
			</form>
			<?php

				if ($errorMessage != '')
				{
					echo "<div class='alert alert-danger error_status' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Error!</strong> $errorMessage";
					echo "</div>";
				}

				if ($successMessage != '')
				{
					echo "<div class='alert alert-success success_status' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Success!</strong> $successMessage";
					echo "</div>";
				}

				?>
			</div>
			<div  class="col-md-3">
			</div>
		</div>
	</div>
		


<?php include 'footer.php'; }?>

</body>
</html>

<script>
	$(document).ready(function(){
		$('.submit_btn').click(function(){
			$('.error_status').hide();
			$('.success_status').hide();
			$('.loading_img').show();
		});
	});
</script>
