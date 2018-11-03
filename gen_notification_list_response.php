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
		0 =>'notifymasterid',
		1 =>'fullname',
		2 =>'title',
		3 =>'message', 
		4 => 'url',
		5 => 'notifiedon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
		$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(Q.notifymasterid AS text) ILIKE '%".$paramsearchval."%'  ";  
		$where .=" OR U.firstname ILIKE '%".$paramsearchval."%' ";  
		$where .=" OR U.lastname ILIKE '%".$paramsearchval."%' ";  
		$where .=" OR Q.title ILIKE '%".$paramsearchval."%' ";
		$where .=" OR Q.message ILIKE '%".$paramsearchval."%' ";
		$where .=" OR Q.url ILIKE '%".$paramsearchval."%' ";
		$where .=" OR  CAST(Q.notifiedon AS text) ILIKE '%".$paramsearchval."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT Q.notifymasterid,concat(U.firstname,' ',U.lastname) as fullname,Q.title,Q.message,Q.url,Q.notifiedon,G.userid FROM notifymaster as Q LEFT OUTER JOIN gennotifyalloc as G ON Q.notifymasterid = G.notifymasterid JOIN userinfo as U ON G.userid = U.userid WHERE Q.jobinfoid is NULL";
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
	
