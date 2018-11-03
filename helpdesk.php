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
	//error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');
	include 'php/sessioncheck.php';

?>
<html>
<head>
<title>Helpdesk</title>

</head>
<body>
<?php

include 'header.php';

?>
<!-- Page Content start -->
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

                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            <h3>Helpdesk Queries</h3>
            <div class='row'>
                <div class="col-md-2">
                    Users:
                    <select class='form-control form-control-sm query_userid' name='query_userid' id='query_userid' data-column="0" data-live-search="true">
                    <option value='0'>All</option>
                        <?php
                            $query = "SELECT * FROM userinfo";
                            $result = pg_query($conn, $query);

                            if (!$result)
                            {
                                echo "ERROR : " . pg_last_error($conn);
                                exit;
                            }
                            
                            while($row = pg_fetch_array($result))
                            {
                                echo "<option value='".$row['userid']."'>".$row['firstname']." ".$row['lastname']."</option>";
                            }
                        ?>
                    </select >
                </div>
                <div class="col-md-2">
                    Status:<br/>
                    <label><input type='radio' name='status_query' class='status_query' value='1' checked data-column="1"> Open</label>
                    <label><input type='radio' name='status_query'  class='status_query' value='2'  data-column="1"> Closed</label>
                    <label><input type='radio' name='status_query' class='status_query' value='3'  data-column="1"> All</label>
                </div>
                <div class="col-md-2">
                    <label>
                    Last message from date: <input class="form-control form-control-sm start_date" id="start_date" name="start_date" placeholder="YYYY-MM-DD" type="text" data-column="2"/>
                    </label>
                    
                </div>
                <div class="col-md-2">
                    <label>
                    Last message to date: <input class="form-control form-control-sm end_date" id="end_date" name="end_date" placeholder="YYYY-MM-DD" type="text" data-column="3"/>
                    </label>
                </div>
            </div>
            <hr/>
            <table  id='tieuptable' class='table-hover table-striped table-bordered help_desk_list' style="width:100%">
                <thead>
                    <tr>
                        <th>Query ID</th>
                        <th>User Name</th>
                        <th>Type of Query</th>
                        <th>Message</th>
                        <th>Job</th>
                        <th>Last message date</th>
                        <th>View</th>
                    </tr>
                </thead>

            </table>
        </div>
       
    </div>
        


<?php include 'footer.php'; }?>
<script>

    $(document).ready(function(){
       
        //datatable loading from serverside
        var dataTable = $('.help_desk_list').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url :"help_desk_response.php", // json datasource
                type: "post",  // type of method  ,GET/POST/DELETE
                error: function(){
                    $(".help_desk_list_processing").css("display","none");
                }
            }
        });

        //data-table filter "user-id"
        $('.query_userid').on( 'change', function () {
            var i =$(this).attr('data-column');
            var v =$(this).val();
            dataTable.columns(i).search(v).draw();
        } );

        //data-table filter "query status"
        $('.status_query').on( 'change', function () {
            var i =$(this).attr('data-column');
            var v =$(this).val();
            dataTable.columns(i).search(v).draw();
        } );

        //data-table filter "from-date"
        $('.start_date').on( 'change', function (){
            var i =$('.start_date').attr('data-column');  // getting column index
            var v =$('.start_date').val();  // getting search input value
            dataTable.columns(i).search(v).draw();
        });

        //data-table filter "upto-date"
        $('.end_date').on( 'change', function (){
            var i =$('.end_date').attr('data-column');  // getting column index
            var v =$('.end_date').val();  // getting search input value
            dataTable.columns(i).search(v).draw();
        });
    });
</script>
</body>
</html>
