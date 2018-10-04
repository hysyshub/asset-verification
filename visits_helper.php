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
	if($task == 'add_visit')
	{
		$info=null;
		$jobinfoid = $_POST['jobinfoid'];

		$scanner_count = $_POST['scanner_count'];
		$desc_count = $_POST['desc_count'];
		$date_count = $_POST['date_count'];
		$dropdown_count = $_POST['dropdown_count'];
		$image_count = $_POST['image_count'];

		$conn = pg_connect($conn_string);

		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		if($scanner_count!='0' || $desc_count!='0' || $date_count!='0' || $dropdown_count!='0' || $image_count!='0')
		{
			if($scanner_count!='0')
			{
				$scan_visit_type = $_POST['scan_visit_type'];
				$scan_desc = $_POST['scan_desc'];
				$chk_scanner = $_POST['chk_scanner'];

				$scan_desc = explode(',',$scan_desc);
				$chk_scanner = explode(',',$chk_scanner);

				for($i=0;$i<$scanner_count;$i++)
				{
					$sql = "INSERT INTO visits(jobinfoid,visittype,visittypedesc,ismandatory) VALUES('$jobinfoid','$scan_visit_type','$scan_desc[$i]','$chk_scanner[$i]')";
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

			if($desc_count!='0')
			{
				$desc_visit_type = $_POST['desc_visit_type'];
				$desc_desc = $_POST['desc_desc'];
				$chk_desc = $_POST['chk_desc'];

				$desc_desc = explode(',',$desc_desc);
				$chk_desc = explode(',',$chk_desc);

				for($i=0;$i<$desc_count;$i++)
				{
					$sql = "INSERT INTO visits(jobinfoid,visittype,visittypedesc,ismandatory) VALUES('$jobinfoid','$desc_visit_type','$desc_desc[$i]','$chk_desc[$i]')";
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

			if($date_count!='0')
			{
				$date_visit_type = $_POST['date_visit_type'];
				$date_desc = $_POST['date_desc'];
				$chk_date = $_POST['chk_date'];

				$date_desc = explode(',',$date_desc);
				$chk_date = explode(',',$chk_date);

				for($i=0;$i<$date_count;$i++)
				{
					$sql = "INSERT INTO visits(jobinfoid,visittype,visittypedesc,ismandatory) VALUES('$jobinfoid','$date_visit_type','$date_desc[$i]','$chk_date[$i]')";
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

			if($dropdown_count!='0')
			{
				$dropdown_visit_type = $_POST['dropdown_visit_type'];
				$dropdown_desc = $_POST['dropdown_desc'];
				$chk_dropdown = $_POST['chk_dropdown'];

				$dropdown_desc = explode(',',$dropdown_desc);
				$chk_dropdown = explode(',',$chk_dropdown);

				for($i=0;$i<$dropdown_count;$i++)
				{
					$sql = "INSERT INTO visits(jobinfoid,visittype,visittypedesc,ismandatory) VALUES('$jobinfoid','$dropdown_visit_type','$dropdown_desc[$i]','$chk_dropdown[$i]')";
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

			if($image_count!='0')
			{
				$image_visit_type = $_POST['image_visit_type'];
				$image_desc = $_POST['image_desc'];
				$chk_image = $_POST['chk_image'];

				$image_desc = explode(',',$image_desc);
				$chk_image = explode(',',$chk_image);

				for($i=0;$i<$image_count;$i++)
				{
					$sql = "INSERT INTO visits(jobinfoid,visittype,visittypedesc,ismandatory) VALUES('$jobinfoid','$image_visit_type','$image_desc[$i]','$chk_image[$i]')";
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
		}
	}
	else
	if($task == 'update_visit_desc')
	{
		$info=null;
		$visitsid = $_POST['visitsid'];
		$jobinfoid = $_POST['jobinfoid'];
		$visit_desc = $_POST['visit_desc'];
		$new_is_mandatory = $_POST['new_is_mandatory'];	

		$conn = pg_connect($conn_string);

		if(!$conn)
		{
			$info = 'conn_error';
			exit;
		}

		$sql_update_visit_desc = "UPDATE visits SET visittypedesc='$visit_desc',ismandatory='$new_is_mandatory' WHERE visitsid='$visitsid' AND jobinfoid='$jobinfoid'";

		$result = pg_query($conn, $sql_update_visit_desc);

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
	pg_close($conn);
}
?>