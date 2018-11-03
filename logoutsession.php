<?php
session_start();

if (isset($_SESSION['user'])) 
{
	unset($_SESSION['user']);
}

if (isset($_SESSION['emailid'])) 
{
	unset($_SESSION['emailid']);
}

if (isset($_SESSION['admininfoid'])) 
{
	unset($_SESSION['admininfoid']);
}

if (isset($_SESSION['superadmin'])) 
{
	unset($_SESSION['superadmin']);
}

if (isset($_SESSION['csrf_token'])) 
{
	unset($_SESSION['csrf_token']);
}

header("Location: login.php");
exit;

?>
