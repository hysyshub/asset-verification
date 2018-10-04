
<?php
	//include connection file 
	include 'php/config.php';
	$conn = pg_connect($conn_string); 
	// initilize all variable
	$params = $columns = $totalRecords = $data = $img_files = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'jobinfoid',
		1 =>'jobno', 
		2 =>'accurdistance',
		3 =>'tokenid',
		4 =>'starttime',
		5 =>'endtime',
		6 =>'createdon'
	);

	$where = $sqlTot = $sqlRec = "";

	// check search value if exist
	if( !empty($params['search']['value']) ) {   
		$where .=" AND ( CAST(jobinfoid AS text) LIKE '%".$params['search']['value']."%' ";    
		$where .=" OR lower(jobno) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(accurdistance AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR lower(tokenid) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(starttime AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(endtime AS text) LIKE '%".$params['search']['value']."%' ";
		$where .=" OR CAST(createdon AS text) LIKE '%".$params['search']['value']."%' )";
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
		$where .=" AND createdon >= '".$params['columns'][2]['search']['value']."' ";
	}

	if( !empty($params['columns'][3]['search']['value']) ) 
	{ 
		$where .=" AND createdon <= '".$params['columns'][3]['search']['value']." 23:59:59' ";
	}

	// getting total number records without any search
	$sql = "SELECT DISTINCT jobinfoid FROM jobinfo  WHERE 1 = 1";
	/*$sql = "SELECT J.jobinfoid, L.sitecode, L.sitename, J.jobno, J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.emailid, J.starttime, J.endtime, J.createdon, I.filename
    	FROM jobinfo AS J 
		INNER JOIN location AS L ON J.locationid=L.locationid 
		LEFT JOIN userinfo AS U ON J.userid=U.userid
		LEFT JOIN (SELECT * FROM imageinfo WHERE filename LIKE 'Site_%' OR filename LIKE 'Hording_%') AS I ON J.jobinfoid=I.jobinfoid";*/
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}

	//echo $sqlRec;exit;
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  OFFSET ".$params['start']." LIMIT ".$params['length']." ";

	$queryTot = pg_query($conn, $sqlTot);


	$totalRecords = pg_num_rows($queryTot);

	$queryRecords = pg_query($conn, $sqlRec);
	$total_rows = pg_num_rows($queryRecords);
	//iterate on results row and create new index array of data
	$data_index = 0;
	$currjobid = 0;

	while( $row = pg_fetch_row($queryRecords) )
	{
		//fetching data
		$sql_jobinfo = "SELECT J.jobinfoid,L.sitecode, L.sitename,J.jobno,J.accurdistance, J.accurdistanceunit, J.errorflg, J.tokenid, J.status, U.emailid, J.starttime, J.endtime, J.createdon 
		FROM jobinfo AS J 
		INNER JOIN location AS L ON J.locationid=L.locationid 
		LEFT JOIN userinfo AS U ON J.userid=U.userid 
		WHERE J.jobinfoid=".$row['0'];
		$query_jobinfo = pg_query($conn, $sql_jobinfo);
		$row_jobinfo = pg_fetch_row($query_jobinfo);
		$img_files = null;
		$sql_fileinfo = "SELECT filename FROM imageinfo WHERE (filename LIKE 'Site_%' OR filename LIKE 'Hording_%') AND jobinfoid=".$row['0']."ORDER BY filename";
		$query_fileinfo = pg_query($conn, $sql_fileinfo);
		while($row_fileinfo = pg_fetch_row($query_fileinfo))
		{
			if($row_fileinfo['0'] != '')
			{
				//$img_files[] .= $row_fileinfo['0'];
				$img_files[] = "<a data-fancybox href='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row_fileinfo['0'] . "' title='" . $row_fileinfo['0'] . "'>
				<img src='https://ihsavstorage.blob.core.windows.net/fileserverdata/" . $row_fileinfo['0'] . "' height='30' />
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
		//$row['7'] =  $row['8'];
		if ($row_jobinfo['8'] == '0')
			$row_jobinfo['7'] = "Not Started";
		else if ($row_jobinfo['8'] == '1')
			$row_jobinfo['7'] = "Started";
		else if ($row_jobinfo['8'] == '2')
			$row_jobinfo['7'] = "Finished";

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
	
