
<?php
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'notifymasterid',
		1 =>'firstname',
		2 =>'title',
		3 =>'message', 
		4 => 'url',
		5 => 'notifiedon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(Q.notifymasterid AS text) LIKE '%".$params['search']['value']."%'  ";  
		$where .=" OR lower(U.firstname) LIKE '%".$params['search']['value']."%' ";  
		$where .=" OR lower(Q.title) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(Q.message) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(Q.url) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR  CAST(Q.notifiedon AS text) LIKE '%".$params['search']['value']."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT Q.notifymasterid,U.firstname,Q.title,Q.message,Q.url,Q.notifiedon,G.userid FROM notifymaster as Q LEFT OUTER JOIN gennotifyalloc as G ON Q.notifymasterid = G.notifymasterid JOIN userinfo as U ON G.userid = U.userid WHERE Q.jobinfoid is NULL";
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

	//iterate on results row and create new index array of data
	while( $row = pg_fetch_row($queryRecords) ) { 
		$data[] = $row;              // resultatnt array
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	