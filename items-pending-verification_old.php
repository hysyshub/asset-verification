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
<title>Items Pending Verification</title>

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
<!-- Page Content  -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style='width:1100px'>
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#6a1b9a;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                        	<li class=" nav-item dropdown">
                            <a href="#" id="nbAcctDD" class="dropdown-toggle" data-toggle="dropdown"  style="background:transparent;color:black;">&nbsp;Hi&nbsp; <?php echo $_SESSION['user']; ?></a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="index.php"  class="user_event"><span class="glyphicon glyphicon-home"></span>&nbsp;Home</a>
                                </li>
                                <li>
                                    <a href="admin-self-change-password.php"  class="user_event"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Change Password</a>
                                </li>
                                <li>
                                    <a href="logout.php?logout" class="user_event"><span class="glyphicon glyphicon-off"></span>&nbsp;Sign Out</a>
                                </li>
                            </ul>
                        </li>
                        </ul>
                    </div>
                </div>
            </nav>  
		<div  class="col-md-12">
			
		<h2>Items Pending Verification</h2>
			
			Select Job:
		        <div>
		        <?php
		        	$query1 = "SELECT jobinfoid,jobno FROM jobinfo ORDER BY jobinfoid ";
					$result1 = pg_query($conn, $query1);

					if (!$result1)
					{
						echo "ERROR : " . pg_last_error($conn);
						exit;
					}
		        ?>
		            <select name='jobinfoid' class='col-md-2 jobinfoid form-control' style='width:200px;'>
						<option value='0'>All</option>
						<?php
							while($row1 = pg_fetch_array($result1))
							{
								echo "<option value='".$row1['jobinfoid']."'>".$row1['jobno']."</option>";
							}

						?>
					</select>
		        </div>
		        <div class="col=md-7 form-group">
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='radio' name='status_items' class='status_items' value='pending' checked> Pending Approval
				<input type='radio' name='status_items' class='status_items' value='approved'> Approved Items
				<input type='radio' name='status_items' class='status_items' value='rejected'> Rejected Items
				<input type='radio' name='status_items' class='status_items' value='all'> All
			</div>
			<div class="col=md-2 form-group">
				<input type='button' class='submit_filter btn btn-warning col-md-1' value='Search'/>
			</div>
      	
		<br/>
		<hr/>
		<?php
		$jobinfoid=$_GET['jobinfoid'];
		$status_items=$_GET['status_items'];

		$query = "SELECT V.visitinfoid, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.descriptionone, V.genimageoneid, V.dateone, V.dropdownone,V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4 
		FROM visitinfo AS V
		INNER JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
		INNER JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
		INNER JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
		INNER JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
		INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
		INNER JOIN location AS L ON J.locationid=L.locationid";

		if($jobinfoid == '0' && $status_items == 'all')
		{
			$query = $query;
		}
		
		if($jobinfoid == '0' && $status_items != 'all')
		{
			if($status_items=='pending')
			{
				$query = $query." WHERE V.jobinfoid > 0 and V.barcodeinfoid IS null ORDER BY V.visitinfoid DESC";
			}
			else
			if($status_items=='approved')
			{
				$query = "select * from visitinfo where barcodeinfoid is not null  ORDER BY visitinfoid DESC LIMIT 500";
			}
			else
			if($status_items=='rejected')
			{
				$query = $query." WHERE V.isrejected='1' ORDER BY V.visitinfoid DESC";
			}
		}
		
		if($jobinfoid != '0' && $status_items == 'all')
		{
			$query = $query." WHERE V.jobinfoid='$jobinfoid'";
		}
		
		if($jobinfoid != '0' && $status_items != 'all')
		{
			if($status_items=='pending')
			{
				$query = $query." WHERE V.jobinfoid > 0 and V.barcodeinfoid IS not null";
			}
			else
			if($status_items=='approved')
			{
				$query = $query." WHERE V.barcodeinfoid is not null";
			}
			else
			if($status_items=='rejected')
			{
				$query = $query." WHERE V.isrejected='1'";
			}

			$query = $query." AND V.jobinfoid='$jobinfoid' ORDER BY V.visitinfoid DESC";
		}
		
		$result = pg_query($conn, $query);

		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}

		
		echo "<h3 class='h4'>". pg_num_rows($result) . " item pending verifications found</h3>";

		echo "<table id='tieuptable' class='table table-bordered table-responsive table-condensed table-fixed-header myTable items_pending_list'>";
		echo "<thead>";
		echo "<tr>";
		    echo "<th>Item Id</th>";
			echo "<th>Site Code</th>";
			echo "<th>Site Name</th>";
			echo "<th>Job Id</th>";
			echo "<th>Job Code</th>";
			echo "<th>Level 1</th>";
			echo "<th>Level 2</th>";
			echo "<th>Level 3</th>";
			echo "<th>Level 4</th>";
			echo "<th>Scanner Value</th>";
			echo "<th>Scanner Image</th>";
			echo "<th>Description</th>";
			echo "<th>Item Image</th>";
			echo "<th>Date</th>";
			echo "<th>Dropdown</th>";
			echo "<th>Is partial</th>";
			echo "<th>Is Rejected</th>";
			echo "<th>Reject Remark</th>";
			echo "<th>Reject On</th>";
			echo "<th>Approved Type</th>";
			echo "<th>Approved On</th>";
			echo "<th colspan='2'>Approve/Reject</th>";
		echo "</tr>";
		echo "</thead>";
		while($row = pg_fetch_array($result))
		{
			echo "<tr>";
			echo "<td>" . $row['visitinfoid'] . "</td>";
			echo "<td>" . $row['sitecode'] . "</td>";
			echo "<td nowrap='true'>" . $row['sitename'] . "</td>";	
			echo "<td>" . $row['jobinfoid'] . "</td>";
			echo "<td nowrap='true'>" . $row['jobno'] . "</td>";	
			echo "<td>" . $row['term1'] . "</td>";
			echo "<td>" . $row['term2'] . "</td>";
			echo "<td>" . $row['term3'] . "</td>";
			echo "<td>" . $row['term4'] . "</td>";
			echo "<td>" . $row['scanneritemone'] . "</td>";
			echo "<td>";
			if ($row['scanneroneimageid'] != '')
			{
				echo "<a data-fancybox href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scanneroneimageid'] . ".jpg' title='" . $row['scanneroneimageid'] . "'>";
				echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['scanneroneimageid'] . ".jpg' height='50' />";
				echo "</a>";
			}
			echo "</td>";
			echo "<td>" . $row['descriptionone'] . "</td>";
			echo "<td>";
			if ($row['genimageoneid'] != '')
			{
				echo "<a data-fancybox href='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimageoneid'] . ".jpg' title='" . $row['scanneroneimageid'] . "'>";
				echo "<img src='https://fileserverdata.blob.core.windows.net/fileserverdata2/" . $row['genimageoneid'] . ".jpg' height='50' />";
				echo "</a>";
			}
			echo "</td>";
			echo "<td>" . $row['dateone'] . "</td>";
			echo "<td>" . $row['dropdownone'] . "</td>";
			echo "<td>" . $row['ispartialverified'] . "</td>";
			if($row['isrejected']=='1')
			{
				echo "<td>Rejected</td>";
			}
			else
			{
				echo "<td>-- NA --</td>";
			}
			echo "<td>" . $row['rfrejection'] . "</td>";
			echo "<td>" . $row['rejectedon'] . "</td>";
			if($row['approvedtype']=='1')
			{
				$approvedtype = 'Manual';
			}
			else
			if($row['approvedtype']=='0')
			{
				$approvedtype = 'Bulk';
			}
			echo "<td>" . $approvedtype . "</td>";
			echo "<td>" . $row['approvedon'] . "</td>";
			if($row['barcodeinfoid']!='' || $row['barcodeinfoid']!= null)
			{
				echo "<td> Approved </td>";
				echo "<td> Rejected </td>";
			}
			else
			if($row['isrejected']!='1')
			{
				echo "<td><a class='btn btn-info approve' href='items-pending-verification-helper.php?type=approve&visitinfoid=" . $row['visitinfoid'] . "' value='".$row['visitinfoid']."'>Approve</a></td>";
				echo "<td> <a class='btn btn-danger reject' href='#reject_".$row['visitinfoid']."' data-toggle='modal'>Reject</a>
				</td>";
			}
			else
			if($row['isrejected']=='1')
			{
				echo "<td><a class='btn btn-info approve' href='items-pending-verification-helper.php?type=approve&visitinfoid=" . $row['visitinfoid'] . "' value='".$row['visitinfoid']."'>Approve</a></td>";
				echo "<td> Rejected </td>";
			}
			
			
			echo "</tr>";
			echo "<div class='modal fade' id='reject_".$row['visitinfoid']."' role='dialog'>
		    <div class='modal-dialog'>
		        <div class='modal-content  col-md-12'>
		            <!-- Modal Header -->
		            <div class='modal-header'>
		                <button type='button' class='close' data-dismiss='modal'>
		                    <span aria-hidden='true'>&times;</span>
		                    <span class='sr-only'>Close</span>
		                </button>
		            </div>
		            
		            <!-- Modal reject item -->
		            <div class='modal-body'>
		                <h2 class='text-center'>Reject Item</h2>
		                <form>
					        <div class='form-group'>
					            Remark/Reason for reject: <input type='text' class='form-control rfrejection_".$row['visitinfoid']."' name='rfrejection' placeholder='Remark for reject item' id='rfrejection' >
					            <input type='hidden' class='form-control' id='visitinfoid_val_".$row['visitinfoid']."' value='".$row['visitinfoid']."' disabled>       
					        </div>
					        
		                </form>
		            </div>
		            
		            
		            <!-- Modal Footer -->
		            <div class='modal-footer'>
		                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
		                <button type='button' class='btn btn-danger btn_submit' value='".$row['visitinfoid']."'>Reject</button>
		            </div>
		        </div>
		    </div>";
		}

		echo "</table>";
		//echo $data;
		pg_close($conn);
		$script = "1";
		?>
	</div>		
	</div>
		
<?php include 'footer.php'; }?>

<script>
	$('.submit_filter').click(function(){
		var jobinfoid = $('.jobinfoid').val();
		var radio = document.getElementsByName('status_items');
		for (var i = 0, length = radio.length; i < length; i++)
		{
			if (radio[i].checked)
			{
				var status_items = radio[i].value;
				// only one radio can be logically checked, don't check the rest
				break;
			}
		}
		window.location.assign("items-pending-verification.php?jobinfoid="+jobinfoid+"&status_items="+status_items);
	});
	$('.approve').click(function(){
		var txt;
		var r = confirm("Are you really want to approve this item!");
		if (r == false) {
		    return false;
		} 
	});
	$('.btn_submit').click(function(){
		var visitinfoid = $(this).val();
		var rfrejection = $('.rfrejection_'+visitinfoid).val();
		var type = 'reject';
		
		$.ajax({
			type : 'get',
			url : 'items-pending-verification-helper.php',
			data : 'visitinfoid='+visitinfoid+'&rfrejection='+rfrejection+'&type='+type,
			success : function(res)
			{
				alert(res);
				window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=pending');
			}
		});
	});
</script>
</body>
</html>