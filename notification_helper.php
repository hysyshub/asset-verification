<?php
	
	include 'php/config.php';
	include 'NotificationHub.php';

	$hub = new NotificationHub("Endpoint=sb://ihsav-notificationnamespace.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=O25gx5oeD//cOjMSuOKlhGUkLv8Cd+E8rQ1t/bVTDkY=", "ihsav-notification");
	error_reporting(0);
	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "db_conn_error";
		exit;
	}

	$task = $_POST['task'];

	// adding new circle info
	if($task=='add_general_notification')
	{
		$info = null;
		$userid = $_POST['userid'];
		$gen_notify_sub = $_POST['gen_notify_sub'];
		$gen_notify_message = $_POST['gen_notify_message'];
		$gen_notify_url = $_POST['gen_notify_url'];
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
		$jobinfoid = $_POST['jobinfoid'];
		$job_notify_sub = $_POST['job_notify_sub'];
		$job_notify_message = $_POST['job_notify_message'];
		$job_notify_url = $_POST['job_notify_url'];
		
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

?>
