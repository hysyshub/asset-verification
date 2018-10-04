
<?php
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'r',
		1 =>'barcodeinfoid', 
		2 =>'barcode',
		3 =>'locationid',
		4 =>'type', 
		5 =>'capturedon',
		6 =>'lastmodifiedon',
		7 =>'sitecode', 
		8 =>'sitename'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(barcodeinfoid AS text) LIKE '%".$params['search']['value']."%'  ";  
		$where .=" OR lower(sitecode) LIKE '%".$params['search']['value']."%' ";  
		$where .=" OR lower(sitename) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(barcode) LIKE '%".$params['search']['value']."%'  )";
	}

	// getting total number records without any search
	$sql = "SELECT * FROM (
	  SELECT ROW_NUMBER() OVER (PARTITION BY t.locationid ORDER BY barcodeinfoid) AS r, t.*, L.sitecode, L.sitename
	  FROM inventorymaster AS t, location AS L WHERE t.locationid=L.locationid AND t.type='1' ) x WHERE 1=1";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}


 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  OFFSET ".$params['start']." LIMIT ".$params['length']." ";

	$queryTot = pg_query($conn, $sqlTot);


	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$data_results = null;
	while( $row = pg_fetch_row($queryRecords) ) {
		//rearrange resultant data & adding it to $data_result array
		$data_results['0'] = $row['7'];
		$data_results['1'] = $row['8'];
		$data_results['2'] = $row['1'];
		$data_results['3'] = $row['2'];
		$data[] = $data_results;
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	