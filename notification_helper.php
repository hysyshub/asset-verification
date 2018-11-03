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

//echo "csrf post: " . $_POST['csrf_token']."<br/>";
//echo "csrf session: " . $_SESSION['csrf_token']."<br/>";

if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) 
{

	include 'php/config.php';
	include 'NotificationHub.php';

	$hub = new NotificationHub($azure_notification_hub_string, $azure_notification_hub_name);
	//error_reporting(0);
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "db_conn_error";
		exit;
	}

	$task = quote_smart($_POST['task']);

	// adding new circle info
	if($task=='add_general_notification')
	{
		$info = null;
		$userid = quote_smart($_POST['userid']);
		$gen_notify_sub = quote_smart($_POST['gen_notify_sub']);
		$gen_notify_message = quote_smart($_POST['gen_notify_message']);
		$gen_notify_url = quote_smart($_POST['gen_notify_url']);
		$jobinfoid=NULL;
		$query = "INSERT INTO notifymaster(title,message,url) values('$gen_notify_sub','$gen_notify_message','$gen_notify_url') RETURNING notifymasterid";

		$ret = pg_query($conn, $query);
		if(!$ret) 
		{
			$info = pg_last_error($conn);
		} 
		else 
		{
			$oid = pg_fetch_row($ret);
			$notifymasterid = $oid[0];
			$userid = explode(',',$userid);
			for($i=0;$i<count($userid);$i++)
			{
				$sql = "SELECT * FROM userinfo WHERE userid='$userid[$i]'";
				$result = pg_query($conn, $sql);
				$row = pg_fetch_array($result);
				$emailid = $row['emailid'];

				$query1 = "INSERT INTO gennotifyalloc(notifymasterid,userid) values('$notifymasterid','$userid[$i]')";
				$ret1 = pg_query($conn, $query1);
				if(!$ret1) 
				{
					$info = pg_last_error($conn);
				} 
				else 
				{
					$message = '{"data":{"JobData":"3_'.$gen_notify_sub.'"}}';

					$notification = new Notification("gcm", $message);

					$hub->sendNotification($notification, $emailid);

					$info = 'success';
				}
			}
		}
		pg_close($conn);
		echo $info;
	}
	else
	if($task=='add_job_notification')
	{
		$info = null;
		$jobinfoid = quote_smart($_POST['jobinfoid']);
		$job_notify_sub = quote_smart($_POST['job_notify_sub']);
		$job_notify_message = quote_smart($_POST['job_notify_message']);
		$job_notify_url = quote_smart($_POST['job_notify_url']);

		if (!is_numeric($jobinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$query = "INSERT INTO notifymaster(jobinfoid,title,message,url) values('$jobinfoid','$job_notify_sub','$job_notify_message','$job_notify_url') RETURNING notifymasterid";

		$ret = pg_query($conn, $query);
		if(!$ret) 
		{
			$info = pg_last_error($conn);
		} 
		else 
		{
			$oid = pg_fetch_row($ret);
			$notifymasterid = $oid[0];

			$query1 = "SELECT userid FROM jobinfo WHERE jobinfoid='$jobinfoid'";
			$ret1 = pg_query($conn, $query1);
			$row_user = pg_fetch_array($ret1);
			$userid = $row_user['userid'];
			
			$sql = "SELECT * FROM userinfo WHERE userid='$userid'";
			$result = pg_query($conn, $sql);
			$row = pg_fetch_array($result);
			$emailid = $row['emailid'];

			$query2 = "INSERT INTO gennotifyalloc(notifymasterid,userid) values('$notifymasterid','$userid')";
			$ret2 = pg_query($conn, $query2);
			if(!$ret2) 
			{
				$info = pg_last_error($conn);
			} 
			else 
			{
				$message = '{"data":{"JobData":"1_'.$jobinfoid.'"}}'; //$message = '{"data":{"JobData":"1_239"}}';
				
				$notification = new Notification("gcm", $message);

				$hub->sendNotification($notification, $emailid);

				$info = 'success';
			}
		
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
