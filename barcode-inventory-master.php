<?php 
session_start();
if($_SESSION['user']=='')
{
	header('Location: login.php');
}
else
{
	error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');
    if(isset($_POST['export_data']))
    {
        include 'php/config.php';

        $conn = pg_connect($conn_string);

        if(!$conn)
        {
            echo "ERROR : Unable to open database";
            exit;
        }

        $sql = "SELECT * FROM (
          SELECT ROW_NUMBER() OVER (PARTITION BY t.locationid ORDER BY barcodeinfoid) AS r, t.*, L.sitecode, L.sitename
          FROM inventorymaster AS t, location AS L WHERE t.locationid=L.locationid AND t.type='1' ) x 
            WHERE 1=1 ORDER BY barcodeinfoid";
        $result1 = pg_query($conn, $sql);

        if (!$result1)
        {
            echo "ERROR : " . pg_last_error($conn);
            exit;
        }

        if(pg_num_rows($result1) > 0){
            $delimiter = ",";
            $filename = "Barcode_inventory_master_" . date('Y-m-d') . ".csv";
            
            //create a file pointer
            $f = fopen('php://memory', 'w');
            
            //set CSV column headers
            $fields = array('Site Code', 'Site Name', 'Barcode Id', 'Barcode Value');
            fputcsv($f, $fields, $delimiter);
            
            //output each row of the data, format line as csv and write to file pointer
            while($row = pg_fetch_array($result1))
            {
                $lineData = array(''.$row['sitecode'].'', ''.$row['sitename'].'', ''.$row['barcodeinfoid'].'', ''.$row['barcode'].'');
                fputcsv($f, $lineData, $delimiter);
            }

            //move back to beginning of file
            fseek($f, 0);
            
            //set headers to download file rather than displayed
            header("Content-Type: text/csv");
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            //output all remaining data on a file pointer
            fpassthru($f);
        }
        exit;
    }
?>
<html>
<head>
<title>Barcode Inventory Master</title>

</head>
<body>
<?php

include 'header.php';

?>
<!-- Page Content Start -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse pull-right" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="import-barcode-inventory.php"style="color:blue;text-align:right;" class="nav-link">Import Barcode Inventory</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  

        <div  class="col-md-12">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <h3>Barcode Inventory Master
                    <input class='btn btn-sm btn-success' value='Export Result' name='export_data' type='submit'></h3>
                <table  id='tieuptable' class='table-hover table-striped table-bordered barcode_inventory_list' style="width:100%">
                    <thead>
                        <tr>
                            <th>Site Code</th>
                            <th>Site Name</th>
                            <th>Barcode Id</th>
                            <th>Barcode Value</th>
                        </tr>
                    </thead>
                </table>
            </form>
        </div>
	</div>
		
<?php include 'footer.php'; }?>

<script>
    $(document).ready(function(){

        $('.barcode_inventory_list').DataTable({           // barcode_inventory_list table         
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :"barcode_inventory_list_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                    $(".barcode_inventory_list_processing").css("display","none");
                }
            }
        });
    });
</script>
</body>
</html>
