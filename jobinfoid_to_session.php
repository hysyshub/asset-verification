<?php
	session_start();
	if($_SESSION['user']=='')
	{
		header('Location: login.php');
	}
	else
	{
		error_reporting(0);
		$jobinfoid = $_POST['jobinfoid'];

		$_SESSION['jobinfoid_val']=$jobinfoid;

		echo "success";
	}
?>