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
	include 'NotificationHub.php';

	$hub = new NotificationHub("Endpoint=sb://ihsav-notificationnamespace.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=O25gx5oeD//cOjMSuOKlhGUkLv8Cd+E8rQ1t/bVTDkY=", "ihsav-notification");

	date_default_timezone_set('Asia/Calcutta');
	$today = date('Y-m-d H-i-s');
	$type = $_GET['type'];

	$conn = pg_connect($conn_string);

	if(!$conn)
	{
		echo "<script>alert('DB connection error')</script>";
		echo "<script>window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=pending')</script>";
	}

	if($type=='approve')      //for approve item
	{
		$visitinfoid = $_GET['visitinfoid'];
		$query = "SELECT V.visitinfoid, L.sitecode, L.sitename, J.jobinfoid, J.jobno, V.scanneritemone, V.scanneroneimageid, V.descriptionone, V.genimageoneid, V.dateone, V.dropdownone, D1.term AS term1, D2.term AS term2, D3.term AS term3, D4.term AS term4, L.locationid 
			FROM visitinfo AS V
			INNER JOIN dropdownmaster AS D1 ON V.level1termid=D1.termid
			INNER JOIN dropdownmaster AS D2 ON V.level2termid=D2.termid
			INNER JOIN dropdownmaster AS D3 ON V.level3termid=D3.termid
			INNER JOIN dropdownmaster AS D4 ON V.level4termid=D4.termid
			INNER JOIN jobinfo AS J ON V.jobinfoid=J.jobinfoid
			INNER JOIN location AS L ON J.locationid=L.locationid
			WHERE V.jobinfoid > 0 and V.barcodeinfoid IS null AND V.visitinfoid='$visitinfoid'
			ORDER BY V.visitinfoid DESC";
		$result = pg_query($conn, $query);
		if (!$result)
		{
			echo "<script>alert('Query execution error')</script>";
			echo "<script>window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=pending')</script>";
		}

		$row = pg_fetch_array($result);
		$sitecode = $row['sitecode'];
		$jobinfoid = $row['jobinfoid'];
		$term1 = $row['term1'];
		$term2 = $row['term2'];
		$term3 = $row['term3'];
		$term4 = $row['term4'];
		$scanneritemone = $row['scanneritemone'];
		$scanneroneimageid = $row['scanneroneimageid'];
		$descriptionone = $row['descriptionone'];
		$genimageoneid = $row['genimageoneid'];
		$dateone = $row['dateone'];
		$dropdownone = $row['dropdownone'];
		$locationid = $row['locationid'];
		$scanneritemvalue = $row['scanneritemvalue'];

		$barcode = $scanneritemvalue;

		$barcode_info_sql = "INSERT INTO inventorymaster(barcode,locationid,type) VALUES('$barcode','$locationid','1') RETURNING barcodeinfoid";
		
		$result = pg_query($conn, $barcode_info_sql);

		if (!$result)
		{
			echo "<script>alert('Barcode id generation error')</script>";
			echo "<script>window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=pending')</script>";
		}
		else
		{
			$oid = pg_fetch_row($result);
			$barcodeinfoid = $oid[0];

			$sql1 = "UPDATE visitinfo SET barcodeinfoid='$barcodeinfoid',approvedon=now(),approvedtype='2',isrejected='0',rfrejection=null WHERE visitinfoid='$visitinfoid'";       //update approved type='2'
			$result1 = pg_query($conn, $sql1);

			if (!$result1)
			{
				echo "<script>alert('Approval updation error')</script>";
				echo "<script>window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=all')</script>";
			}
			else
			{
				echo "<script>alert('Item approved successfully')</script>";
				echo "<script>window.location.assign('items-pending-verification.php?jobinfoid=0&status_items=all')</script>";
			}
		}
	}
	else
	if($type=='reject')      //for reject item
	{	
		$info = '';
		$visitinfoid = $_GET['visitinfoid'];
		$rfrejection = $_GET['rfrejection'];
		$sql_chk = "SELECT visitinfoid FROM visitinfo WHERE isrejected='1' AND visitinfoid='$visitinfoid'";
		$result_chk = pg_query($conn, $sql_chk);
		if(pg_num_rows($result_chk)>0)
		{
			$info = 'duplicate';
			exit;
		}
		else
		{
			$reject_sql = "UPDATE visitinfo SET rfrejection='$rfrejection',isrejected=1,rejectedon=now() WHERE visitinfoid='$visitinfoid'";
			$result = pg_query($conn, $reject_sql);
			
			$sql = "SELECT V.jobinfoid,J.userid FROM visitinfo as V JOIN jobinfo as J ON V.jobinfoid=J.jobinfoid  WHERE V.visitinfoid='$visitinfoid'";
			$result = pg_query($conn, $sql);
			$row = pg_fetch_array($ret1);
			$jobinfoid = $row['jobinfoid'];
			$userid = $row['userid'];

			$sql1 = "SELECT J.userid,U.emailid FROM jobinfo as J JOIN userinfo as U ON J.userid=U.userid  WHERE J.userid='$userid'";
			$result1 = pg_query($conn, $sql1);
			$row1 = pg_fetch_array($result1);
			$emailid = $row1['emailid'];

			if (!$result)
			{
				$info = "Query execution error";
			}
			else
			{
				$message = '{"data":{"JobData":"2_'.$jobinfoid.'"}}';

				$notification = new Notification("gcm", $message);

				$hub->sendNotification($notification, $emailid);

				$info = "success";
			}
		}
		

		echo $info;
	}
}
?>
