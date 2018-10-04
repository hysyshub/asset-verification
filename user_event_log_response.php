
<?php
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'usereventsinfoid',
		1 =>'emailid', 
		2 =>'event',
		3 =>'longitude',
		4 =>'latitude',
		5 =>'capturedon',
		6 =>'sitecode',
		7 =>'sitename',
		8 =>'jobinfoid',
		9 =>'jobno'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(E.usereventsinfoid AS text) LIKE '%".$params['search']['value']."%'  ";  
		$where .=" OR lower(U.emailid) LIKE '%".$params['search']['value']."%' ";  
		$where .=" OR lower(E.event) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(E.longitude) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(E.latitude) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(E.capturedon AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(L.sitecode) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(L.sitename) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(J.jobinfoid AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(J.jobno) LIKE '%".$params['search']['value']."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT E.usereventsinfoid, U.emailid, E.event, E.longitude, E.latitude, E.capturedon, L.sitecode, L.sitename, J.jobinfoid, J.jobno
			FROM usereventsinfo AS E
			INNER JOIN userinfo AS U ON E.userid=U.userid
			LEFT JOIN jobinfo AS J ON E.jobinfoid=J.jobinfoid
			LEFT JOIN location AS L ON J.locationid=L.locationid
			WHERE U.emailid NOT LIKE '%sandeep%' AND U.emailid NOT LIKE '%padhi%'";
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
	while( $row = pg_fetch_row($queryRecords) ) {
		
		$data[] = $row;                          //resultant array
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	