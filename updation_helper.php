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

	include 'php/config.php';
	include 'php/send_mail.php';
	include 'NotificationHub.php';
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

	// update circle info
	if($task=='update_circle_info')
	{
		$info = null;
		$circleinfoid = quote_smart($_POST['circleinfoid']);
		$circlecode = quote_smart($_POST['circlecode']);
		$circlevalue = quote_smart($_POST['circlevalue']);

		if (!is_numeric($circleinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

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
		$vendorinfoid = quote_smart($_POST['edit_vendorinfoid']);
		$vendorname = quote_smart($_POST['edit_vendorname']);

		if (!is_numeric($vendorinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

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
		$locationid = quote_smart($_POST['locationid']);
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

		if (!is_numeric($locationid) || !is_numeric($longitude) || !is_numeric($lattitude) || !is_numeric($pincode) || !is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

		$query_chk = "SELECT * FROM location WHERE sitecode='$sitecode' AND LOWER(sitename)=LOWER('$sitename') AND locationid != '$locationid'";
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
				$query = "UPDATE location SET sitecode='$sitecode',sitename='$sitename',longitude='$longitude',lattitude='$lattitude',address='$address',towncitylocation='$towncitylocation',district='$district',pincode='$pincode',circleinfoid='$circleinfoid',vendorinfoid='$vendorinfoid',technician_name='$technician_name',technician_contact='$technician_contact',supervisor_name='$supervisor_name',supervison_contact='$supervison_contact',cluster='$cluster',cluster_manager_name='$cluster_manager_name',cluster_manager_contact='$cluster_manager_contact',zone='$zone',zonal_manager_name='$zonal_manager_name',zonal_manager_contact='$zonal_manager_contact' WHERE locationid='$locationid'";

				$ret = pg_query($conn, $query);
				if(!$ret) 
				{
					$info = pg_last_error($conn);
				} 
				else 
				{
					$jobqry = "UPDATE jobinfo SET circleinfoid='$circleinfoid',vendorinfoid='$vendorinfoid' WHERE locationid='$locationid'";
					$jobret = pg_query($conn, $jobqry);
					if (!$jobret)
					{
						$info = pg_last_error($conn);
					}
					else
					{
						$info = "success";
					}
				}
			}
		}

		pg_close($conn);
		echo $info;
	}

	else
		// update job info
		if($task=='update_job_info')
		{
		$info = null;
		$jobinfoid = quote_smart($_POST['jobinfoid']);
		$jobno = quote_smart($_POST['jobno']);
		$locationid = quote_smart($_POST['locationid']);
		$jobtoken = quote_smart($_POST['jobtoken']);
		$accurdistance = quote_smart($_POST['accurdistance']);
		$errorflg = quote_smart($_POST['errorflg']);
		
		if (!is_numeric($jobinfoid) || !is_numeric($locationid) || !is_numeric($accurdistance) || !is_numeric($errorflg))
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

				$query = "UPDATE jobinfo SET jobno='$jobno',circleinfoid='$circleinfoid',locationid='$locationid',tokenid='$jobtoken',accurdistance='$accurdistance',accurdistanceunit='m',errorflg='$errorflg',vendorinfoid='$vendorinfoid' WHERE jobinfoid='$jobinfoid'";
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
	// update admin info
	if($task=='update_admin_info' && $_SESSION['superadmin'] == 1)
	{
		$info = null;
		$admininfoid = quote_smart($_POST['admininfoid']);
		$firstname = quote_smart($_POST['firstname']);
		$lastname = quote_smart($_POST['lastname']);
		$emailid = quote_smart($_POST['emailid']);
		$address = quote_smart($_POST['address']);
		$contactnumber = quote_smart($_POST['contactnumber']);
		$superadmin = quote_smart($_POST['superadmin']);
		
		if (!is_numeric($admininfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

		$query = "UPDATE admininfo SET firstname='$firstname',lastname='$lastname',emailid='$emailid',address='$address',contactnumber='$contactnumber',superadmin='$superadmin' WHERE admininfoid='$admininfoid'";
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
	if($task=='change_admin_password' && $_SESSION['superadmin'] == 1)
	{
		$info = null;
		$admininfoid = quote_smart($_POST['admininfoid']);
		$new_password_alpha = quote_smart($_POST['new_password']);
		$new_password = quote_smart($_POST['new_password']);
		$confirm_password = quote_smart($_POST['confirm_password']);

		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = quote_smart($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		if (!is_numeric($admininfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

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
					$subject = "Password changed - AVApp Web Login";
						
					$message = "Hi $firstname $lastname,<br/><br/>
						Your password for AVApp Web Login was changed on " . date('d-M-Y H:i:s') . ".<br/><br/>
						Please use the following details to login. <br/><br/>";
					$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
					$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVApp Updated Login Details</th></tr>";
					$message .="<tr><th align='left'>Email Id</th><td colspan='8'>".$emailid."</td></tr>";
					$message .="<tr><th align='left'>Updated Password</th><td colspan='8'>".$new_password_alpha."</td></tr>";
					$message .= "<tr><td colspan='9'>
						Important: Do not share this with anyone.<br/><br/>
						Thanks,<br/>Asset Verification Team<br/><br/>
						<i>Note: This is a system generated email. Please do not reply to this email.</i></td></tr></table>";
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
		$admininfoid = quote_smart($_POST['admininfoid']);
		$emailid = quote_smart($_POST['emailid']);
		$firstname = quote_smart($_POST['firstname']);
		$current_password = quote_smart($_POST['current_password']);
		$new_password = quote_smart($_POST['new_password']);
		$confirm_password = quote_smart($_POST['confirm_password']);

		//$current_password = hash('sha256',$current_password);
		$current_password = hash('sha512',$current_password);
		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = quote_smart($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		if (!is_numeric($admininfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		

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
		$userid = quote_smart($_POST['userid']);
		$firstname = quote_smart($_POST['firstname']);
		$lastname = quote_smart($_POST['lastname']);
		$emailid = quote_smart($_POST['emailid']);
		$address = quote_smart($_POST['address']);
		$contactnumber = quote_smart($_POST['contactnumber']);
		$circleinfoid = quote_smart($_POST['circleinfoid']);
		$vendorinfoid = quote_smart($_POST['vendorinfoid']);
		
		if (!is_numeric($userid) || !is_numeric($circleinfoid) || !is_numeric($vendorinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
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
		$userid = quote_smart($_POST['userid']);
		$new_password_alpha = quote_smart($_POST['new_password']);
		$new_password = quote_smart($_POST['new_password']);
		$confirm_password = quote_smart($_POST['confirm_password']);

		//$new_password = hash('sha256',$new_password);
		$new_password = hash('sha512',$new_password);
		$new_passwordtext = quote_smart($_POST['new_password']);
		//$confirm_password = hash('sha256',$confirm_password);
		$confirm_password = hash('sha512',$confirm_password);
		
		if (!is_numeric($userid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
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
					$subject = "Password changed - AVApp";
						
					$message = "Hi $firstname $lastname,<br/><br/>
						Your password for AVApp was changed on " . date('d-M-Y H:i:s') . ".<br/><br/>
						Please use the following details to login. <br/><br/>";
					$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
					$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVApp Updated Login Details</th></tr>";
					$message .="<tr><th align='left'>Email Id</th><td colspan='8'>".$emailid."</td></tr>";
					$message .="<tr><th align='left'>Updated Password</th><td colspan='8'>".$new_password_alpha."</td></tr>";
					$message .= "<tr><td colspan='9'>
						Important: Do not share this with anyone.<br/><br/>
						Thanks,<br/>Asset Verification Team<br/><br/>
						<i>Note: This is a system generated email. Please do not reply to this email.</i></td></tr></table>";
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
		$notification = quote_smart($_POST['notification']);
		
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

	else
	// resetting user
	if($task=='reset_app_user')
	{
		$userid = quote_smart($_POST['userid']);
		
		if (!is_numeric($userid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
		$query = "UPDATE userinfo SET deviceinfo=null,tokenid=null,longitude=null,lattitude=null,loggedinon=null WHERE userid='$userid'";
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

	else
	// resetting job
	if($task=='reset_jobinfo')
	{
		$jobinfoid = quote_smart($_POST['jobinfoid']);
		
		if (!is_numeric($jobinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
		//$query1 = "DELETE FROM visitinfo WHERE jobinfoid='$jobinfoid'";
		$query2 = "UPDATE jobinfo SET userid=null,starttime=null,endtime=null,status='0' WHERE jobinfoid='$jobinfoid'";
		//$ret1 = pg_query($conn, $query1);
		$ret2 = pg_query($conn, $query2);
		//if (!$ret1 && !$ret2)      // uncomment this line for delete data from visitinfo
		if (!$ret2)                  // comment this line for delete data
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

	//Approve item
	else
	if($task=='approve_item')      //for approve items
	{
		$visitinfoid = quote_smart($_POST['item_id']);
		
		if (!is_numeric($visitinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
		$query = "SELECT V.visitinfoid, V.scanneritemvalue, L.sitename, J.jobinfoid, J.jobno, L.locationid 
			FROM visitinfo AS V
			INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
			INNER JOIN location AS L ON J.locationid=L.locationid
			WHERE V.jobinfoid > 0 and V.barcodeinfoid IS null AND V.visitinfoid='$visitinfoid'
			ORDER BY V.visitinfoid DESC";
		$result = pg_query($conn, $query);
		if (!$result)
		{
			echo "DB Connection error.";
		}

		$row = pg_fetch_array($result);
		$jobinfoid = $row['jobinfoid'];
		$locationid = $row['locationid'];
		$scanneritemvalue = $row['scanneritemvalue'];

		$barcode = $scanneritemvalue;

		$barcode_info_sql = "INSERT INTO inventorymaster(barcode,locationid,type) VALUES('$barcode','$locationid','1') RETURNING barcodeinfoid";
		
		$result = pg_query($conn, $barcode_info_sql);

		if (!$result)
		{
			echo "Barcode id generation error";
		}
		else
		{
			$oid = pg_fetch_row($result);
			$barcodeinfoid = $oid[0];

			$sql1 = "UPDATE visitinfo SET barcodeinfoid='$barcodeinfoid',approvedon=now(),approvedtype='2',isrejected='0',rfrejection=null,rejectedon=null WHERE visitinfoid='$visitinfoid'";       //update approved type='2'
			$result1 = pg_query($conn, $sql1);

			if (!$result1)
			{
				echo "Approval updation error";
			}
			else
			{
				echo "success";
			}
		}
	}

	//reject item
	else
	if($task=='reject_item')      //for reject item
	{	
		$hub = new NotificationHub($azure_notification_hub_string, $azure_notification_hub_name);
	
		$info = '';
		$visitinfoid = quote_smart($_POST['item_id']);
		$rfrejection = quote_smart($_POST['rfrejection']);
		
		if (!is_numeric($visitinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
		$reject_sql = "UPDATE visitinfo SET approvedon=null,approvedtype='0',rfrejection='$rfrejection',isrejected=1,rejectedon=now() WHERE visitinfoid='$visitinfoid'";
		$result = pg_query($conn, $reject_sql);
		
		$sql = "SELECT V.jobinfoid,J.userid FROM visitinfo as V JOIN jobinfo as J ON V.jobinfoid=J.jobinfoid  WHERE V.visitinfoid='$visitinfoid'";
		$result = pg_query($conn, $sql);
		$row = pg_fetch_array($result);
		$jobinfoid = $row['jobinfoid'];
		$userid = $row['userid'];

		$sql1 = "SELECT J.userid,U.emailid FROM jobinfo as J JOIN userinfo as U ON J.userid=U.userid  WHERE J.userid='$userid'";
		$result1 = pg_query($conn, $sql1);
		$row1 = pg_fetch_array($result1);
		$emailid = $row1['emailid'];
		
		if (!$result)
		{
			$info = "Query execution error";
		}
		else
		{
			$message = '{"data":{"JobData":"2_'.$jobinfoid.'"}}';

			$notification = new Notification("gcm", $message);

			$hub->sendNotification($notification, $emailid);

			$info = "success";
		}
		 
		echo $info;
	}
	else if ($task=='update_visitinfo' && $_SESSION['superadmin'] == 1)
	{
		$info = null;
		$visitinfoid = quote_smart($_POST['visitinfoid']);
		$serialnumber = quote_smart($_POST['serialnumber']);
		$modelnumber = quote_smart($_POST['modelnumber']);
		$desc1 = quote_smart($_POST['desc1']);
		$desc2 = quote_smart($_POST['desc2']);
		$desc3 = quote_smart($_POST['desc3']);
		$desc4 = quote_smart($_POST['desc4']);
		$dropdown1 = quote_smart($_POST['dropdown1']);
		$dropdown2 = quote_smart($_POST['dropdown2']);
		
		if (!is_numeric($visitinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}		
		
		$query = "UPDATE visitinfo SET scanneritemone='$serialnumber',scanneritemtwo='$modelnumber',descriptionone='$desc1',descriptiontwo='$desc2',descriptionthree='$desc3',descriptionfour='$desc4',dropdownone='$dropdown1',dropdowntwo='$dropdown2' WHERE visitinfoid='$visitinfoid'";
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
	{
		echo "Error: Unknown operation";
	}

}
else
{
	echo "Invalid Security Token";
}
?>
