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
	if($task == 'reply_query')
	{
		$info=null;
		$querymasterid = quote_smart($_POST['querymasterid']);
		$message = quote_smart($_POST['message']);
		$laststatus = quote_smart($_POST['laststatus']);
		//$usertype = quote_smart($_POST['usertype']);
		$admin_email = $_SESSION['emailid'];
		$today = date('Y-m-d H-i-s');

		if (!is_numeric($querymasterid) || !is_numeric($laststatus))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

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
