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
		0 =>'admininfoid',
		1 =>'firstname', 
		2 =>'lastname',
		3 =>'emailid',
		4 =>'address', 
		5 =>'contactnumber', 
		6 =>'superadmin'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(admininfoid AS text) ILIKE '%".$paramsearchval."%' ";    
		$where .=" OR firstname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR lastname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR emailid ILIKE '%".$paramsearchval."%' ";
		$where .=" OR address ILIKE '%".$paramsearchval."%' ";
		$where .=" OR contactnumber ILIKE '%".$paramsearchval."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT admininfoid,firstname,lastname,emailid,address,contactnumber,superadmin FROM admininfo WHERE 1=1";
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
		$admininfoid = $row['0'];
		if ($row['6'] == 1)
			$row['6'] = 'Yes';
		else
			$row['6'] = 'No';

		$row['7'] = "<center><center><a href='edit_admin_info.php?admininfoid=$admininfoid' class='edit_admin_info' >
						<i class='far fa-edit'></i> </a>
					</center>";     //adding edit_admin_info button
		$row['8'] = "<center><a href='change_pass_admin.php?admininfoid=$admininfoid'>
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
	
