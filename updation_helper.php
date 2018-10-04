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

	// update circle info
	if($task=='update_circle_info')
	{
		$info = null;
		$circleinfoid = $_POST['circleinfoid'];
		$circlecode = $_POST['circlecode'];
		$circlevalue = $_POST['circlevalue'];

		$query = "UPDATE circleinfo SET circlecode='$circlecode',circlevalue='$circlevalue' WHERE circleinfoid='$circleinfoid'";
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
	// update vendor info
	if($task=='update_vendor_info')
	{
		$info = null;
		$vendorinfoid = $_POST['edit_vendorinfoid'];
		$vendorname = $_POST['edit_vendorname'];

		$query = "UPDATE vendorinfo SET vendorname='$vendorname' WHERE vendorinfoid='$vendorinfoid'";
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
	// update vendor info
	if($task=='update_location_info')
	{
		$info = null;
		$locationid = $_POST['locationid'];
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

		$query = "UPDATE location SET sitecode='$sitecode',sitename='$sitename',address='$address',towncitylocation='$towncitylocation',district='$district',pincode='$pincode',circleinfoid='$circleinfoid',vendorinfoid='$vendorinfoid',technician_name='$technician_name',technician_contact='$technician_contact',supervisor_name='$supervisor_name',supervison_contact='$supervison_contact',cluster='$cluster',cluster_manager_name='$cluster_manager_name',cluster_manager_contact='$cluster_manager_contact',zone='$zone',zonal_manager_name='$zonal_manager_name',zonal_manager_contact='$zonal_manager_contact' WHERE locationid='$locationid'";
		
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
	// update job info
	if($task=='update_job_info')
	{
		$info = null;
		$jobinfoid = $_POST['jobinfoid'];
		$jobno = $_POST['jobno'];
		$circleinfoid = $_POST['circleinfoid'];
		$locationid = $_POST['locationid'];
		$accurdistance = $_POST['accurdistance'];
		$errorflg = $_POST['errorflg'];
		$vendorinfoid = $_POST['vendorinfoid'];
		
		
		if($errorflg=='on')
		{
			$strict = '1';
		}
		else
		{
			$strict = '0';
		}	
		$query = "UPDATE jobinfo SET circleinfoid='$circleinfoid',locationid='$locationid',accurdistance='$accurdistance',accurdistanceunit='m',errorflg='$strict',vendorinfoid='$vendorinfoid' WHERE jobinfoid='$jobinfoid'";
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
	// update admin info
	if($task=='update_admin_info')
	{
		$info = null;
		$admininfoid = $_POST['admininfoid'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$emailid = $_POST['emailid'];
		$address = $_POST['address'];
		$contactnumber = $_POST['contactnumber'];
		
		
		$query = "UPDATE admininfo SET firstname='$firstname',lastname='$lastname',emailid='$emailid',address='$address',contactnumber='$contactnumber' WHERE admininfoid='$admininfoid'";
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
	// admin password change
	if($task=='change_admin_password')
	{
		$info = null;
		$admininfoid = $_POST['admininfoid'];
		$new_password_alpha = $_POST['new_password'];
		$new_password = $_POST['new_password'];
		$confirm_password = $_POST['confirm_password'];

		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = trim($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		$sql = "SELECT firstname,lastname,emailid FROM admininfo WHERE admininfoid='$admininfoid'";
		$return = pg_query($conn,$sql);
		$row = pg_fetch_array($return);

		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$emailid = $row['emailid'];

		if ($new_password == $confirm_password)
		{
			$haveuppercase = preg_match('/[A-Z]/', $new_passwordtext);
			$havenumeric = preg_match('/[0-9]/', $new_passwordtext);
			$havespecial = preg_match('/[!@#$%]/', $new_passwordtext);

			if (!$haveuppercase)
			{
				$info = 'Password must have atleast one upper case character.';
			}
			else if (!$havenumeric)
			{
				$info = 'Password must have atleast one digit.';
			}
			else if (!$havespecial)
			{
				$info = 'Password must have atleast one of the special characters !@#$%';
			}
			else
			{
				$query = "UPDATE admininfo SET password='$new_password' WHERE admininfoid='$admininfoid'";
				$ret = pg_query($conn, $query);
				if(!$ret) 
				{
					$info = pg_last_error($conn);
				} 
				else
				{
					$subject = "Password update - AVSAPP Web-Interface";
						
					$message = "Hi, $firstname $lastname.<br/><br/>
						Your password updated for AVSAPP Web interface <br/>
						Please use following details to login. <br/><br/>";
					$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
					$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVPAPP Updated Login Details</th></tr>";
					$message .="<tr><th align='left'>Email</th><td colspan='8'>".$emailid."</td></tr>";
					$message .="<tr><th align='left'>Updated Password</th><td colspan='8'>".$new_password_alpha."</td></tr>";
					$message .= "<tr><td colspan='9'>
						Important: Do not share this details with anyone.  <br/> <br/><br/>
						Thanks, <br/> The Asset Verification Team.<br/><br/><br/>
						<b style='background-color:yellow;'><u>NOTE: This is system generated mail,Please do not reply to this mail.</u></b></td></tr></table>";
					$mailto = $emailid;
					$mailtoname = $firstname.' '.$lastname;
					
					$info = SendEmail($subject,$message,$mailto,$mailtoname);
				}
			}
		}
		else
		{
			$info = 'New password & confirm password not same';
		}
		
		pg_close($conn);
		echo $info;
	}

	else
	// admin self password change
	if($task=='admin_change_self_password')
	{
		$info = null;
		$admininfoid = $_POST['admininfoid'];
		$emailid = $_POST['emailid'];
		$firstname = $_POST['firstname'];
		$current_password = $_POST['current_password'];
		$new_password = $_POST['new_password'];
		$confirm_password = $_POST['confirm_password'];

		//$current_password = hash('sha256',$current_password);
		$current_password = hash('sha512',$current_password);
		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = trim($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		if ($new_password == $confirm_password)
		{
			$haveuppercase = preg_match('/[A-Z]/', $new_passwordtext);
			$havenumeric = preg_match('/[0-9]/', $new_passwordtext);
			$havespecial = preg_match('/[!@#$%]/', $new_passwordtext);

			if (!$haveuppercase)
			{
				$info = 'New password must have atleast one upper case character.';
			}
			else if (!$havenumeric)
			{
				$info = 'New password must have atleast one digit.';
			}
			else if (!$havespecial)
			{
				$info = 'New password must have atleast one of the special characters !@#$%';
			}
			else
			{

				$sql = "SELECT * FROM admininfo WHERE admininfoid='$admininfoid'";
				$result = pg_query($conn, $sql);
				if (!$result)
			    {
			        echo "ERROR : " . pg_last_error($conn);
			        exit;
			    }
			    if(pg_num_rows($result)==1)
			    {
			    	$row = pg_fetch_array($result);
			    	$password = $row['password'];
			    	$password1 = $row['password1'];
					$password2 = $row['password2'];

					if($password != $current_password)
					{
						$info = 'Current password does not match with database! <br/>Try again with proper password.';
					}
					else
					{
						if($new_password==$password || $new_password==$password1 || $new_password==$password2 )
						{
							$info = 'New password should not be same as last 3 passwords.';
						}
						else
						{
							$todaytime = date('Y-m-d H:i:s');
							$query = "UPDATE admininfo SET password2='$password1',password1='$password',password='$new_password',updatedon=NOW() WHERE admininfoid='$admininfoid'";
							$ret = pg_query($conn, $query);
							if(!$ret) 
							{
								$info = pg_last_error($conn);
							} 
							else
							{
								$info = 'success';
							}
						}
					}
			    }
			    else
			    {
			    	$info = 'No data found';
			    }
			}
		}
		else
		{
			$info = 'New password & confirm password not same';
		}
		
		pg_close($conn);
		echo $info;
	}

	else
	// update user info
	if($task=='update_user_info')
	{
		$info = null;
		$userid = $_POST['userid'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$emailid = $_POST['emailid'];
		$address = $_POST['address'];
		$contactnumber = $_POST['contactnumber'];
		$circleinfoid = $_POST['circleinfoid'];
		$vendorinfoid = $_POST['vendorinfoid'];
		
		
		$query = "UPDATE userinfo SET firstname='$firstname',lastname='$lastname',emailid='$emailid',address='$address',contactnumber='$contactnumber',circleinfoid='$circleinfoid',vendorinfoid='$vendorinfoid' WHERE userid='$userid'";
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
	// admin password change
	if($task=='change_user_password')
	{
		$info = null;
		$userid = $_POST['userid'];
		$new_password_alpha = $_POST['new_password'];
		$new_password = $_POST['new_password'];
		$confirm_password = $_POST['confirm_password'];

		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = trim($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		$sql = "SELECT * FROM userinfo WHERE userid='$userid'";
		$return = pg_query($conn, $sql);
		if(!$return) 
		{
			echo pg_last_error($conn);
			exit;
		}
		
		$row = pg_fetch_array($return);
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$emailid = $row['emailid'];
		
		if ($new_password == $confirm_password)
		{
			$haveuppercase = preg_match('/[A-Z]/', $new_passwordtext);
			$havenumeric = preg_match('/[0-9]/', $new_passwordtext);
			$havespecial = preg_match('/[!@#$%]/', $new_passwordtext);

			if (!$haveuppercase)
			{
				$info = 'Password must have atleast one upper case character.';
			}
			else if (!$havenumeric)
			{
				$info = 'Password must have atleast one digit.';
			}
			else if (!$havespecial)
			{
				$info = 'Password must have atleast one of the special characters !@#$%';
			}
			else
			{
				$query = "UPDATE userinfo SET password='$new_password' WHERE userid='$userid'";
				$ret = pg_query($conn, $query);
				if(!$ret) 
				{
					$info = pg_last_error($conn);
				} 
				else
				{
					$subject = "Password update - AVSAPP";
						
					$message = "Hi, $firstname $lastname.<br/><br/>
						Your password updated & use following details to login. <br/><br/>";
					$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
					$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVPAPP Updated Login Details</th></tr>";
					$message .="<tr><th align='left'>Email</th><td colspan='8'>".$emailid."</td></tr>";
					$message .="<tr><th align='left'>Updated Password</th><td colspan='8'>".$new_password_alpha."</td></tr>";
					$message .= "<tr><td colspan='9'>
						Important: Do not share this details with anyone.  <br/> <br/><br/>
						Thanks, <br/> The Asset Verification Team.<br/><br/><br/>
						<b style='background-color:yellow;'><u>NOTE: This is system generated mail,Please do not reply to this mail.</u></b></td></tr></table>";
					$mailto = $emailid;
					$mailtoname = $firstname.' '.$lastname;
					
					$info = SendEmail($subject,$message,$mailto,$mailtoname);
				}
			}
		}
		else
		{
			$info = 'New password & confirm password not same';
		}
		
		pg_close($conn);
		echo $info;
	}
	
	else
	// edit notification
	if($task=='update_notification')
	{
		$notification = $_POST['notification'];
		
		$query = "UPDATE usernotifications SET notification='$notification'";
		$ret = pg_query($conn, $query);
		
		if (!$ret)
		{
			$info = "ERROR : " . pg_last_error($conn);
			exit;
		}
		else
		{
			$info ='success';
		}
		echo $info;
	}
?>