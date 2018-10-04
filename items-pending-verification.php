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
	if(isset($_POST['export_data']))
	{
		include 'php/config.php';

		$conn = pg_connect($conn_string);

		if(!$conn)
		{
		    echo "ERROR : Unable to open database";
		    exit;
		}
		$jobinfoid = $_POST['jobinfoid'];
		$status_items = $_POST['status_items'];
		if($_POST['start_date']=='' || $_POST['start_date']==null)
		{
			$start_date = '0';
		}
		else
		{
			$start_date = $_POST['start_date'];
		}

		if($_POST['end_date']=='' || $_POST['end_date']==null)
		{
			$end_date = '0';
		}
		else
		{
			$end_date = $_POST['end_date'];
		}
		
		
		$where = '';

		$sql = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.scanneritemtwo, V.scannertwoimageid, V.scanneritemthree, V.scannerthreeimageid, V.scanneritemfour, V.scannerfourimageid, V.descriptionone, V.genimageoneid, V.descriptiontwo, V.genimagetwoid, V.descriptionthree, V.descriptionfour, V.descriptionfive, V.descriptionsix, V.dateone, V.datetwo, V.dropdownone, V.dropdowntwo, V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, D5.term AS term5, V.capturedon
			FROM visitinfo AS V
			INNER JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
			INNER JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
			INNER JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
			INNER JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
			INNER JOIN dropdownmaster AS D5 ON V.level5termid=D5.termid
			INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
			INNER JOIN location AS L ON J.locationid=L.locationid WHERE 1 = 1 ";

		if($jobinfoid=='0')
		{
		    $where .= "";
		}
		else
		{
		    $where .=" AND V.jobinfoid = '".$jobinfoid."' ";
		}

		if($status_items == '1')
		{
		    $where .=" AND V.barcodeinfoid IS null AND V.isrejected='0' ";
		}
		else
		if($status_items == '2')
		{
		    $where .=" AND V.barcodeinfoid IS NOT null AND V.isrejected='0' ";
		    if($start_date!='0') 
		    { 
		        $where .=" AND V.approvedon >= '".$start_date."' ";
		    }

		    if($end_date!='0') 
		    { 
		        $where .=" AND V.approvedon <= '".$end_date." 23:59:59' ";
		    }
		}   
		else
		if($status_items == '3')
		{
		    $where .=" AND V.isrejected='1' AND V.approvedtype='0' ";
		    if($start_date!='0') 
		    { 
		        $where .=" AND V.rejectedon >= '".$start_date."' ";
		    }

		    if($end_date!='0') 
		    { 
		        $where .=" AND V.rejectedon <= '".$end_date." 23:59:59' ";
		    }
		}
		else
		if($status_items == '4')
		{
		    $where .= "";
		}

		$where .= " ORDER BY V.visitinfoid";
		$sql .= $where;

		$result1 = pg_query($conn, $sql);

		if (!$result1)
		{
		    echo "ERROR : " . pg_last_error($conn);
		    exit;
		}

		if(pg_num_rows($result1) > 0){
		    $delimiter = ",";
		    $filename = "item_verification_data_" . date('Y-m-d') . ".csv";
		    
		    //create a file pointer
		    $f = fopen('php://memory', 'w');
		    
		    //set CSV column headers
		    $fields = array('Item ID', 'Asset Barcode', 'Asset Image 1', 'Asset Image 2', 'Site Code', 'Site Name', 'Job ID', 'Job Code', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5', 'Serial', 'Serial Image', 'Model', 'Model Image', 'Barcode', 'Barcode Image', 'Scanner 4 Value', 'Scanner 4 Image', 'L3 Other', 'L4 Other', 'L5 Other', 'RT/PS', 'Gen Notes', 'Description 6', 'Date 1', 'Date 2', 'Item Status', 'Condition', 'Partially Verified?', 'Is Rejected?', 'Reject Reason', 'Rejected On', 'Approved Type', 'Approved On', 'Captured On');
		    fputcsv($f, $fields, $delimiter);
		    
		    //output each row of the data, format line as csv and write to file pointer
		    while($row = pg_fetch_array($result1)){
		    	if($row['ispartialverified']=='1')
		    	{
		    		$ispartialverified = 'Yes';
		    	}
		    	else
		    	{
		    		$ispartialverified = 'No';
		    	}

		    	if($row['isrejected']=='1')
		    	{
		    		$isrejected = 'Rejected';
		    	}
		    	else
		    	{
		    		$isrejected = '';
		    	}

		    	if($row['isrejected']=='0' && $row['approvedtype']=='1' && ($row['barcodeinfoid']!='' || $row['barcodeinfoid']!= null))
		    	{
		    		$approvedtype = 'Manual';
		    	}
		    	else if($row['isrejected']=='0' && $row['approvedtype']=='0' && ($row['barcodeinfoid']!='' || $row['barcodeinfoid']!= null))
		    	{
		    		$approvedtype = 'Bulk';
		    	}
		    	else if($row['isrejected']=='0')
		    	{
		    		$approvedtype = 'Pending';
		    	}

		    	if($row['dateone']!='')
				{
					$dateone = $row['dateone'];
					$dateone = date('Y-m-d H:i:s', strtotime($dateone)); 
				}
				else
				{
					$dateone = "";
				}  

				if($row['datetwo']!='')
				{
					$datetwo = $row['datetwo'];
					$datetwo = date('Y-m-d H:i:s', strtotime($datetwo)); 
				}
				else
				{
					$datetwo = "";
				} 

				if($row['rejectedon']!='')
				{
					$rejectedon = $row['rejectedon'];
					$rejectedon = date('Y-m-d H:i:s', strtotime($rejectedon)); 
				}
				else
				{
					$rejectedon = "";
				}  

				if($row['approvedon']!='')
				{
					$approvedon = $row['approvedon'];
					$approvedon = date('Y-m-d H:i:s', strtotime($approvedon)); 
				}
				else
				{
					$approvedon = "";
				} 

				if($row['capturedon']!='')
				{
					$capturedon = $row['capturedon'];
					$capturedon = date('Y-m-d H:i:s', strtotime($capturedon)); 
				}
				else
				{
					$capturedon = "";
				} 

				$genimageoneid = '';
				if ($row['genimageoneid'] != '')
					$genimageoneid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['genimageoneid'].'.jpg';

				$genimagetwoid = '';
				if ($row['genimagetwoid'] != '')
					$genimagetwoid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['genimagetwoid'].'.jpg';

				$scanneroneimageid = '';
				if ($row['scanneroneimageid'] != '')
					$scanneroneimageid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['scanneroneimageid'].'.jpg';

				$scannertwoimageid = '';
				if ($row['scannertwoimageid'] != '')
					$scannertwoimageid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['scannertwoimageid'].'.jpg';

				$scannerthreeimageid = '';
				if ($row['scannerthreeimageid'] != '')
					$scannerthreeimageid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['scannerthreeimageid'].'.jpg';

				$scannerfourimageid = '';
				if ($row['scannerfourimageid'] != '')
					$scannerfourimageid = 'https://ihsavstorage.blob.core.windows.net/fileserverdata/'.$row['scannerfourimageid'].'.jpg';

				$lineData = array(
					''.$row['visitinfoid'].'',
					''.$row['scanneritemvalue'].'',
					''.$genimageoneid.'',
					''.$genimagetwoid.'',
					''.$row['sitecode'].'',
					''.$row['sitename'].'',
					''.$row['jobinfoid'].'',
					''.$row['jobno'].'',
					''.$row['term1'].'',
					''.$row['term2'].'',
					''.$row['term3'].'',
					''.$row['term4'].'',
					''.$row['term5'].'',
					''.$row['scanneritemone'].'',
					''.$scanneroneimageid.'',
					''.$row['scanneritemtwo'].'',
					''.$scannertwoimageid.'',
					''.$row['scanneritemthree'].'',
					''.$scannerthreeimageid.'',
					''.$row['scanneritemfour'].'',
					''.$scannerfourimageid.'',
					''.$row['descriptionone'].'',
					''.$row['descriptiontwo'].'',
					''.$row['descriptionthree'].'',
					''.$row['descriptionfour'].'',
					''.$row['descriptionfive'].'',
					''.$row['descriptionsix'].'',
					''.$dateone.'',
					''.$datetwo.'',
					''.$row['dropdownone'].'',
					''.$row['dropdowntwo'].'',
					''.$ispartialverified.'',
					''.$isrejected.'',
					''.$row['rfrejection'].'',
					''.$rejectedon.'',
					''.$approvedtype.'',
					''.$approvedon.'',
					''.$capturedon.''
				);
		        fputcsv($f, $lineData, $delimiter);
		    }
		    
		    //move back to beginning of file
		    fseek($f, 0);
		    
		    //set headers to download file rather than displayed
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename="' . $filename . '";');
		    
		    //output all remaining data on a file pointer
		    fpassthru($f);
		}
		exit;
	}
?>
<html>
<head>
<title>Item Verification</title>

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

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">

			</ul>
                    </div>
                </div>
            </nav>  

			<div class='col-md-12'>
			<h3>Item Verification</h3>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<div class='row'>
					<div class="col-md-2">
			        <?php
			        	$query1 = "SELECT J.*,L.sitecode FROM jobinfo as J JOIN location as L ON J.locationid=L.locationid ORDER BY J.jobinfoid ";
						$result1 = pg_query($conn, $query1);

						if (!$result1)
						{
							echo "ERROR : " . pg_last_error($conn);
							exit;
						}
			        ?>
						<label>
						Jobs: 
			            <select name='jobinfoid' class='jobinfoid form-control form-control-sm' style='width:150px;' data-column="0">
							<option value='0' selected>Jobs</option>
							<?php
								while($row1 = pg_fetch_array($result1))
								{
									echo "<option value='".$row1['jobinfoid']."'>".$row1['jobno']." [Sitecode - ".$row1['sitecode']."]</option>";
								}

							?>
						</select>
						</label>
			        	</div>
			        
			        	<div class="col-md-4">
						Status:<br/>
						<label><input type='radio' name='status_items' class='status_items' value='1' data-column="1"> Pending Approval</label>
						<label><input type='radio' name='status_items' class='status_items' value='2' data-column="1"> Approved Items</label>
						<label><input type='radio' name='status_items' class='status_items' value='3' data-column="1"> Rejected Items</label>
						<label><input type='radio' name='status_items' class='status_items' value='4' data-column="1" checked > All</label>
					</div>
					<div class="col-md-2">
						<label>
						Approval/Rejection Start Date: <input class="form-control form-control-sm start_date" id="start_date" name="start_date" placeholder="YYYY-MM-DD" type="text" data-column="2"/>
						</label>
					</div>
					<div class="col-md-2">
						<label>
						Approval/Rejection End Date: <input class="form-control form-control-sm end_date" id="end_date" name="end_date" placeholder="YYYY-MM-DD" type="text" data-column="3"/>
						</label>
					</div>
					<div class="col-md-2">
						<input class='btn btn-sm btn-success' value='Export Result' name='export_data' type='submit'>
					</div>
				</div>
			</div>
		</form>
        <div  class="col-md-12">
            <table  id='tieuptable' class='table-hover table-striped table-bordered items_pending_list' style="width:100%">
                <thead>
                    <tr>
        				<th>Item Id</th>
                    	<th>Asset Barcode</th>
						<th>Asset Image 1</th>
						<th>Asset Image 2</th>
						<th>Site Code</th>
						<th>Site Name</th>
						<th>Job Id</th>
						<th>Job Code</th>
						<th>Level 1</th>
						<th>Level 2</th>
						<th>Level 3</th>
						<th>Level 4</th>
						<th>Level 5</th>
						<th>Serial</th>
						<th>Serial Image</th>
						<th>Model</th>
						<th>Model Image</th>
						<th>Barcode</th>
						<th>Barcode Image</th>
						<th>Scanner 4 Value</th>
						<th>Scanner 4 Image</th>
						<th>L3 Other</th>
						<th>L4 Other</th>
						<th>L5 Other</th>
						<th>RT/PS</th>
						<th>Gen Notes</th>
						<th>Description 6</th>
						<th>Date 1</th>
						<th>Date 2</th>
						<th>Item Status</th>
						<th>Condition</th>
						<th>Partially Verified?</th>
						<th>Is Rejected?</th>
						<th>Reject Reason</th>
						<th>Rejected On</th>
						<th>Approved Type</th>
						<th>Approved On</th>
						<th>Approve</th>
						<th>Reject</th>
						<th>Captured On</th>
                    </tr>
                </thead>

            </table>
        </div>
			
	</div>
		
<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
	
	var dataTable = $('.items_pending_list').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "dom": 	"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "ajax":{
                url :"items_pending_list_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                //data: "jobinfoid=hello",
                error: function(){
                    $(".items_pending_list_processing").css("display","none");
                }
            }
        });
    
	$('.jobinfoid').on( 'change', function () {
		var i =$(this).attr('data-column');
	    var v =$(this).val();
	    dataTable.columns(i).search(v).draw();
	} );

	$('.status_items').on( 'change', function () {
		var i =$(this).attr('data-column');
	    var v =$(this).val();
		dataTable.columns(i).search(v).draw();
	} );

	$('.start_date').on( 'change', function (){
		var i =$('.start_date').attr('data-column');  // getting column index
		var v =$('.start_date').val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.end_date').on( 'change', function (){
		var i =$('.end_date').attr('data-column');  // getting column index
		var v =$('.end_date').val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.approve').click(function(){
		alert('hi');
		return false;
	});
	/*{
		var txt;
		var r = confirm("Are you really want to approve this item!");
		if (r == false) {
		    return false;
		} 
	}*/
		
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
				window.location.assign('items-pending-verification.php');
			}
		});
	});

});
</script>
</body>
</html>
