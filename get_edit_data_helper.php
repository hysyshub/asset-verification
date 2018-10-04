<?php
	include 'php/config.php';
	error_reporting(0);
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "db_conn_error";
		exit;
	}

	$task = $_POST['task'];

	// circle info
	if($task=='circle_info')
	{
		$circleinfoid = $_POST['circleinfoid'];
		$query = "SELECT * FROM circleinfo WHERE circleinfoid='$circleinfoid'";
		$result = pg_query($conn, $query);
		$row = pg_fetch_array($result);
		echo $row['circleinfoid'].','.$row['circlecode'].','.$row['circlevalue'];
	}
	else
	// vendor info
	if($task=='location_info')
	{
		$locationid = $_POST['locationid'];
		$query = "SELECT * FROM location AS L, circleinfo AS C, vendorinfo AS V WHERE L.circleinfoid=C.circleinfoid AND L.vendorinfoid=V.vendorinfoid AND L.locationid='$locationid' ORDER BY L.locationid ";
		$result = pg_query($conn, $query);
		$row = pg_fetch_array($result);
		echo $row['locationid'].','.$row['sitecode'].','.$row['sitename'].','.$row['address'].','.$row['towncitylocation'].','.$row['district'].','.$row['pincode'].','.$row['circleinfoid'].','.$row['vendorinfoid'].','.$row['technician_name'].','.$row['technician_contact'].','.$row['supervisor_name'].','.$row['supervison_contact'].','.$row['cluster'].','.$row['cluster_manager_name'].','.$row['cluster_manager_contact'].','.$row['zone'].','.$row['zonal_manager_name'].','.$row['zonal_manager_contact'].','.$row['circlevalue'].','.$row['vendorname'];
	}
?>