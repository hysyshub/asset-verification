<?php
	
	include 'php/config.php';
	include 'php/send_mail.php';
	error_reporting(0);
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "db_conn_error";
		exit;
	}

	$task = $_POST['task'];

	// adding new circle info
	if($task=='add_circle_info')
	{
		$info = null;
		$circlecode = $_POST['circlecode'];
		$circlevalue = $_POST['circlevalue'];

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
		$vendorname = $_POST['vendorname'];
		
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
		$sitecode = $_POST['sitecode'];
		$sitename = $_POST['sitename'];
		$address = $_POST['address'];
		$towncitylocation = $_POST['towncitylocation'];
		$district = $_POST['district'];
		$pincode = $_POST['pincode'];
		$circleinfoid = $_POST['circleinfoid'];
		$vendorinfoid = $_POST['vendorinfoid'];
		$technician_name = $_POST['technician_name'];
		$technician_contact = $_POST['technician_contact'];
		$supervisor_name = $_POST['supervisor_name'];
		$supervison_contact = $_POST['supervison_contact'];
		$cluster = $_POST['cluster'];
		$cluster_manager_name = $_POST['cluster_manager_name'];
		$cluster_manager_contact = $_POST['cluster_manager_contact'];
		$zone = $_POST['zone'];
		$zonal_manager_name = $_POST['zonal_manager_name'];
		$zonal_manager_contact = $_POST['zonal_manager_contact'];

		$query_chk = "SELECT * FROM location WHERE sitecode='$sitecode' AND  LOWER(sitename)=LOWER('$sitename')";
		$ret_query_CHK = pg_query($conn, $query_chk);
		if(pg_num_rows($ret_query_CHK)>0)
		{
			$info = "duplicate";
		}
		else
		{
			$query = "INSERT INTO location(sitename,longitude,lattitude,address,circleinfoid,towncitylocation,sitecode,pincode,district,cluster,zone,technician_name,technician_contact,supervisor_name,supervison_contact,cluster_manager_name,cluster_manager_contact,zonal_manager_name,zonal_manager_contact,vendorinfoid) values('$sitename',null,null,'$address','$circleinfoid','$towncitylocation','$sitecode','$pincode','$district','$cluster','$zone','$technician_name','$technician_contact','$supervisor_name','$supervison_contact','$cluster_manager_name','$cluster_manager_contact','$zonal_manager_name','$zonal_manager_contact','$vendorinfoid')";
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
	// adding new job info
	if($task=='add_job_info')
	{
		$info = null;
		$jobno = $_POST['jobno'];
		$circleinfoid = $_POST['circleinfoid'];
		$locationid = $_POST['locationid'];
		$accurdistance = $_POST['accurdistance'];
		$errorflg = $_POST['errorflg'];
		$vendorinfoid = $_POST['vendorinfoid'];
		
		$sql = "SELECT sitecode FROM location WHERE locationid='$locationid'";
		$res = pg_query($conn, $sql);
		$row = pg_fetch_array($res);

		$sitecode = $row['sitecode'];

		$job_id = substr(trim($jobno), -1);
		
		$query = "INSERT INTO jobinfo(jobno,circleinfoid,locationid,accurdistance,accurdistanceunit,status,errorflg,vendorinfoid) values('$jobno','$circleinfoid','$locationid','$accurdistance','m',0,'$errorflg','$vendorinfoid')";
		$ret = pg_query($conn, $query);
		if(!$ret) 
		{
			$info = pg_last_error($conn);
		} 
		else 
		{
			$info = "success";
		}
		
		pg_close($conn);
		echo $info;
	}

	else
	// adding new admin info
	if($task=='add_admin_info')
	{
		$info = null;
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$emailid = $_POST['emailid'];
		$address = $_POST['address'];
		$contactnumber = $_POST['contactnumber'];
		$is_superadmin = $_POST['superadmin'];
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
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$emailid = $_POST['emailid'];
		$address = $_POST['address'];
		$contactnumber = $_POST['contactnumber'];
		$circleinfoid = $_POST['circleinfoid'];
		$vendorinfoid = $_POST['vendorinfoid'];
		
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
		$circleinfoid = $_POST['circleinfoid'];
		$notification = $_POST['notification'];

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
?>