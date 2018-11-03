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

	include 'php/config.php';
	include 'php/send_mail.php';
	//error_reporting(0);
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "db_conn_error";
		exit;
	}

//echo "csrf post: " . $_POST['csrf_token']."<br/>";
//echo "csrf session: " . $_SESSION['csrf_token']."<br/>";

if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) 
{

	$task = quote_smart($_POST['task']);

	// adding new circle info
	if($task=='add_circle_info')
	{
		$info = null;
		$circlecode = quote_smart($_POST['circlecode']);
		$circlevalue = quote_smart($_POST['circlevalue']);

		$query_chk = "SELECT * FROM circleinfo WHERE LOWER(circlecode)=LOWER('$circlecode') OR LOWER(circlevalue)=LOWER('$circlevalue')";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(pg_num_rows($ret_query_CHK)>0)
		{
			$info = "duplicate";
		}
		else
		{
			$query = "INSERT INTO circleinfo(circlecode,circlevalue) values('$circlecode','$circlevalue')";
			$ret = pg_query($conn, $query);
			if(!$ret) 
			{
				$info = pg_last_error($conn);
			} 
			else 
			{
				$info = "success";
			}
			
		}
		
		pg_close($conn);
		echo $info;
	}
	else
	// adding new vendor info
	if($task=='add_vendor_info')
	{
		$info = null;
		$vendorname = quote_smart($_POST['vendorname']);
		
		$query_chk = "SELECT * FROM vendorinfo WHERE LOWER(vendorname)=LOWER('$vendorname') ";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(pg_num_rows($ret_query_CHK)>0)
		{
			$info = "duplicate";
		}
		else
		{
			$query = "INSERT INTO vendorinfo(vendorname) values('$vendorname')";
			$ret = pg_query($conn, $query);
			if(!$ret) 
			{
				$info = pg_last_error($conn);
			} 
			else 
			{
				$info = "success";
			}
			
		}
		
		pg_close($conn);
		echo $info;
	}
	else
	// adding new vendor info
	if($task=='add_location_info')
	{
		$info = null;
		$sitecode = quote_smart($_POST['sitecode']);
		$sitename = quote_smart($_POST['sitename']);
		$longitude = quote_smart($_POST['longitude']);
		$lattitude = quote_smart($_POST['lattitude']);
		$address = quote_smart($_POST['address']);
		$towncitylocation = quote_smart($_POST['towncitylocation']);
		$district = quote_smart($_POST['district']);
		$pincode = quote_smart($_POST['pincode']);
		$circleinfoid = quote_smart($_POST['circleinfoid']);
		$vendorinfoid = quote_smart($_POST['vendorinfoid']);
		$technician_name = quote_smart($_POST['technician_name']);
		$technician_contact = quote_smart($_POST['technician_contact']);
		$supervisor_name = quote_smart($_POST['supervisor_name']);
		$supervison_contact = quote_smart($_POST['supervison_contact']);
		$cluster = quote_smart($_POST['cluster']);
		$cluster_manager_name = quote_smart($_POST['cluster_manager_name']);
		$cluster_manager_contact = quote_smart($_POST['cluster_manager_contact']);
		$zone = quote_smart($_POST['zone']);
		$zonal_manager_name = quote_smart($_POST['zonal_manager_name']);
		$zonal_manager_contact = quote_smart($_POST['zonal_manager_contact']);

		if (!is_numeric($longitude) || !is_numeric($lattitude) || !is_numeric($pincode) || !is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$query_chk = "SELECT * FROM location WHERE sitecode='$sitecode' AND LOWER(sitename)=LOWER('$sitename')";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(!$ret_query_CHK) 
		{
			$info = pg_last_error($conn);
		} 
		else 
		{
			if(pg_num_rows($ret_query_CHK)>0)
			{
				$info = "duplicate";
			}
			else
			{
				$query = "INSERT INTO location(sitename,longitude,lattitude,address,circleinfoid,towncitylocation,sitecode,pincode,district,cluster,zone,technician_name,technician_contact,supervisor_name,supervison_contact,cluster_manager_name,cluster_manager_contact,zonal_manager_name,zonal_manager_contact,vendorinfoid) values('$sitename',$longitude,$lattitude,'$address','$circleinfoid','$towncitylocation','$sitecode','$pincode','$district','$cluster','$zone','$technician_name','$technician_contact','$supervisor_name','$supervison_contact','$cluster_manager_name','$cluster_manager_contact','$zonal_manager_name','$zonal_manager_contact','$vendorinfoid')";
				$ret = pg_query($conn, $query);
				if(!$ret) 
				{
					$info = pg_last_error($conn);
				} 
				else 
				{
					$info = "success";
				}

			}
		}

		pg_close($conn);
		echo $info;
	}
	else
	// adding new job info
	if($task=='add_job_info')
	{
		$info = null;
		$jobno = quote_smart($_POST['jobno']);
		$locationid = quote_smart($_POST['locationid']);
		$jobtoken = quote_smart($_POST['jobtoken']);
		$accurdistance = quote_smart($_POST['accurdistance']);
		$errorflg = quote_smart($_POST['errorflg']);

		if (!is_numeric($locationid) || !is_numeric($accurdistance) || !is_numeric($errorflg))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$sql = "SELECT * FROM location WHERE locationid='$locationid'";
		$res = pg_query($conn, $sql);
		if(!$res) 
		{
			$info = pg_last_error($conn);
		} 
		else 
		{
			if (pg_num_rows($res) > 0)
			{
				$row = pg_fetch_array($res);

				$circleinfoid = $row['circleinfoid'];
				$vendorinfoid = $row['vendorinfoid'];

				$query = "INSERT INTO jobinfo(jobno,circleinfoid,locationid,accurdistance,accurdistanceunit,tokenid,status,errorflg,vendorinfoid) values('$jobno','$circleinfoid','$locationid','$accurdistance','m','$jobtoken',0,'$errorflg','$vendorinfoid')";
				$ret = pg_query($conn, $query);
				if(!$ret) 
				{
					$info = pg_last_error($conn);
				} 
				else 
				{
					$info = "success";
				}
			}
			else
			{
				$info = "No such location exists";
			}
		}

		pg_close($conn);
		echo $info;
	}

	else
	// adding new admin info
	if($task=='add_admin_info')
	{
		$info = null;
		$firstname = quote_smart($_POST['firstname']);
		$lastname = quote_smart($_POST['lastname']);
		$emailid = quote_smart($_POST['emailid']);
		$address = quote_smart($_POST['address']);
		$contactnumber = quote_smart($_POST['contactnumber']);
		$is_superadmin = quote_smart($_POST['superadmin']);
		
		if($is_superadmin=='true')
		{
			$superadmin = '1';
		}
		else
		if($is_superadmin=='false')
		{
			$superadmin = '0';
		}
		$password = strtoupper(substr(trim($firstname), -1)).'pav@#sa'.date('Y');

		//$password_hash = hash('sha256', $password);
		$password_hash = hash('sha512', $password);

		$query_chk = "SELECT * FROM admininfo WHERE emailid='$emailid'";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(pg_num_rows($ret_query_CHK)>0)
		{
			$info = "duplicate";
		}
		else
		{
			$query = "INSERT INTO admininfo(firstname,lastname,emailid,address,password,contactnumber,superadmin) values('$firstname','$lastname','$emailid','$address','$password_hash','$contactnumber','$superadmin')";
			$ret = pg_query($conn, $query);
			if(!$ret) 
			{
				$info = pg_last_error($conn);
			} 
			else 
			{
				$subject = "Login details - AVSAPP";
						
				$message = "Hi, $firstname $lastname.<br/><br/>
					Use following details to login to your account <br/><br/>";
				$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
				$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVPAPP Login Details</th></tr>";
				$message .="<tr><th align='left'>Email</th><td colspan='8'>".$emailid."</td></tr>";
				$message .="<tr><th align='left'>Password</th><td colspan='8'>".$password."</td></tr>";
				$message .= "<tr><td colspan='9'>
					Important: Do not share this details with anyone.  <br/> <br/><br/>
					Thanks, <br/> The Asset Verification Team.<br/><br/><br/>
					<b style='background-color:yellow;'><u>NOTE: This is system generated mail,Please do not reply to this mail.</u></b></td></tr></table>";
				$mailto = $emailid;
				$mailtoname = $firstname.' '.$lastname;
				
				$info = SendEmail($subject,$message,$mailto,$mailtoname);
			}
			
		}
		
		pg_close($conn);
		echo $info;
	}

	else
	// adding new admin info
	if($task=='add_user_info')
	{
		$info = null;
		$firstname = quote_smart($_POST['firstname']);
		$lastname = quote_smart($_POST['lastname']);
		$emailid = quote_smart($_POST['emailid']);
		$address = quote_smart($_POST['address']);
		$contactnumber = quote_smart($_POST['contactnumber']);
		$circleinfoid = quote_smart($_POST['circleinfoid']);
		$vendorinfoid = quote_smart($_POST['vendorinfoid']);
		
		if (!is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$password = strtoupper(substr(trim($firstname), -1)).'pav@#sa'.date('Y');

		//$password_hash = hash('sha256', $password);
		$password_hash = hash('sha512', $password);

		$query_chk = "SELECT * FROM userinfo WHERE emailid='$emailid'";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(pg_num_rows($ret_query_CHK)>0)
		{
			$info = "duplicate";
		}
		else
		{
			$query = "INSERT INTO userinfo(firstname,lastname,emailid,address,password,circleinfoid,vendorinfoid,contactnumber) values('$firstname','$lastname','$emailid','$address','$password_hash','$circleinfoid','$vendorinfoid','$contactnumber')";

			$ret = pg_query($conn, $query);
			if(!$ret) 
			{
				$info = pg_last_error($conn);
			} 
			else 
			{
				$subject = "Login details - AVSAPP";
						
				$message = "Hi, $firstname $lastname.<br/><br/>
					Use following details for login. <br/><br/>";
				$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
				$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVPAPP Login Details</th></tr>";
				$message .="<tr><th align='left'>Email</th><td colspan='8'>".$emailid."</td></tr>";
				$message .="<tr><th align='left'>Password</th><td colspan='8'>".$password."</td></tr>";
				$message .= "<tr><td colspan='9'>
					Important: Do not share this details with anyone.  <br/> <br/><br/>
					Thanks, <br/> The Asset Verification Team.<br/><br/><br/>
					<b style='background-color:yellow;'><u>NOTE: This is system generated mail,Please do not reply to this mail.</u></b></td></tr></table>";
				$mailto = $emailid;
				$mailtoname = $firstname.' '.$lastname;
				
				$info = SendEmail($subject,$message,$mailto,$mailtoname);
			}
			
		}
		
		pg_close($conn);
		echo $info;
	}
	else
	if($task=='add_notification')
	{
		$info=null;
		$circleinfoid = quote_smart($_POST['circleinfoid']);
		$notification = quote_smart($_POST['notification']);

		if (!is_numeric($circleinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$conn = pg_connect($conn_string);
		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		$sql = "INSERT INTO usernotifications(circleinfoid,isactive,notification) VALUES('$circleinfoid','1','$notification')";
		$result = pg_query($conn, $sql);
		//exit;
		if (!$result)
		{
			$info = "ERROR : " . pg_last_error($conn);
			exit;
		}
		else
		{
			$info ='success';
		}
		pg_close($conn);
		echo $info;
	}
	else
	{
		echo "Error: Unknown operation";
	}
}
else
{
	echo "Invalid Security Token";
}
?>
