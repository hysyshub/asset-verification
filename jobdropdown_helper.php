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
	date_default_timezone_set('Asia/Calcutta');

	$info=null;
	$task = quote_smart($_POST['task']);

	if($task=='add_new_terms')
	{
		$jobinfoid = quote_smart($_POST['jobinfoid']);
		if (!is_numeric($jobinfoid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$term_val1_data = quote_smart($_POST['term_val1']);
		$term_val2_data = quote_smart($_POST['term_val2']);
		
		$term_val1 = $term_val1_data;
		$term_val2 = $term_val2_data;
		
		$conn = pg_connect($conn_string);

		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		$term_val1 = explode(',',$term_val1);
		$term_val2 = explode(',',$term_val2);
		
		if($term_val1_data!='')
		{
			
			$sql_term_count1 = "SELECT count(category) as count FROM jobdropdown WHERE jobinfoid='$jobinfoid' AND category='1' GROUP BY category";
			$result_term_count1 = pg_query($conn, $sql_term_count1);

			if (pg_num_rows($result_term_count1)>0)
			{
				$row_term_count1 = pg_fetch_array($result_term_count1);
				$count1=$row_term_count1['count'];
			}
			else
			{
				$count1='0';
			}

			for($i=0;$i<count($term_val1);$i++)
			{
				$j=$count1+$i+1;
				$sql = "INSERT INTO jobdropdown(jobinfoid,indx,category,term) VALUES('$jobinfoid','$j','1','$term_val1[$i]')";
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
			}
		}
		if($term_val2_data!='')
		{
			$sql_term_count2 = "SELECT count(category) as count FROM jobdropdown WHERE jobinfoid='$jobinfoid' AND category='2' GROUP BY category";
			$result_term_count2 = pg_query($conn, $sql_term_count2);

			if (pg_num_rows($result_term_count2)>0)
			{
				$row_term_count2 = pg_fetch_array($result_term_count2);
				$count2=$row_term_count2['count'];
			}
			else
			{
				$count2='0';
			}

			for($i=0;$i<count($term_val2);$i++)
			{
				$j=$count2+$i+1;
				$sql = "INSERT INTO jobdropdown(jobinfoid,indx,category,term) VALUES('$jobinfoid','$j','2','$term_val2[$i]')";
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
			}
		}
		echo $info;
		pg_close($conn);
	}
	else
	if($task == 'update_term')
	{
		$jobdropdownid = quote_smart($_POST['jobdropdownid']);
		if (!is_numeric($jobdropdownid))
		{
			echo "ERROR : Invalid parameter value";
			exit;
		}

		$new_term_val = quote_smart($_POST['new_term_val']);
		$indx = quote_smart($_POST['indx']);
		if (!is_numeric($indx))
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

		$sql = "UPDATE jobdropdown SET term='$new_term_val' WHERE jobdropdownid='$jobdropdownid' AND indx='$indx'";

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

		echo $info;
		pg_close($conn);
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
