
<?php
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'admininfoid',
		1 =>'firstname', 
		2 =>'lastname',
		3 =>'emailid',
		4 =>'address', 
		5 =>'contactnumber'		
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(admininfoid AS text) LIKE '%".$params['search']['value']."%' ";    
		$where .=" OR lower(firstname) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(lastname) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(emailid) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(address) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(contactnumber) LIKE '%".$params['search']['value']."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT admininfoid,firstname,lastname,emailid,address,contactnumber FROM admininfo WHERE 1=1";
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
		$admininfoid = $row['0'];
		$row['6'] = "<center><center><a href='edit_admin_info.php?admininfoid=$admininfoid' class='edit_admin_info' >
						<i class='far fa-edit'></i> </a>
					</center>";     //adding edit_admin_info button
		$row['7'] = "<center><a href='change_pass_admin.php?admininfoid=$admininfoid'>
						<i class='far fa-edit'></i> </a>
					</center>";     //adding change_pass_admin button
		$data[] = $row;
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	
