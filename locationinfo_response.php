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
		0 =>'locationid',
		1 =>'sitecode', 
		2 =>'sitename',
		3 =>'longitude',
		4 =>'lattitude',
		5 =>'address',
		6 =>'towncitylocation',
		7 =>'district',
		8 =>'pincode',
		9 =>'circlevalue',
		10 =>'vendorname',
		11 =>'technician_name',
		12 =>'technician_contact',
		13 =>'supervisor_name',
		14 =>'supervison_contact',
		15 =>'cluster',
		16 =>'cluster_manager_name',
		17 =>'cluster_manager_contact',
		18 =>'zone',
		19 =>'zonal_manager_name',
		20 =>'zonal_manager_contact'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(locationid AS text) ILIKE '%".$paramsearchval."%' ";    
		$where .=" OR CAST(sitecode AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR sitename ILIKE '%".$paramsearchval."%' ";
		$where .=" OR longitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR lattitude ILIKE '%".$paramsearchval."%' ";
		$where .=" OR address ILIKE '%".$paramsearchval."%' ";
		$where .=" OR towncitylocation ILIKE '%".$paramsearchval."%' ";
		$where .=" OR district ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(pincode AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR circlevalue ILIKE '%".$paramsearchval."%' ";
		$where .=" OR vendorname ILIKE '%".$paramsearchval."%' ";
		$where .=" OR technician_name ILIKE '%".$paramsearchval."%' ";
		$where .=" OR technician_contact ILIKE '%".$paramsearchval."%' ";
		$where .=" OR supervisor_name ILIKE '%".$paramsearchval."%' ";
		$where .=" OR supervison_contact ILIKE '%".$paramsearchval."%' ";
		$where .=" OR cluster ILIKE '%".$paramsearchval."%' ";
		$where .=" OR cluster_manager_name ILIKE '%".$paramsearchval."%' ";
		$where .=" OR cluster_manager_contact ILIKE '%".$paramsearchval."%' ";
		$where .=" OR zone ILIKE '%".$paramsearchval."%' ";
		$where .=" OR zonal_manager_name ILIKE '%".$paramsearchval."%' ";
		$where .=" OR zonal_manager_contact ILIKE '%".$paramsearchval."%' )";
	}

	// getting total number records without any search
	$sql = "SELECT L.locationid,L.sitecode,L.sitename,L.longitude,L.lattitude,L.address,L.towncitylocation,L.district,L.pincode,
		C.circlevalue,V.vendorname,L.technician_name,L.technician_contact,L.supervisor_name,L.supervison_contact,L.cluster,L.cluster_manager_name,
		L.cluster_manager_contact,L.zone,L.zonal_manager_name,L.zonal_manager_contact
		FROM location AS L, circleinfo AS C, vendorinfo AS V WHERE L.circleinfoid=C.circleinfoid AND L.vendorinfoid=V.vendorinfoid";
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
		$locationid = $row['0'];
		$row['21'] = "<center><a href='edit_locationinfo.php?locationid=$locationid'>
							<i class='far fa-edit'></i> </a>
					</center>";                  //adding edit button to table column
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
	
