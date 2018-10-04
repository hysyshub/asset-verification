<?php
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

echo json_encode($ary);

pg_close($conn);

?>
