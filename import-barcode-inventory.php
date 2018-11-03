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
	$errorMessage1 = '';
	$successMessage1 = '';
	if (isset($_POST['submit'])) 
	{
		$i=0; //so we skip first row
		$file_ext = null;

		$filename = $_FILES['fileToUpload']['name'];
		if(empty($filename))
		{
			$errorMessage = 'Please select barcode inventory file to upload';        //error if file not selected
		}	
		else
		{
			$file_ext=strtolower(end(explode('.',$_FILES['fileToUpload']['name'])));

			if($file_ext!='csv')
			{
				$errorMessage = "Only csv files can be uploaded! Download sample file for your refrence!<br/>
					To download sample file <a href='sample_csv/4_sample_barcode_inventory_master [inventorymaster].csv' style='color:blue;'> Click here</a>";
			}
			else
			{
				$handle = fopen($_FILES['fileToUpload']['tmp_name'], "r");

				$i=0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
				{
					$i++;
					if($i==1) continue;

					$sql = "INSERT INTO inventorymaster(barcodeinfoid,barcode,locationid,type) VALUES($data[0],'$data[1]','$data[2]','$data[3]')";
					$result = pg_query($conn, $sql);

					if (!$result)
					{
						$errorMessage = 'Error in executing query';
					}
					else
					{
						$successMessage = 'Barcode inventory file data imported successfully to database';
					}
					//break;
				}
				fclose($handle);
				pg_close($conn);
			}
		}
	}

	if (isset($_POST['submit1'])) 
	{
		$i=0; //so we skip first row
		$file_ext = null;

		$filename = $_FILES['fileToUpload1']['name'];
		if(empty($filename))
		{
			$errorMessage1 = 'Please select visit info file to upload';        //error if file not selected
		}	
		else
		{
			$file_ext=strtolower(end(explode('.',$_FILES['fileToUpload1']['name'])));

			if($file_ext!='csv')
			{
				$errorMessage1 = "Only csv files can be uploaded! Download sample file for your refrence!<br/>
					To download sample file <a href='sample_csv/5_sample_default_inventory_captured [visitinfo].csv' style='color:blue;'> Click here</a>";
			}
			else
			{
				$handle = fopen($_FILES['fileToUpload1']['tmp_name'], "r");

				$i=0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
				{
					$i++;
					if($i==1) continue;

					if($data[0]=='')
					{
						$data[0]='NULL';
					}
					if($data[11]=='')
					{
						$data[11]='NULL';
					}
					if($data[12]=='')
					{
						$data[12]='NULL';
					}
					if($data[15]=='')
					{
						$data[15]='NULL';
					}
					if($data[16]=='')
					{
						$data[16]='NULL';
					}
					if($data[17]=='')
					{
						$data[17]='NULL';
					}
					if($data[18]=='')
					{
						$data[18]='NULL';
					}
					if($data[19]=='')
					{
						$data[19]='NULL';
					}
					if($data[20]=='')
					{
						$data[20]='NULL';
					}
					if($data[27]=='')
					{
						$data[27]='NULL';
					}
					if($data[28]=='')
					{
						$data[28]='NULL';
					}
					if($data[30]=='')
					{
						$data[30]='NULL';
					}
					if($data[32]=='')
					{
						$data[32]='NULL';
					}
					if($data[34]=='')
					{
						$data[34]='NULL';
					}
					if($data[35]=='')
					{
						$data[35]='NULL';
					}

					$sql = "INSERT INTO visitinfo(jobinfoid,scanneritemone,scanneritemtwo,scanneritemthree,scanneritemfour,descriptionone,descriptiontwo,descriptionthree,descriptionfour,descriptionfive,descriptionsix,dateone,datetwo,dropdownone,dropdowntwo,level1termid,level2termid,level3termid,level4termid,level5termid,level6termid,scanneroneimageid,scannertwoimageid,scannerthreeimageid,scannerfourimageid,genimageoneid,genimagetwoid,barcodeinfoid,ispartialverified,scanneritemvalue,isrejected,rfrejection,rejectedon,linkguid,approvedon,approvedtype) VALUES($data[0],'$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]',$data[11],$data[12],'$data[13]','$data[14]',$data[15],$data[16],$data[17],$data[18],$data[19],$data[20],'$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]',$data[27],$data[28],'$data[29]',$data[30],'$data[31]',$data[32],'$data[33]',$data[34],$data[35])";
					$result = pg_query($conn, $sql);

					if (!$result)
					{
						$errorMessage1 = 'Error in executing query';
					}
					else
					{
						$successMessage1 = 'visit info file data imported successfully to database';
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
<title>Import Barcode Inventory</title>

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
                                <a href="barcode-inventory-master.php"style="color:blue;text-align:right;" class="nav-link">Barcode Inventory Master</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
			<h3>Import Barcode Inventory</h3>
			<?php
				$sql = "SELECT SUM(last_value+1) FROM seqinventorymaster";
				$result = pg_query($conn, $sql);
				$row_next_barcodeinfoid = pg_fetch_array($result);
				$next_barcodeinfoid = $row_next_barcodeinfoid[0];
			?>	
			<form  method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
		    
			    For sample csv file <a href='sample_csv/4_sample_barcode_inventory_master [inventorymaster].csv' style='color:red;'><b>click here</b></a><br/>
			    Please start <b style="color:red;">barcodeinfoid</b> from <strong style="color:red;"><?php echo $next_barcodeinfoid;?></strong>
			    <br/></br>
			    <table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>
			    	<tr>
			    		<td>
			    			<input type="file" name="fileToUpload" id="fileToUpload"  class='form-control form-control-sm'>
			    		</td>
			    		
			    		<td>
			    			<input type="submit" value="Upload Barcode Info" name="submit" class='btn btn-sm btn-info btn_barcode_master'>
			    		</td>
			    		<td rowspan="2">
			    			<center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:50px;height:50px;display:none;'/></center>
			    		</td>
			    	</tr>	
			    </table>
			</form>
			<?php

				if ($errorMessage != '')
				{
					echo "<div class='alert alert-danger' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Error!</strong> $errorMessage";
					echo "</div>";
				}

				if ($successMessage != '')
				{
					echo "<div class='alert alert-success' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Success!</strong> $successMessage";
					echo "</div>";
				}

				?>
			</div>
			<div  class="col-md-3">	
			</div>
		</div>
		<hr/>
		<div  class="col-md-12">
			<div  class="col-md-3">
			</div>
			<div  class="col-md-6">
			<h3>Import Visit Info</h3>
			<form  method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
		    
			    For sample csv file <a href='sample_csv/5_sample_default_inventory_captured [visitinfo].csv' style='color:red;'><b>click here</b></a></br>
			    <br/></br>
			    <table class='table table-bordered table-responsive table-condensed table-scroll table-fixed-header'  cellspacing='0' cellspacing='0' width='100%'>
			    	<tr>
			    		<td>
			    			<input type="file" name="fileToUpload1" id="fileToUpload1"  class='form-control form-control-sm'>
			    		</td>
			    		<td>
			    			<input type="submit" value="Upload Visit Info" name="submit1" class='btn btn-sm btn-info btn_visit_info'>
			    		</td>
			    		<td rowspan="2">
			    			<center><img src="images/loading.gif" class='img-responsive loading_img1' id='loading_img1' style='widht:50px;height:50px;display:none;'/></center>
			    		</td>
			    	</tr>
			    	
			    </table>
			</form>
			<?php

				if ($errorMessage1 != '')
				{
					echo "<div class='alert alert-danger error_status1' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Error!</strong> $errorMessage1";
					echo "</div>";
				}

				if ($successMessage1 != '')
				{
					echo "<div class='alert alert-success success_status1' style='padding: 10px; margin-bottom: 10px;'>";
					echo "<strong>Success!</strong> $successMessage1";
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
		$('.btn_barcode_master').click(function(){
			$('.error_status').hide();
			$('.success_status').hide();
			$('.loading_img').show();
		});

		$('.btn_visit_info').click(function(){
			$('.error_status1').hide();
			$('.success_status1').hide();
			$('.loading_img1').show();
		});
	});
</script>
