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
		0 =>'querymasterid', 
		1 =>'firstname, lastname', 
		2 =>'title',
		3 =>'title',
		4 =>'jobno',
		5 =>'lastupdatedon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist

	if( !empty($params['search']['value']) ) {
	       	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(X.querymasterid AS text) ILIKE '%".$paramsearchval."%'  ";  
		$where .=" OR U.firstname ILIKE '%".$paramsearchval."%' "; 
		$where .=" OR U.lastname ILIKE '%".$paramsearchval."%' ";  
		$where .=" OR Q.title ILIKE '%".$paramsearchval."%' ";
		$where .=" OR J.jobno ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(X.lastupdatedon AS text) ILIKE '%".$paramsearchval."%' )";
	}
	
	//data-table filter "user-id"
    if( !empty($params['columns'][0]['search']['value']) ) {  
		if($params['columns'][0]['search']['value']=='0')
		{
			$where .= "";
		}
		else
		{
			$where .=" AND U.userid = '".quote_smart($params['columns'][0]['search']['value'])."' ";
		}
	}

	//data-table filter "query status"
	if( !empty($params['columns'][1]['search']['value'])) {
		if($params['columns'][1]['search']['value'] == '1')
		{
			$where .= " AND Q.status='1' ";
		}
		else
		if($params['columns'][1]['search']['value'] == '2')
		{
			$where .=" AND Q.status='2' ";
		}	
		else
		if($params['columns'][1]['search']['value'] == '3')
		{
			$where .=" ";
		}
	}
	else
	{
		$where .= " AND Q.status='1' ";
	}

	//data-table filter "from-date"
	if( !empty($params['columns'][2]['search']['value']) ) 
	{ 
		$where .=" AND X.lastupdatedon >= '".quote_smart($params['columns'][2]['search']['value'])."' ";
	}

	//data-table filter "upto-date"
	if( !empty($params['columns'][3]['search']['value']) ) 
	{ 
		$where .=" AND X.lastupdatedon <= '".quote_smart($params['columns'][3]['search']['value'])." 23:59:59' ";
	}


	// getting total number records without any search
	$sql = "SELECT X.querymasterid, X.lastupdatedon, Q.type, Q.title, Q.status, Q.capturedon, U.userid, U.emailid, U.firstname, U.lastname, J.jobinfoid, J.jobno
    FROM (
    SELECT Q.querymasterid, max(textedon) AS lastupdatedon FROM querymaster AS Q 
    LEFT JOIN queryalloc AS T ON Q.querymasterid=T.querymasterid
    WHERE T.usertype='0' -- msg by user
    GROUP BY Q.querymasterid
    ) AS X 
    JOIN querymaster AS Q ON X.querymasterid=Q.querymasterid
    JOIN userinfo AS U ON Q.userid=U.userid
    LEFT JOIN jobinfo AS J ON Q.jobinfoid=J.jobinfoid
    WHERE 1 = 1 ";

    

	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}

	//echo $sqlRec;exit;
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."  ".quote_smart($params['order'][0]['dir'])." OFFSET ".quote_smart($params['start'])." LIMIT ".quote_smart($params['length'])." ";

	$queryTot = pg_query($conn, $sqlTot);


	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$result_data = null;
	
	while( $row = pg_fetch_row($queryRecords) ) 
	{
		$querymasterid = $row['0'];
		$result_data['0'] = $row['0'];
		$result_data['1'] = $row['8'].' '.$row['9'];

		if($row['2']=='1')
        {
            $result_data['2'] = 'General query';
        }
        else
        if($row['2']=='2')
        {
            $result_data['2'] = 'Job specific query';
        }
        $result_data['3'] = $row['3'];
        $result_data['4'] = $row['11'];
        $result_data['5'] = $row['1'];
        $result_data['6'] = "<a href='helpdesk-info.php?querymasterid=$querymasterid' class='btn btn-sm btn-info'>View</a></td>";
		$data[] = $result_data;               // resultant array
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	
