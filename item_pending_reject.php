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
<title>Reject Item</title>

</head>
<body>
<?php

include 'header.php';
include 'php/config.php';

$conn = pg_connect($conn_string);

if(!$conn)
{
    echo "ERROR : Unable to open database";
    exit;
}
?>
<!-- Page Content start -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style='width:100%'>
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#6a1b9a;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse pull-right" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        	<li class="nav-item">
                                <a href="items-pending-verification.php" style="color:blue;text-align:right;" class="nav-link">Item Pending List</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <div  class="col-md-3">
            </div>
            <div  class="col-md-6">
        <h2>Reject Item</h2>
            <?php
				$visitinfoid = $_GET['visitinfoid'];
				$sql = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.scanneritemtwo, V.scannertwoimageid, V.scanneritemthree, V.scannerthreeimageid, V.scanneritemfour, V.scannerfourimageid, V.descriptionone, V.genimageoneid, V.descriptiontwo, V.genimagetwoid, V.descriptionthree, V.descriptionfour, V.descriptionfive, V.descriptionsix, V.dateone, V.datetwo, V.dropdownone, V.dropdowntwo, V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, D5.term AS term5 
						FROM visitinfo AS V
						INNER JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
						INNER JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
						INNER JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
						INNER JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
						INNER JOIN dropdownmaster AS D5 ON V.level5termid=D5.termid
						INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
						INNER JOIN location AS L ON J.locationid=L.locationid WHERE visitinfoid = '$visitinfoid'";

				$result = pg_query($conn, $sql);

				if (!$result)
				{
					echo "ERROR : " . pg_last_error($conn);
					exit;
				}
				$row = pg_fetch_array($result);
			?>
            <form>
                <input type="hidden" class="visitinfoid"  value="<?php echo $visitinfoid;?>">
		        <div class="form-group" style="border:1px solid blue;">
		            <h4>Reject item details </h4>
		            <b>Scanner code : </b> <?php echo $row['scanneritemvalue'];?>&nbsp;&nbsp;&nbsp;
		            <b>Sitecode : </b> <?php echo $row['sitecode'];?>&nbsp;&nbsp;&nbsp;
		            <b>Sitename : </b> <?php echo $row['sitename'];?><br/>
		            <b>Jobinfo Id : </b> <?php echo $row['jobinfoid'];?>&nbsp;&nbsp;&nbsp;
		            <b>Job code : </b> <?php echo $row['jobno'];?><br/>
		            <b>Level 1 : </b> <?php echo $row['term1'];?>&nbsp;&nbsp;&nbsp;
		            <b>Level 2 : </b> <?php echo $row['term2'];?>&nbsp;&nbsp;&nbsp;
		            <b>Level 3 : </b> <?php echo $row['term3'];?><br/>  
		            <b>Level 4 : </b> <?php echo $row['term4'];?>&nbsp;&nbsp;&nbsp;
		            <b>Level 5 : </b> <?php echo $row['term5'];?>&nbsp;&nbsp;&nbsp;<br/> 
		            <br/>  
		            
		            <?php
		            	// scanner item-1
		            	if ($row['scanneritemone'] != '')
		            	{
		            		echo "<b>Scanner value1 : </b> ".$row['scanneritemone'];
		            	}
		            	else
		            	{
		            		echo "<b>Scanner value1 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['scanneroneimageid'] != '')
						{
							echo "<b>Scanner Image-1 : </b>";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scanneroneimageid'] . ".jpg' title='" . $row['scanneroneimageid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scanneroneimageid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
		            	{
		            		echo "<b>Scanner Image-1:</b> Not Available";
		            	}
		            	echo "<br/>";
					// scanner item-2
						if ($row['scanneritemtwo'] != '')
		            	{
		            		echo "<b>Scanner value2 : </b> ".$row['scanneritemtwo'];
		            	}
		            	else
		            	{
		            		echo "<b>Scanner value2 : </b> Not available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['scannertwoimageid'] != '')
						{
							echo "Scanner Image-2:";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannertwoimageid'] . ".jpg' title='" . $row['scannertwoimageid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannertwoimageid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
		            	{
		            		echo "<b>Scanner Image-2:</b> Not Available";
		            	}
					?>
					<br>
		            <?php
		            // scanner item-3
		            	if ($row['scanneritemthree'] != '')
		            	{
		            		echo "<b>Scanner value3 : </b> ".$row['scanneritemthree'];
		            	}
		            	else
		            	{
		            		echo "<b>Scanner value3 : </b> Not available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['scannerthreeimageid'] != '')
						{
							echo "Scanner Image-3:";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannerthreeimageid'] . ".jpg' title='" . $row['scannerthreeimageid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannerthreeimageid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
		            	{
		            		echo "<b>Scanner Image-3: </b> Not Available";
		            	}
		            	echo "<br/>";
						// scanner item-4
		            	if ($row['scanneritemfour'] != '')
		            	{
		            		echo "<b>Scanner value4 : </b> ".$row['scanneritemfour'];
		            	}
		            	else
		            	{
		            		echo "<b>Scanner value4 : </b> Not available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['scannerfourimageid'] != '')
						{
							echo "<b>Scanner Image-4 : </b>";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannerfourimageid'] . ".jpg' title='" . $row['scannerfourimageid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scannerfourimageid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
		            	{
		            		echo "<b>Scanner Image-4:</b> Not Available";
		            	}
					?>
					<br>
		            <?php
		            // description item-1
		            	if ($row['descriptionone'] != '')
		            	{
		            		echo "<b>Description1 : </b> ".$row['descriptionone'];
		            	}
		            	else
		            	{
		            		echo "<b>Description1 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['genimageoneid'] != '')
						{
							echo "<b>Description-1 Image : </b>";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimageoneid'] . ".jpg' title='" . $row['genimageoneid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimageoneid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
						{
							echo "<b>Description1 Image: </b> Not Available";
						}
						echo "<br/>";
					// description item-2
		            	if ($row['descriptiontwo'] != '')
		            	{
		            		echo "<b>Description2 : </b> ".$row['descriptiontwo'];
		            	}
		            	else
		            	{
		            		echo "<b>Description2 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
			            if ($row['genimagetwoid'] != '')
						{
							echo "Description-2 Image:";
							echo "<a class='single_image' href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimagetwoid'] . ".jpg' title='" . $row['genimagetwoid'] . "'>";
							echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimagetwoid'] . ".jpg' height='50' />";
							echo "</a>";
						}
						else
						{
							echo "<b>Description2 Image: </b> Not Available";
						}
					?>
					<br/>
					<?php
					// description item-3
		            	if ($row['descriptionthree'] != '')
		            	{
		            		echo "<b>Description3 : </b> ".$row['descriptionthree'];
		            	}
		            	else
		            	{
		            		echo "<b>Description3 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
		            // description item-4
						if ($row['descriptionfour'] != '')
		            	{
		            		echo "<b>Description4 : </b> ".$row['descriptionfour'];
		            	}
		            	else
		            	{
		            		echo "<b>Description4 : </b> Not Available";
		            	}
		            	echo "<br/>";
		            // description item-5
		            	if ($row['descriptionfive'] != '')
		            	{
		            		echo "<b>Description5 : </b> ".$row['descriptionfive'];
		            	}
		            	else
		            	{
		            		echo "<b>Description5 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
		            // description item-6
						if ($row['descriptionsix'] != '')
		            	{
		            		echo "<b>Description6 : </b> ".$row['descriptionsix'];
		            	}
		            	else
		            	{
		            		echo "<b>Description6 : </b> Not Available";
		            	}
		            	echo "<br/>";
					?>
					<?php
					// date item-1
		            	if ($row['dateone'] != '')
		            	{
		            		echo "<b>Date 1 : </b> ".$row['dateone'];
		            	}
		            	else
		            	{
		            		echo "<b>Date 1 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
		            // date item-2
						if ($row['descriptionfour'] != '')
		            	{
		            		echo "<b>Date 2 : </b> ".$row['datetwo'];
		            	}
		            	else
		            	{
		            		echo "<b>Date 2 : </b> Not Available";
		            	}
		            	echo "<br/>";
		            ?>
		            <?php
					// dropdown item-1
		            	if ($row['dropdownone'] != '')
		            	{
		            		echo "<b>Dropdown 1 : </b> ".$row['dropdownone'];
		            	}
		            	else
		            	{
		            		echo "<b>Dropdown 1 : </b> Not Available";
		            	}
		            	echo "&nbsp;&nbsp;&nbsp;";
		            // dropdown item-2
						if ($row['descriptionfour'] != '')
		            	{
		            		echo "<b>Dropdown 2 : </b> ".$row['dropdowntwo'];
		            	}
		            	else
		            	{
		            		echo "<b>Dropdown 2 : </b> Not Available";
		            	}
		            	echo "<br/>";
		            ?>
		        </div>

		        <div class="form-group">
		            Remark/Reason for reject: <input type='text' class='form-control form-control-sm rfrejection' name='rfrejection' placeholder='Remark for reject item' id='rfrejection' >       
		        </div>
		        
		        <div class="form-group status">
		                                
		        </div>
		        <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">×</a>
				    <h5>Success</h5>
				    <div>Item Rejected successfully!</div>
				</div>

				<div>
	                <button type="button" class="btn btn-sm btn-danger update_submit">Reject</button>
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
	$('.update_submit').click(function(){                   //update_submit click
		var visitinfoid = $('.visitinfoid').val();
		var rfrejection = $('.rfrejection').val();
		var type = 'reject';
		
		if(rfrejection == '' || rfrejection == null)
		{
			$('.status').html("<div class='alert alert-danger'><strong>Empty field!</strong> Please enter reason to reject this item.</div>");
			return false;
		}
		$.ajax({
			type : 'get',
			url : 'items-pending-verification-helper.php',
			data : 'visitinfoid='+visitinfoid+'&rfrejection='+rfrejection+'&type='+type,
			success : function(res)
			{
				if(res == 'duplicate')
				{
					$('.status').html("<div class='alert alert-danger'><strong>Rejected item!</strong> Yhis item is rejected already.</div>");
					return false;
				}
				else
				if(res == 'success')
				{
					$('.success_status').show();
					window.setTimeout(function () {
					    $(".success_status").fadeTo(500, 0).slideUp(500, function () {
					        $(this).remove();
					    	window.close();  
					    	location.relode();  
					    });
					}, 2000);

				}
				else
				{
					$('.status').html("<div class='alert alert-danger'><strong>"+res+"</div>");
					//$('.status').html("<div class='alert alert-danger'><strong>Query Failed!</strong> Something went wrong.</div>");
					return false;
				}
			}
		});
	});
});
</script>
</body>
</html>