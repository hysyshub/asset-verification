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
else
{
	include 'php/config.php';
	date_default_timezone_set('Asia/Calcutta');

	$task = quote_smart($_POST['task']);
	if($task == 'update_about_us')
	{
		$info=null;
		$about = quote_smart($_POST['edit_about']);

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
		$contactdetails = quote_smart($_POST['edit_contact']);

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
