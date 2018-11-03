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
include 'php/sessioncheck.php';

$task = quote_smart($_POST['task']);

// circle info
if($task=='circle_info')
{
	$circleinfoid = quote_smart($_POST['circleinfoid']);
	if (!is_numeric($circleinfoid))
	{
		echo "ERROR : Invalid parameter value";
		exit;
	}

	$query = "SELECT * FROM circleinfo WHERE circleinfoid='$circleinfoid'";
	$result = pg_query($conn, $query);
	$row = pg_fetch_array($result);
	echo $row['circleinfoid'].','.$row['circlecode'].','.$row['circlevalue'];
}
else if($task=='location_info') // vendor info
{
	$locationid = quote_smart($_POST['locationid']);
	if (!is_numeric($locationid))
	{
		echo "ERROR : Invalid parameter value";
		exit;
	}

	$query = "SELECT * FROM location AS L, circleinfo AS C, vendorinfo AS V WHERE L.circleinfoid=C.circleinfoid AND L.vendorinfoid=V.vendorinfoid AND L.locationid='$locationid' ORDER BY L.locationid ";
	$result = pg_query($conn, $query);
	$row = pg_fetch_array($result);
	echo $row['locationid'].','.$row['sitecode'].','.$row['sitename'].','.$row['address'].','.$row['towncitylocation'].','.$row['district'].','.$row['pincode'].','.$row['circleinfoid'].','.$row['vendorinfoid'].','.$row['technician_name'].','.$row['technician_contact'].','.$row['supervisor_name'].','.$row['supervison_contact'].','.$row['cluster'].','.$row['cluster_manager_name'].','.$row['cluster_manager_contact'].','.$row['zone'].','.$row['zonal_manager_name'].','.$row['zonal_manager_contact'].','.$row['circlevalue'].','.$row['vendorname'];
}
else if ($task=='jobdropdown_info')
{
	$jobid = quote_smart($_POST['jobid']);
	if (!is_numeric($jobid))
	{
		echo "ERROR : Invalid parameter value";
		exit;
	}

	$query = "SELECT * FROM jobdropdown WHERE jobinfoid='$jobid' ORDER BY category, indx";
	$result = pg_query($conn, $query);
	echo "success";
	$curr_category = 0;
	while ($row = pg_fetch_array($result))
	{
		if ($curr_category != $row['category'])
			echo "@#@";

		echo "<option value='" . $row['term'] . "'>" . $row['term'] . "</option>";

		$curr_category = $row['category'];
	}
}
?>
