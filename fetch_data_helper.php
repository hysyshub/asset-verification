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

	// get locations of perticular circlewise
	if($task=='fetch_locations_circlewise')
	{
		$info = null;
		$circleinfoid = $_POST['circleinfoid'];
		$vendorinfoid = $_POST['vendorinfoid'];
		if($vendorinfoid=='0')
		{
			$query = "SELECT * FROM location WHERE circleinfoid='$circleinfoid'";
		}
		else
		{
			$query = "SELECT * FROM location WHERE circleinfoid='$circleinfoid' AND vendorinfoid='$vendorinfoid' ORDER BY locationid";
		}
		$result = pg_query($conn, $query);
		$info .= "<option value='0'>-- Select Location --</option>";	
		while($row = pg_fetch_array($result))
		{
			$info .= "<option value='".$row['locationid']."'>".$row['sitename']."</option>";
		}
		$info .= "</select>";
		echo $info;
	}

	// get locations of perticular vendorwise
	if($task=='fetch_locations_vendorwise')
	{
		$info = null;
		$vendorinfoid = $_POST['vendorinfoid'];
		$circleinfoid = $_POST['circleinfoid'];
		
		if($circleinfoid=='0')
		{
			$query = "SELECT * FROM location WHERE vendorinfoid='$vendorinfoid'";
		}
		else
		{
			$query = "SELECT * FROM location WHERE circleinfoid='$circleinfoid' AND vendorinfoid='$vendorinfoid' ORDER BY locationid";
		}
		$result = pg_query($conn, $query);
		$info .= "<option value='0'>-- Select Location --</option>";	
		while($row = pg_fetch_array($result))
		{
			$info .= "<option value='".$row['locationid']."'>".$row['sitename']."</option>";
		}
		$info .= "</select>";
		echo $info;
	}
	
?>