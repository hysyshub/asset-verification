<?php 
session_start();
error_reporting(0);
if($_SESSION['user']=='')
{
	header('Location: login.php');
}
else
{
	include 'php/config.php';
	date_default_timezone_set('Asia/Calcutta');

	$task = $_POST['task'];
	if($task == 'reply_query')
	{
		$info=null;
		$querymasterid = $_POST['querymasterid'];
		$message = $_POST['message'];
		$laststatus = $_POST['laststatus'];
		//$usertype = $_POST['usertype'];
		$admin_email = $_SESSION['emailid'];
		$today = date('Y-m-d H-i-s');
		$conn = pg_connect($conn_string);

		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		
		$userid = $_SESSION['admininfoid'];
		
		$sql = "INSERT INTO queryalloc(querymasterid,userid,message,textedon,laststatus,usertype) VALUES('$querymasterid','$userid','$message',now(),'$laststatus','1')";
		
		$result = pg_query($conn, $sql);

		if (!$result)
		{
			$info = "ERROR : " . pg_last_error($conn);
			exit;
		}
		else
		{
			$info ='success';
		}

		if($laststatus=='2')
		{
			$sql2 = "UPDATE querymaster SET status='$laststatus' WHERE querymasterid='$querymasterid'";
			$result2 = pg_query($conn, $sql2);
			//exit;
			if (!$result2)
			{
				$info = "ERROR : " . pg_last_error($conn);
				exit;
			}
			else
			{
				$info ='success2';
			}
		}
	}
	echo $info;
}
	
?>