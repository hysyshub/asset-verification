<?php
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
	exit;
}

header('Content-Type: application/json');

include 'php/config.php';

$conn = pg_connect($conn_string);

if(!$conn)
{
	echo "ERROR : Unable to open database";
	exit;
}

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

if(isset($_GET['operation'])) 
{
	$ary = null;
	if ($_GET['operation'] == 'get_node')
	{
		$termid = quote_smart(trim($_GET['id']));

		$query = "";
		if ($termid == '' || $termid == '0' || $termid == null || $termid == '#')
		{
			$query = "SELECT * FROM dropdownmaster WHERE domain=1 AND type=1 ORDER BY indx";
		}
		else
		{
			$query = "SELECT * FROM dropdownmaster WHERE domain IN (SELECT subdomain FROM dropdownmaster WHERE termid='$termid') AND type=1 ORDER BY indx";
		}
		$result = pg_query($conn, $query);

		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}

		$ary = array();
		while($row = pg_fetch_array($result))
		{
			$chary = array();
			$chary['id'] = $row['termid'];
			$chary['text'] = $row['term'];
			if ($row['subdomain'] != '')
				$chary['children'] = true;
			$ary[] = $chary;
			unset($charr);
		}
	}
	else if ($_GET['operation'] == 'create_node' && $_SESSION['superadmin'] == 1)
	{
		$parenttermid = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		$termidtext = isset($_GET['text']) && $_GET['text'] !== '' ? $_GET['text'] : '';
		//$termidposition = isset($_GET['position']) && $_GET['position'] !== '' ? (int)$_GET['position'] : 0;

		// fetch parent details
		$parentquery = "SELECT * FROM dropdownmaster WHERE termid='$parenttermid'";
		$result = pg_query($conn, $parentquery);

		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}
		$row = pg_fetch_array($result);
		$parentgroupno = $row['groupno'];
		$parentsubdomain = $row['subdomain'];

		$termidgroupno = $parentgroupno+1;

		if ($termidgroupno > $max_asset_tree_levels)
		{
			echo "ERROR : Maximum $max_asset_tree_levels levels of Asset Tree are allowed";
			exit;
		}

		// if parent is currently a leaf
		if (!($parentsubdomain != '' && $parentsubdomain != null && $parentsubdomain > 0))
		{
			//get max domain
			$domainquery = "SELECT MAX(domain) AS maxdomain FROM dropdownmaster";
			$result = pg_query($conn, $domainquery);

			if (!$result)
			{
				echo "ERROR : " . pg_last_error($conn);
				exit;
			}
			$row = pg_fetch_array($result);
			$parentsubdomain = $row['maxdomain']+1;

			$subdomainquery = "UPDATE dropdownmaster SET subdomain='$parentsubdomain' WHERE termid='$parenttermid'";
			$result = pg_query($conn, $subdomainquery);
			if (!$result)
			{
				echo "ERROR : " . pg_last_error($conn);
				exit;
			}
		}

		$termiddomain = $parentsubdomain;

		// get index
		$indexquery = "SELECT MAX(indx) AS maxindx FROM dropdownmaster WHERE domain='$termiddomain'";
		$result = pg_query($conn, $indexquery);

		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}
		$row = pg_fetch_array($result);
		$termidindx = $row['maxindx']+1;

		// get grouplabel
		$labelquery = "SELECT grplabel FROM dropdownmaster WHERE groupno='$termidgroupno'";
		$result = pg_query($conn, $labelquery);

		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}
		$row = pg_fetch_array($result);
		$termidgrplabel = $row['grplabel'];


		// insert the new term
		$query = "INSERT INTO dropdownmaster (groupno, grplabel, indx, term, domain, type) VALUES ('$termidgroupno','$termidgrplabel','$termidindx','$termidtext','$termiddomain','1') RETURNING termid";
		$result = pg_query($conn, $query);
		$row = pg_fetch_array($result);

		$ary = array();
		$ary['id'] = $row['termid'];
	}
	else if ($_GET['operation'] == 'rename_node' && $_SESSION['superadmin'] == 1)
	{
		$termid = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		$termidtext = isset($_GET['text']) && $_GET['text'] !== '' ? $_GET['text'] : '';
		$query = "UPDATE dropdownmaster SET term='$termidtext' WHERE termid='$termid'";
		$result = pg_query($conn, $query);
	}
	else if ($_GET['operation'] == 'delete_node' && $_SESSION['superadmin'] == 1)
	{
		$termid = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
		$query = "UPDATE dropdownmaster SET type='0' WHERE termid='$termid' RETURNING domain";
		$result = pg_query($conn, $query);
		
		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}
		$row = pg_fetch_array($result);
		$termiddomain = $row['domain'];

		$chidquery = "SELECT COUNT(*) AS childcount FROM dropdownmaster WHERE domain='$termiddomain' AND type='1'";
		$result = pg_query($conn, $chidquery);
		
		if (!$result)
		{
			echo "ERROR : " . pg_last_error($conn);
			exit;
		}
		$row = pg_fetch_array($result);
		$childcount = $row['childcount'];

		if ($childcount == 0)
		{
			$subdomainquery = "UPDATE dropdownmaster SET subdomain=null WHERE subdomain='$termiddomain'";
			$result = pg_query($conn, $subdomainquery);
		}
	}
	
	echo json_encode($ary);
}

pg_close($conn);

?>
