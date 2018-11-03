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
	$params = $columns = $totalRecords = $data = $img_files = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'jobinfoid',
		1 =>'sitecode',
		2 =>'sitename',
		3 =>'jobno', 
		4 =>'accurdistance',
		5 =>'errorflg',
		6 =>'J.tokenid',
		7 =>'status',
		8 =>'emailid',
		9 =>'starttime',
		10 =>'endtime',
		11 =>'createdon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {
	     	$paramsearchval = quote_smart($params['search']['value']);
		$where .=" AND ( CAST(jobinfoid AS text) ILIKE '%".$paramsearchval."%' ";    
		$where .=" OR sitecode ILIKE '%".$paramsearchval."%' ";
		$where .=" OR sitename ILIKE '%".$paramsearchval."%' ";
		$where .=" OR jobno ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(accurdistance AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR J.tokenid ILIKE '%".$paramsearchval."%' ";
		$where .=" OR emailid ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(starttime AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(endtime AS text) ILIKE '%".$paramsearchval."%' ";
		$where .=" OR CAST(createdon AS text) ILIKE '%".$paramsearchval."%' )";
	}

	if( !empty($params['columns'][1]['search']['value'])) {
		if($params['columns'][1]['search']['value'] == '1')
		{
			$where .= "";
		}
		else
		if($params['columns'][1]['search']['value'] == '2')
		{
			$where .=" AND status = '0' ";
		}	
		else
		if($params['columns'][1]['search']['value'] == '3')
		{
			$where .=" AND status = '1' ";
		}
		else
		if($params['columns'][1]['search']['value'] == '4')
		{
			$where .=" AND status = '2' ";
		}
	}

	if( !empty($params['columns'][2]['search']['value']) ) 
	{ 
		$where .=" AND createdon >= '".quote_smart($params['columns'][2]['search']['value'])."' ";
	}

	if( !empty($params['columns'][3]['search']['value']) ) 
	{ 
		$where .=" AND createdon <= '".quote_smart($params['columns'][3]['search']['value'])." 23:59:59' ";
	}

	// getting total number records without any search
	$sql = "SELECT J.jobinfoid, L.sitecode, L.sitename, J.jobno, J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.emailid, J.starttime, J.endtime, J.createdon
	    	FROM jobinfo AS J 
		INNER JOIN location AS L ON J.locationid=L.locationid 
		LEFT JOIN userinfo AS U ON J.userid=U.userid WHERE 1=1 ";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}

	//echo $sqlRec;exit;
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".quote_smart($params['order'][0]['dir'])."  OFFSET ".quote_smart($params['start'])." LIMIT ".quote_smart($params['length'])." ";

	$queryTot = pg_query($conn, $sqlTot);
	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$data_index = 0;
	$currjobid = 0;

	while( $row_jobinfo = pg_fetch_row($queryRecords) )
	{
		$img_files = null;
		$sql_fileinfo = "SELECT filename FROM imageinfo WHERE (filename LIKE 'Site_%' OR filename LIKE 'Hording_%') AND jobinfoid=".$row_jobinfo['0']."ORDER BY filename";
		$query_fileinfo = pg_query($conn, $sql_fileinfo);
		while($row_fileinfo = pg_fetch_row($query_fileinfo))
		{
			if($row_fileinfo['0'] != '')
			{
				$img_files[] = "<a data-fancybox href='$azure_blob_path" . $row_fileinfo['0'] . "' title='" . $row_fileinfo['0'] . "'>
						<img src='$azure_blob_path" . $row_fileinfo['0'] . "' height='30' />
						</a> ";
			}
			else
			{
				$img_files[] = "";
			}
		}

		$row_jobinfo['4'] =  $row_jobinfo['4'].' '.$row_jobinfo['5'];
		
		if ($row_jobinfo['6'] == '0')
			$row_jobinfo['5'] = "No";
		else if ($row_jobinfo['6'] == '1')
			$row_jobinfo['5'] = "Yes";
		
		$row_jobinfo['6'] =  $row_jobinfo['7'];
		
		if ($row_jobinfo['8'] == '0')
		{
			$row_jobinfo['7'] = "Not Started";
			//Reset job button
			$row_jobinfo['13'] = "<span><button id='button1' class='btn btn-danger btn-sm'>Reset</button><img src='images/loading.gif' width='24' height='24' style='display:none;' /></span>";
			$row_jobinfo['14'] = "<center><a href='edit_job_info.php?jobinfoid=".$row_jobinfo['0']."' class='edit_job_info' >
						<i class='far fa-edit'></i> </a>
						</center>";     //adding edit_job_info button	
		}
		else if ($row_jobinfo['8'] == '1')
		{
			$row_jobinfo['7'] = "Started";
			//Reset job button
			$row_jobinfo['13'] = "<span><button id='button2' class='btn btn-danger btn-sm'>Reset</button><img src='images/loading.gif' width='24' height='24' style='display:none;' /></span>";
			$row_jobinfo['14'] = "<center>-</center>";
		}
		else if ($row_jobinfo['8'] == '2')
		{
			$row_jobinfo['7'] = "Finished";
			$row_jobinfo['13'] = "<center>-</center>";
			$row_jobinfo['14'] = "<center>-</center>";
		}
			
		if ($row_jobinfo['8'] == '1' || $row_jobinfo['8'] == '2')
		{
			$row_jobinfo['15'] = "<center><a style='cursor: pointer;' data-toggle='modal' data-target='#exampleModal' >
						<i class='far fa-edit'></i> </a>
						</center>";
		}
		else
		{
			$row_jobinfo['15'] = "<center>-</center>";
		}


		$row_jobinfo['8'] = $row_jobinfo['9'];
		$row_jobinfo['9'] = $row_jobinfo['10'];
		$row_jobinfo['10'] = $row_jobinfo['11'];
		$row_jobinfo['11'] = $row_jobinfo['12'];

		$row_jobinfo['12'] = $img_files;

		$data[] = $row_jobinfo;                 //resultant array
	}
	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	
