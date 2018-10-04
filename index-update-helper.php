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
	if($task == 'update_about_us')
	{
		$info=null;
		$about = $_POST['edit_about'];

		$conn = pg_connect($conn_string);
		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		$sql = "UPDATE generalinfo SET about='$about'";
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
		echo $info;
	}
	else
	if($task == 'update_contact_us')
	{
		$info=null;
		$contactdetails = $_POST['edit_contact'];

		$conn = pg_connect($conn_string);
		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		$sql = "UPDATE generalinfo SET contactdetails='$contactdetails'";
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
		echo $info;
	}
}
