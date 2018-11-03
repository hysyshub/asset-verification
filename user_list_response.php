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
		0 =>'userid',
		1 =>'firstname', 
		2 =>'lastname'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(U.userid AS text) ILIKE '%".$paramsearchval."%' ";    
		$where .=" OR U.emailid ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.firstname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.lastname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.contactnumber ILIKE '%".$paramsearchval."%' ";
		$where .=" OR C.circlevalue ILIKE '%".$paramsearchval."%' ";
		$where .=" OR V.vendorname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.deviceinfo ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.tokenid ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.longitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR U.lattitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(U.loggedinon AS text) ILIKE '%".$paramsearchval."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT * FROM userinfo AS U, circleinfo AS C, vendorinfo AS V WHERE U.circleinfoid=C.circleinfoid AND U.vendorinfoid=V.vendorinfoid ";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}


 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".quote_smart($params['order'][0]['dir'])."  OFFSET ".quote_smart($params['start'])." LIMIT ".quote_smart($params['length'])." ";

	$queryTot = pg_query($conn, $sqlTot);


	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$data_results = null;
	while( $row = pg_fetch_row($queryRecords) ) {
		$userid = $row['0'];
		$data_results['0'] = $row['0'];
		$data_results['1'] = $row['3'];
		$data_results['2'] = $row['1'];
		$data_results['3'] = $row['2'];
		$data_results['4'] = $row['14'];
		$data_results['5'] = $row['17'];
		$data_results['6'] = $row['19'];
		$data_results['7'] = $row['6'];
		$data_results['8'] = $row['7'];
		$data_results['9'] = $row['8'];
		$data_results['10'] = $row['9'];
		$data_results['11'] = $row['11'];
		$data_results['12'] = "<center><a href='edit_user_info.php?userid=$userid' class='edit_user_info'>
									<i class='far fa-edit'></i> </a>
							</center>";
		$data_results['13'] = "<center><a href='change_pass_user.php?userid=$userid'>
									<i class='far fa-edit'></i> </a>
							</center>";
		$data[] = $data_results;                               //resultant array
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	
