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

date_default_timezone_set('Asia/Calcutta');
	
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'visitinfoid',
		1 =>'scanneritemvalue',
		2 =>'genimageoneid', 
		3 =>'genimagetwoid',
		4 =>'sitecode', 
		5 =>'sitename',
		6 =>'jobinfoid',
		7 =>'jobno', 
		8 =>'level1termid',
		9 =>'level2termid',
		10 =>'level3termid', 
		11 =>'level4termid',
		12 =>'level5termid',
		13 =>'scanneritemone',
		14 =>'scanneroneimageid',
		15 =>'scanneritemtwo', 
		16 =>'scannertwoimageid', 
		17 =>'scanneritemthree',
		18 =>'scannerthreeimageid',
		19 =>'scanneritemfour',
		20 =>'scannerfourimageid',
		21 =>'descriptionone', 
		22 =>'descriptiontwo',
		23 =>'descriptionthree',
		24 =>'descriptionfour', 
		25 =>'descriptionfive',
		26 =>'descriptionsix',
		27 =>'dateone',
		28 =>'datetwo', 
		29 =>'dropdownone',
		30 =>'dropdowntwo',
		31 =>'ispartialverified',
		32 =>'isrejected',
		33 =>'rfrejection',
		34 =>'rejectedon',
		35 =>'approvedtype',
		36 =>'approvedon',
		37 =>'',
		38 =>'',
		39 =>'capturedon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( L.sitename ILIKE '%".$paramsearchval."%' ";
		$where .=" OR J.jobno ILIKE '%".$paramsearchval."%' )";
		/*$where .=" AND ( CAST(V.visitinfoid AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.scanneritemvalue ILIKE '%".$paramsearchval."%' ";    
		$where .=" OR L.sitecode ILIKE '%".$paramsearchval."%' ";
		$where .=" OR L.sitename ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(J.jobinfoid AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR J.jobno ILIKE '%".$paramsearchval."%' ";
		$where .=" OR D1.term ILIKE '%".$paramsearchval."%' ";
		$where .=" OR D2.term ILIKE '%".$paramsearchval."%' ";
		$where .=" OR D3.term ILIKE '%".$paramsearchval."%' ";
		$where .=" OR D4.term ILIKE '%".$paramsearchval."%' ";
		$where .=" OR D5.term ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.scanneritemone ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.scanneritemtwo ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.scanneritemthree ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.scanneritemfour ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptionone ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptiontwo ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptionthree ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptionfour ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptionfive ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.descriptionsix ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(V.dateone AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(V.datetwo AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.dropdownone ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.dropdowntwo ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.rfrejection ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(V.rejectedon AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(V.approvedon AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(V.capturedon AS text) ILIKE '%".$paramsearchval."%' )";*/
	}
	//echo $params['columns'][3]['search']['value'];exit;
	
	// getting total number records without any search
	$sql = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.scanneritemtwo, V.scannertwoimageid, V.scanneritemthree, V.scannerthreeimageid, V.scanneritemfour, V.scannerfourimageid, V.descriptionone, V.genimageoneid, V.descriptiontwo, V.genimagetwoid, V.descriptionthree, V.descriptionfour, V.descriptionfive, V.descriptionsix, V.dateone, V.datetwo, V.dropdownone, V.dropdowntwo, V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, D5.term AS term5, V.capturedon
		FROM visitinfo AS V
		LEFT JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
		LEFT JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
		LEFT JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
		LEFT JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
		LEFT JOIN dropdownmaster AS D5 ON V.level5termid=D5.termid
		INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
		INNER JOIN location AS L ON J.locationid=L.locationid WHERE 1 = 1";
		
	if( !empty($params['columns'][0]['search']['value']) ) {  
		if($params['columns'][0]['search']['value']=='0')
		{
			$where .= "";
		}
		else
		{
			$where .=" AND V.jobinfoid = '".quote_smart($params['columns'][0]['search']['value'])."' ";
		}
	}

	if( !empty($params['columns'][1]['search']['value'])) {
		if($params['columns'][1]['search']['value'] == '1')
		{
			$where .=" AND V.barcodeinfoid IS null AND V.isrejected='0' ";
		}
		else
		if($params['columns'][1]['search']['value'] == '2')
		{
			$where .=" AND V.barcodeinfoid IS NOT null AND V.isrejected='0' ";
			if( !empty($params['columns'][2]['search']['value']) ) 
			{ 
				$where .=" AND V.approvedon >= '".quote_smart($params['columns'][2]['search']['value'])."' ";
			}

			if( !empty($params['columns'][3]['search']['value']) ) 
			{ 
				$where .=" AND V.approvedon <= '".quote_smart($params['columns'][3]['search']['value'])." 23:59:59' ";
			}
		}	
		else
		if($params['columns'][1]['search']['value'] == '3')
		{
			$where .=" AND V.isrejected='1' AND V.approvedtype='0' ";
			if( !empty($params['columns'][2]['search']['value']) ) 
			{ 
				$where .=" AND V.rejectedon >= '".quote_smart($params['columns'][2]['search']['value'])."' ";
			}

			if( !empty($params['columns'][3]['search']['value']) ) 
			{ 
				$where .=" AND V.rejectedon <= '".quote_smart($params['columns'][3]['search']['value'])." 23:59:59' ";
			}
		}
		else
		if($params['columns'][1]['search']['value'] == '4')
		{
			$where .= "";
		}
	}
	
	

	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}
