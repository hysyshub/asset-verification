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
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(E.usereventsinfoid AS text) ILIKE '%".$paramsearchval."%'  ";  
		$where .=" OR U.emailid ILIKE '%".$paramsearchval."%' ";  
		$where .=" OR E.event ILIKE '%".$paramsearchval."%' ";
		$where .=" OR E.longitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR E.latitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(E.capturedon AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR L.sitecode ILIKE '%".$paramsearchval."%' ";
		$where .=" OR L.sitename ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(J.jobinfoid AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR J.jobno ILIKE '%".$paramsearchval."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT E.usereventsinfoid, U.emailid, E.event, E.longitude, E.latitude, E.capturedon, L.sitecode, L.sitename, J.jobinfoid, J.jobno
			FROM usereventsinfo AS E
			INNER JOIN userinfo AS U ON E.userid=U.userid
			LEFT JOIN jobinfo AS J ON E.jobinfoid=J.jobinfoid
			LEFT JOIN location AS L ON J.locationid=L.locationid WHERE 1=1";
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
	
