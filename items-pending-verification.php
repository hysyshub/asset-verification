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
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

	if(isset($_POST['export_data']))
	{
		$jobinfoid = quote_smart($_POST['jobinfoid']);
		$status_items = quote_smart($_POST['status_items']);
		if(quote_smart($_POST['start_date'])=='' || quote_smart($_POST['start_date'])==null)
		{
			$start_date = '0';
		}
		else
		{
			$start_date = quote_smart($_POST['start_date']);
		}

		if(quote_smart($_POST['end_date'])=='' || quote_smart($_POST['end_date'])==null)
		{
			$end_date = '0';
		}
		else
		{
			$end_date = quote_smart($_POST['end_date']);
		}

		$where = '';

		$sql = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.scanneritemtwo, V.scannertwoimageid, V.scanneritemthree, V.scannerthreeimageid, V.scanneritemfour, V.scannerfourimageid, V.descriptionone, V.genimageoneid, V.descriptiontwo, V.genimagetwoid, V.descriptionthree, V.descriptionfour, V.descriptionfive, V.descriptionsix, V.dateone, V.datetwo, V.dropdownone, V.dropdowntwo, V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, D5.term AS term5, V.capturedon
			FROM visitinfo AS V
			LEFT JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
			LEFT JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
			LEFT JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
			LEFT JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
			LEFT JOIN dropdownmaster AS D5 ON V.level5termid=D5.termid
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
		else if($status_items == '2')
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
		else if($status_items == '3')
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
		else if($status_items == '4')
		{
		    $where .= "";
		}

		if (isset($_POST['datatable_search']) && quote_smart($_POST['datatable_search']) != '')
		{
			$dbtable_search = quote_smart($_POST['datatable_search']);

			$where .=" AND ( L.sitename ILIKE '%".$dbtable_search."%' ";
			$where .=" OR J.jobno ILIKE '%".$dbtable_search."%' )";
			/*$where .=" AND ( CAST(V.visitinfoid AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.scanneritemvalue ILIKE '%".$dbtable_search."%' ";    
			$where .=" OR L.sitecode ILIKE '%".$dbtable_search."%' ";
			$where .=" OR L.sitename ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(J.jobinfoid AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR J.jobno ILIKE '%".$dbtable_search."%' ";
			$where .=" OR D1.term ILIKE '%".$dbtable_search."%' ";
			$where .=" OR D2.term ILIKE '%".$dbtable_search."%' ";
			$where .=" OR D3.term ILIKE '%".$dbtable_search."%' ";
			$where .=" OR D4.term ILIKE '%".$dbtable_search."%' ";
			$where .=" OR D5.term ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.scanneritemone ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.scanneritemtwo ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.scanneritemthree ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.scanneritemfour ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptionone ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptiontwo ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptionthree ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptionfour ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptionfive ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.descriptionsix ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(V.dateone AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(V.datetwo AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.dropdownone ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.dropdowntwo ILIKE '%".$dbtable_search."%' ";
			$where .=" OR V.rfrejection ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(V.rejectedon AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(V.approvedon AS text) ILIKE '%".$dbtable_search."%' ";
			$where .=" OR CAST(V.capturedon AS text) ILIKE '%".$dbtable_search."%' )";*/
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
		    
                    //set headers to download file rather than displayed
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="' . $filename . '";');

                    // do not cache the file
                    header('Pragma: no-cache');
                    header('Expires: 0');

                    //create a file pointer
                    $f = fopen('php://output', 'w');

		    //set CSV column headers
		    $fields = array('Item ID', 'Asset Barcode', 'Asset Image 1', 'Asset Image 2', 'Site Code', 'Site Name', 'Job ID', 'Job Code', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5', 'Serial', 'Serial Image', 'Model', 'Model Image', 'Barcode', 'Barcode Image', 'Scanner 4 Value', 'Scanner 4 Image', 'Brand (Other)', 'Type, Capacity (Other)', 'Other Details (Other)', 'RT/PS', 'Gen Notes', 'Description 6', 'Date 1', 'Date 2', 'Item Status', 'Condition', 'Partially Verified?', 'Is Rejected?', 'Reject Reason', 'Rejected On', 'Approved Type', 'Approved On', 'Captured On');
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

		    	if($row['isrejected']=='0' && $row['approvedtype']=='2' && ($row['barcodeinfoid']!='' || $row['barcodeinfoid']!= null))
		    	{
		    		$approvedtype = 'Manual';
		    	}
		    	else if($row['isrejected']=='0' && $row['approvedtype']=='1' && ($row['barcodeinfoid']!='' || $row['barcodeinfoid']!= null))
		    	{
		    		$approvedtype = 'Bulk';
		    	}
		    	else if($row['isrejected']=='0' && $row['approvedtype']=='0')
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
					$genimageoneid = $azure_blob_path.$row['genimageoneid'].'.jpg';

				$genimagetwoid = '';
				if ($row['genimagetwoid'] != '')
					$genimagetwoid = $azure_blob_path.$row['genimagetwoid'].'.jpg';

				$scanneroneimageid = '';
				if ($row['scanneroneimageid'] != '')
					$scanneroneimageid = $azure_blob_path.$row['scanneroneimageid'].'.jpg';

				$scannertwoimageid = '';
				if ($row['scannertwoimageid'] != '')
					$scannertwoimageid = $azure_blob_path.$row['scannertwoimageid'].'.jpg';

				$scannerthreeimageid = '';
				if ($row['scannerthreeimageid'] != '')
					$scannerthreeimageid = $azure_blob_path.$row['scannerthreeimageid'].'.jpg';

				$scannerfourimageid = '';
				if ($row['scannerfourimageid'] != '')
					$scannerfourimageid = $azure_blob_path.$row['scannerfourimageid'].'.jpg';

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
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="custom_form">
				<input type="hidden" name='export_data' id='export_data' value='1' />
				<input type="hidden" name="datatable_search" id="datatable_search" value="" />
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
			        
			        	<div class="col-md-3">
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
					<div class="col-md-1">
						<input class='btn btn-sm btn-success' value='Export' name='export_button' id='export_button' type='button'>
					</div>
					<div class="col-md-1">
						<input class='btn btn-sm btn-info refresh_page' value='Refresh' name='refresh_page' type='button'>
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
						<th>Brand (Other)</th>
						<th>Type, Capacity (Other)</th>
						<th>Other Details (Other)</th>
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
				<?php 
					if ($_SESSION['superadmin'] == 1)
						echo "<th>Edit</th>";
				?>	
                    </tr>
                </thead>

            </table>
        </div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	<form>
	  <input type="hidden" name="visitinfoid" id="visitinfoid" value="" />
	  <input type="hidden" name="btnrowidx" id="btnrowidx" value="" />
	  <div class="form-row">
          <div class="form-group col-md-6">
            <label for="serialnumber" class="col-form-label">Serial:</label>
            <input type="text" class="form-control form-control-sm" id="serialnumber" name="serialnumber">
          </div>
          <div class="form-group col-md-6">
            <label for="modelnumber" class="col-form-label">Model:</label>
            <input type="text" class="form-control form-control-sm" id="modelnumber" name="modelnumber">
	  </div>
	  </div>
	  <div class="form-row">
          <div class="form-group col-md-6">
            <label for="desc1" class="col-form-label">Brand (Other):</label>
            <input type="text" class="form-control form-control-sm" id="desc1" name="desc1">
          </div>
          <div class="form-group col-md-6">
            <label for="desc2" class="col-form-label">Type, Capacity (Other):</label>
            <input type="text" class="form-control form-control-sm" id="desc2" name="desc2">
          </div>
	  </div>
	  <div class="form-row">
          <div class="form-group col-md-6">
            <label for="desc3" class="col-form-label">Other Details (Other):</label>
            <input type="text" class="form-control form-control-sm" id="desc3" name="desc3">
          </div>
          <div class="form-group col-md-6">
            <label for="desc4" class="col-form-label">RT / PS</label>
            <input type="text" class="form-control form-control-sm" id="desc4" name="desc4">
          </div>
	  </div>
	  <div class="form-row">
          <div class="form-group col-md-6">
            <label for="dropdown1" class="col-form-label">Item Status:</label>
	    <select class="form-control form-control-sm" id="dropdown1" name="dropdown1">
	    </select>
          </div>
          <div class="form-group col-md-6">
            <label for="dropdown2" class="col-form-label">Item Condition:</label>
	    <select class="form-control form-control-sm" id="dropdown2" name="dropdown2">
	    </select>
          </div>
	  </div>
        </form>
      </div>
      <div class="status">
      </div>
      <div class="alert alert-success success_status" style='display:none'> <a href="#" class="close" data-dismiss="alert">&times;</a>
	<h5>Success</h5>
	<div>Captured item details updated successfully!</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm btn_update">Update</button>
      </div>
    </div>
  </div>
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

	$('#export_button').click( function (){
		$("#datatable_search").val($("#tieuptable_filter input").val());
		$("#custom_form").submit();
	});


    //refresh table
    $('.refresh_page').click(function(){
		$('.items_pending_list').DataTable().ajax.reload();
	});

	//approve & reject item
	$('.items_pending_list tbody').on( 'click', 'button', function () {

	var button = $(this);
	var button_type = $(this).text();
        var btnrow = dataTable.row( $(this).parents('tr') );
        var btnrowidx = btnrow.index();
        var data = btnrow.data();
        
	var item_id = data[0];
	var item_barcode = data[1];
		
        if(button_type == "Approve")
        {
        	var task = "approve_item";
        	var task_data = 'item_id='+item_id+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>');
        	
        	var r = confirm("Do you want to approve the item " + item_id + " with barcode " + item_barcode + "?");
			if (r == true) 
			{
				$(button).attr("disabled",true);
				$('.approve_status_'+item_id).html("<img src='images/loading.gif' width='24' height='24' />");
				$.ajax({
					type : 'post',
					url : 'updation_helper.php',
					data : task_data,
					success : function(res)
					{
						if(res == 'success')
						{
							dataTable.cell(btnrowidx, 32).data('');
							dataTable.cell(btnrowidx, 33).data('');
							dataTable.cell(btnrowidx, 35).data('Manual');
							dataTable.cell(btnrowidx, 37).data('Approved');
							dataTable.cell(btnrowidx, 38).data('');
							return false; 
						}
						else
						{
							$(button).attr("disabled",false);
							$('.approve_status_'+item_id).html("<b style='color:red;'>"+res+"</b>");
							return false;
						}
					}
				});
			}
			else
			{
				return false;
			}
        }
        else
        if(button_type == "Reject")
        {

        	var r = confirm("Do you really want to reject the item " + item_id + " with barcode " + item_barcode + "?");
			if (r == true) 
			{
			var rfrejection = prompt("Please enter rejection comments:");
	        	if (rfrejection == null || rfrejection == "") 
	        	{
			       	return false;
			    } 
			    else 
			    {
			    	$(button).attr("disabled",true);
				$('.reject_status_'+item_id).html("<img src='images/loading.gif' width='24' height='24' />");
			    	var task = "reject_item";
			        var task_data = 'item_id='+item_id+'&rfrejection='+rfrejection+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>');
	        		//alert(task_data);return false;
		        	
			    }
				$.ajax({
					type : 'post',
					url : 'updation_helper.php',
					data : task_data,
					success : function(res)
					{
						if(res == 'success')
						{
							dataTable.cell(btnrowidx, 32).data('Rejected');
							dataTable.cell(btnrowidx, 33).data(rfrejection);
							dataTable.cell(btnrowidx, 35).data('');
							dataTable.cell(btnrowidx, 37).data('');
							dataTable.cell(btnrowidx, 38).data('Rejected');
							return false; 
						}
						else
						{
							$(button).attr("disabled",false);
							$('.reject_status_'+item_id).html("<b style='color:red;'>"+res+"</b>");
							return false;
						}
					}
				});
			}
			else
			{
				return false;
			}
        	
        	
        }	
		return false;
    });
		
	$('.items_pending_list tbody').on( 'click', 'a', function () {

	var button = $(this);
        var btnrow = dataTable.row( $(this).parents('tr') );
        var btnrowidx = btnrow.index();
        var data = btnrow.data();
        
	var item_id = data[0];
	var item_barcode = data[1];
	var jobid = data[6];
	var serialnumber = data[13];
	var modelnumber = data[15];
	var desc1 = data[21];
	var desc2 = data[22];
	var desc3 = data[23];
	var desc4 = data[24];

	var modal = $("#exampleModal");
	modal.find('#visitinfoid').val(item_id);
	modal.find('#btnrowidx').val(btnrowidx);
	modal.find('.modal-title').html("Editing item <span style='font-family: monospace;'>" + item_id + "</span> with barcode <span style='font-family: monospace;'>" + item_barcode + "</span>")
	modal.find('#serialnumber').val(serialnumber);
	modal.find('#modelnumber').val(modelnumber);
	modal.find('#desc1').val(desc1);
	modal.find('#desc2').val(desc2);
	modal.find('#desc3').val(desc3);
	modal.find('#desc4').val(desc4);

	var task = "jobdropdown_info";
        var task_data = 'jobid='+jobid+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>');
        	
	$.ajax({
		type : 'post',
		url : 'get_edit_data_helper.php',
		data : task_data,
		success : function(res)
		{
			var resary = res.split("@#@");
			if(resary[0] == 'success')
			{
				modal.find('#dropdown1').html(resary[1]);
				modal.find('#dropdown2').html(resary[2]);
			}
			else
			{
				alert('Error while fetching Job dropdown');
				return false;
			}
		}
	});

    });


	//update button clicked
	$('.btn_update').click(function(){
		var visitinfoid = $('#visitinfoid').val();
		var btnrowidx = $('#btnrowidx').val();
		var serialnumber = $('#serialnumber').val();
		var modelnumber = $('#modelnumber').val();
		var desc1 = $('#desc1').val();
		var desc2 = $('#desc2').val();
		var desc3 = $('#desc3').val();
		var desc4 = $('#desc4').val();
		var dropdown1 = $('#dropdown1').val();
		var dropdown2 = $('#dropdown2').val();
		var task = 'update_visitinfo';
	
		$.ajax({
		type : 'post',
		url : 'updation_helper.php',
		data : 'visitinfoid='+visitinfoid+'&serialnumber='+serialnumber+'&modelnumber='+modelnumber+'&desc1='+desc1+'&desc2='+desc2+'&desc3='+desc3+'&desc4='+desc4+'&dropdown1='+dropdown1+'&dropdown2='+dropdown2+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
		success : function(res)
		{
			if(res == 'success')
			{
				$('.success_status').show();
				dataTable.cell(btnrowidx, 13).data(serialnumber);
				dataTable.cell(btnrowidx, 15).data(modelnumber);
				dataTable.cell(btnrowidx, 21).data(desc1);
				dataTable.cell(btnrowidx, 22).data(desc2);
				dataTable.cell(btnrowidx, 23).data(desc3);
				dataTable.cell(btnrowidx, 24).data(desc4);
				dataTable.cell(btnrowidx, 29).data(dropdown1);
				dataTable.cell(btnrowidx, 30).data(dropdown2);
			}
			else
			{
				$('.status').html("<div class='alert alert-danger'><h5>Error</h5><strong>"+res+"</div>");
				return false;
			}
		}
		});
	});

});
</script>
</body>
</html>
