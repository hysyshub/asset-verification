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

// get locations of perticular circlewise
if($task=='fetch_locations_circlewise')
{
	$info = null;
	$circleinfoid = quote_smart($_POST['circleinfoid']);
	$vendorinfoid = quote_smart($_POST['vendorinfoid']);

	if (!is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
	{
		echo "</select>";
		exit;
	}

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
	$vendorinfoid = quote_smart($_POST['vendorinfoid']);
	$circleinfoid = quote_smart($_POST['circleinfoid']);

	if (!is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
	{
		echo "</select>";
		exit;
	}

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

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at https://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: https://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2017		   		     :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

if ($task=='jobeventlogs_info')
{
	$info = null;
	$jobinfoid = quote_smart($_POST['jobinfoid']); 

	if (!is_numeric($jobinfoid))
	{
		echo "ERROR : Invalid parameter value";
		exit;
	}

	// only job which is started or finished
	$query = "SELECT * FROM jobinfo as J, location as L WHERE J.locationid=L.locationid AND J.jobinfoid='$jobinfoid' AND J.status IN (1, 2)";
	$result = pg_query($conn, $query);

	if (pg_num_rows($result) > 0)
	{
		$row = pg_fetch_array($result); 
		$loc_longitude = $row['longitude'];
		$loc_lattitude = $row['lattitude'];

		// only job start, resume & submit events
		$logquery = "SELECT * FROM usereventsinfo WHERE jobinfoid='$jobinfoid' AND event IN ('JOB_START_SUCCESS','JOB_RESUME_SUCCESS','JOB_SUBMIT_SUCCESS') ORDER BY capturedon";
		$logresult = pg_query($conn, $logquery);
		
		echo "success@#@";
		echo "<div class='table-responsive'><table class='table table-striped table-bordered' style='font-size: 12px;'><thead>
      					<tr>
        					<th>Event</th>
        					<th>Job Latt</th>
					        <th>Job Long</th>
					        <th>User Latt</th>
					        <th>User Long</th>
					        <th>Diff (Kms)</th>
					        <th>Timestamp</th>
					</tr>
					</thead><tbody>";
		while ($logrow = pg_fetch_array($logresult))
		{
			$user_event = $logrow['event'];
			$user_longitude = $logrow['longitude'];
			$user_lattitude = $logrow['latitude'];
			$user_timestamp = date('Y-m-d H:i:s', strtotime($logrow['capturedon']));
			$distance_diff = number_format(distance($loc_lattitude, $loc_longitude, $user_lattitude, $user_longitude, "K"), 3); // kilometers

			echo "<tr>";
			echo "<td>$user_event</td>";
			echo "<td>$loc_lattitude</td>";
			echo "<td>$loc_longitude</td>";
			echo "<td>$user_lattitude</td>";
			echo "<td>$user_longitude</td>";
			echo "<td>$distance_diff</td>";
			echo "<td>$user_timestamp</td>";
			echo "</tr>";
		}
		echo "</tbody></table></div>";
	}
	else
	{
		echo "ERROR: Job not found or started";
	}
}

?>
