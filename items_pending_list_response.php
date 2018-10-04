
<?php
	
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
		2 =>'sitecode', 
		3 =>'sitename',
		4 =>'jobinfoid',
		5 =>'jobno', 
		6 =>'level1termid',
		7 =>'level2termid',
		8 =>'level3termid', 
		9 =>'level4termid',
		10 =>'scanneritemone',
		11 =>'scanneritemtwo', 
		12 =>'scanneritemthree',
		13 =>'scanneritemfour',
		14 =>'descriptionone', 
		15 =>'descriptiontwo',
		16 =>'descriptionthree',
		17 =>'descriptionfour', 
		18 =>'descriptionfive',
		19 =>'descriptionsix',
		20 =>'term2',
		21 =>'term3',
		22 =>'dateone',
		23 =>'datetwo', 
		24 =>'dropdownone',
		25 =>'dropdowntwo',
		26 =>'rejectedon'		
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(V.visitinfoid AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.scanneritemvalue) LIKE '%".$params['search']['value']."%' ";    
		$where .=" OR lower(L.sitecode) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(L.sitename) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(J.jobinfoid AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(J.jobno) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(D1.term) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(D2.term) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(D3.term) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(D4.term) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(D5.term) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.scanneritemone) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.scanneritemtwo) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.scanneritemthree) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.scanneritemfour) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptionone) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptiontwo) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptionthree) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptionfour) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptionfive) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.descriptionsix) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(V.dateone AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(V.datetwo AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.dropdownone) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.dropdowntwo) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(V.rfrejection) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(V.rejectedon AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(V.approvedon AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(V.capturedon AS text) LIKE '%".$params['search']['value']."%' )";
	}
	//echo $params['columns'][3]['search']['value'];exit;
	
	// getting total number records without any search
	$sql = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.scanneritemtwo, V.scannertwoimageid, V.scanneritemthree, V.scannerthreeimageid, V.scanneritemfour, V.scannerfourimageid, V.descriptionone, V.genimageoneid, V.descriptiontwo, V.genimagetwoid, V.descriptionthree, V.descriptionfour, V.descriptionfive, V.descriptionsix, V.dateone, V.datetwo, V.dropdownone, V.dropdowntwo, V.isrejected,V.rfrejection,V.rejectedon,V.ispartialverified,V.approvedtype,V.approvedon,V.barcodeinfoid, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, D5.term AS term5, V.capturedon
		FROM visitinfo AS V
		INNER JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
		INNER JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
		INNER JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
		INNER JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
		INNER JOIN dropdownmaster AS D5 ON V.level5termid=D5.termid
		INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
		INNER JOIN location AS L ON J.locationid=L.locationid WHERE 1 = 1";
		
	if( !empty($params['columns'][0]['search']['value']) ) {  
		if($params['columns'][0]['search']['value']=='0')
		{
			$where .= "";
		}
		else
		{
			$where .=" AND V.jobinfoid = '".$params['columns'][0]['search']['value']."' ";
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
				$where .=" AND V.approvedon >= '".$params['columns'][2]['search']['value']."' ";
			}

			if( !empty($params['columns'][3]['search']['value']) ) 
			{ 
				$where .=" AND V.approvedon <= '".$params['columns'][3]['search']['value']." 23:59:59' ";
			}
		}	
		else
		if($params['columns'][1]['search']['value'] == '3')
		{
			$where .=" AND V.isrejected='1' AND V.approvedtype='0' ";
			if( !empty($params['columns'][2]['search']['value']) ) 
			{ 
				$where .=" AND V.rejectedon >= '".$params['columns'][2]['search']['value']."' ";
			}

			if( !empty($params['columns'][3]['search']['value']) ) 
			{ 
				$where .=" AND V.rejectedon <= '".$params['columns'][3]['search']['value']." 23:59:59' ";
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
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  OFFSET ".$params['start']." LIMIT ".$params['length']." ";

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
			$data_result['2'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['15'] . ".jpg' title='" . $row['15'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['15'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['2'] = "";
		}

		if($row['17'] != '')
		{
			$data_result['3'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['17'] . ".jpg' title='" . $row['17'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['17'] . ".jpg' height='40' />
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
			$data_result['14'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['7'] . ".jpg' title='" . $row['7'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['7'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['14'] = "";
		}

		$data_result['15'] = $row['8'];
		if($row['9'] != '')
		{
			$data_result['16'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['9'] . ".jpg' title='" . $row['9'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['9'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['16'] = "";
		}

		$data_result['17'] = $row['10'];
		if($row['11'] != '')
		{
			$data_result['18'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['11'] . ".jpg' title='" . $row['11'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['11'] . ".jpg' height='40' />
				</a>";
		}
		else
		{
			$data_result['18'] = "";
		}

		$data_result['19'] = $row['12'];
		if($row['13'] != '')
		{
			$data_result['20'] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['13'] . ".jpg' title='" . $row['13'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row['13'] . ".jpg' height='40' />
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
		else if($row['26']=='0')
		{
			$data_result['32'] = "";
		}

		$data_result['33'] = $row['27'];
		$data_result['34'] = $row['28'];
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
			$data_result['37'] =  "<a class='btn btn-success btn-sm approve' href='items-pending-verification-helper.php?type=approve&visitinfoid=" . $row['0'] . "' value='".$row['0']."'>Approve</a>";
			$data_result['38'] =  "<a class='btn btn-danger btn-sm reject' href='item_pending_reject.php?visitinfoid=".$row['0']."' target='_blank'>Reject</a>";
		}
		else if(($row['32']=='' || $row['32']== null) && $row['26']=='1')
		{
			$data_result['37'] =  "";
			$data_result['38'] =  "Rejected";
		}
		
		$data_result['39'] =  $row['38'];
				
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