//echo $sqlRec;exit;
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".quote_smart($params['order'][0]['dir'])."  OFFSET ".quote_smart($params['start'])." LIMIT ".quote_smart($params['length'])." ";

	$queryTot = pg_query($conn, $sqlTot);
//echo $sqlRec;

	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$data_result = null;
	while( $row = pg_fetch_row($queryRecords) ) {
		//fetching data & storing in data_result[] array.
		$data_result['0'] = $row['0'];
		$data_result['1'] = $row['1'];

		if($row['15'] != '')
		{
			$data_result['2'] = "<a data-fancybox href='$azure_blob_path" . $row['15'] . ".jpg' title='" . $row['15'] . "'>
				<img src='$azure_blob_path" . $row['15'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['2'] = "";
		}

		if($row['17'] != '')
		{
			$data_result['3'] = "<a data-fancybox href='$azure_blob_path" . $row['17'] . ".jpg' title='" . $row['17'] . "'>
				<img src='$azure_blob_path" . $row['17'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['3'] = "";
		}

		$data_result['4'] = $row['2'];
		$data_result['5'] = $row['3'];
		$data_result['6'] = $row['4'];
		$data_result['7'] = $row['5'];		
		$data_result['8'] = $row['33'];
		$data_result['9'] = $row['34'];
		$data_result['10'] = $row['35'];
		$data_result['11'] = $row['36'];
		$data_result['12'] = $row['37'];

		$data_result['13'] = $row['6'];
		if($row['7'] != '')
		{
			$data_result['14'] = "<a data-fancybox href='$azure_blob_path" . $row['7'] . ".jpg' title='" . $row['7'] . "'>
				<img src='$azure_blob_path" . $row['7'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['14'] = "";
		}

		$data_result['15'] = $row['8'];
		if($row['9'] != '')
		{
			$data_result['16'] = "<a data-fancybox href='$azure_blob_path" . $row['9'] . ".jpg' title='" . $row['9'] . "'>
				<img src='$azure_blob_path" . $row['9'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['16'] = "";
		}

		$data_result['17'] = $row['10'];
		if($row['11'] != '')
		{
			$data_result['18'] = "<a data-fancybox href='$azure_blob_path" . $row['11'] . ".jpg' title='" . $row['11'] . "'>
				<img src='$azure_blob_path" . $row['11'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['18'] = "";
		}

		$data_result['19'] = $row['12'];
		if($row['13'] != '')
		{
			$data_result['20'] = "<a data-fancybox href='$azure_blob_path" . $row['13'] . ".jpg' title='" . $row['13'] . "'>
				<img src='$azure_blob_path" . $row['13'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['20'] = "";
		}

		$data_result['21'] = $row['14'];
		$data_result['22'] = $row['16'];
		$data_result['23'] = $row['18'];
		$data_result['24'] = $row['19'];
		$data_result['25'] = $row['20'];
		$data_result['26'] = $row['21'];

		$data_result['27'] = $row['22'];
		$data_result['28'] = $row['23'];

		$data_result['29'] = $row['24'];
		$data_result['30'] = $row['25'];

		if($row['29']=='1')
		{
			$data_result['31'] = 'Yes';
		}
		else
		{
			$data_result['31'] = 'No';
		}
		
		if($row['26']=='1')
		{
			$data_result['32'] = "Rejected";
		}
		else
		{
			$data_result['32'] = "";
		}

		$data_result['33'] = $row['27'];
		$data_result['34'] = $row['28'];

		$approvedtype = '';
		if($row['26']=='0' && $row['30']=='2' && ($row['32']!='' || $row['32']!= null))
		{
			$approvedtype = 'Manual';
		}
		else if($row['26']=='0' && $row['30']=='1' && ($row['32']!='' || $row['32']!= null))
		{
			$approvedtype = 'Bulk';
		}
		else if ($row['26']=='0' && $row['30']=='0' )
		{
			$approvedtype = 'Pending';
		}
		$data_result['35'] = $approvedtype;
		
		$data_result['36'] = $row['31'];
		if(($row['32']!='' || $row['32']!= null) && $row['26']=='0')
		{
			$data_result['37'] =  "Approved";
			$data_result['38'] =  "";
		}
		else if(($row['32']=='' || $row['32']== null) && $row['26']!='1')
		{
			$item_id = $row['0'];
			//$data_result['37'] =  "<a class='btn btn-success btn-sm approve' href='items-pending-verification-helper.php?type=approve&visitinfoid=" . $row['0'] . "' value='".$row['0']."'>Approve</a>";
			//$data_result['38'] =  "<a class='btn btn-danger btn-sm reject' href='item_pending_reject.php?visitinfoid=".$row['0']."' target='_blank'>Reject</a>";

			//Approved button
			$data_result['37'] =  "<button class='btn btn-success btn-sm'>Approve</button><p class='approve_status_$item_id'></p>";
			//Reject button
			$data_result['38'] =  "<button class='btn btn-danger btn-sm'>Reject</button><p class='reject_status_$item_id'></p>";
		}
		else if(($row['32']=='' || $row['32']== null) && $row['26']=='1')
		{
			$data_result['37'] =  "";
			$data_result['38'] =  "Rejected";
		}
		
		$data_result['39'] =  $row['38'];
		
		if ($_SESSION['superadmin'] == 1)
		{
			$data_result['40'] = "<center><a class='edit_status_$item_id' style='cursor: pointer;' data-toggle='modal' data-target='#exampleModal' >
						<i class='far fa-edit'></i> </a>
						</center>";
		}
				
		$data[] = $data_result;
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
