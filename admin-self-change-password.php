<?php 
session_start();
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
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Change Admin Password</title>
<link rel="apple-touch-icon-precomposed" sizes="57x57" href="apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="60x60" href="apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon-precomposed" sizes="76x76" href="apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon-precomposed" sizes="152x152" href="apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16" />
<link rel="icon" type="image/png" href="favicon-128.png" sizes="128x128" />
<meta name="application-name" content="Asset Verification"/>
<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="msapplication-TileImage" content="mstile-144x144.png" />
<meta name="msapplication-square70x70logo" content="mstile-70x70.png" />
<meta name="msapplication-square150x150logo" content="mstile-150x150.png" />
<meta name="msapplication-wide310x150logo" content="mstile-310x150.png" />
<meta name="msapplication-square310x310logo" content="mstile-310x310.png" />

<!-- Bootstrap CSS CDN -->
<link rel="stylesheet" href="js/bootstrap-4.1.0/css/bootstrap.min.css">

<!-- Our Custom CSS -->
<link rel="stylesheet" href="css/style4.css">

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="js/fontawesome-free-5.2.0-web/css/all.css">

<!-- Font Awesome JS -->
<script defer src="js/fontawesome-free-5.2.0-web/js/all.js"></script>

<link href="css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

<link href="css/bootstrap-toggle.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="js/DataTables-1.10.18/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="js/Buttons-1.5.4/css/buttons.bootstrap4.min.css"/>

<link rel="stylesheet" href="css/site.css">
<link rel="stylesheet" href="css/richtext.min.css">
<style>
    .myTable tr:hover:not(.headerStyle):hover 
    {
        background-color: #ddd;
    }
    .headerStyle 
    {
        color: #ffffff;
        font-size: 16px;
        text-align: center;
        font-weight: 500;
        background-color: #07889b;
        vertical-align: middle;
    }
    .img-responsive 
    {
        display:block;
        max-width:100%;
        height:auto;
        margin-left: auto;
        margin-right: auto;
    }
    .scroll-top 
    {
        position:fixed;
        bottom:0;
        right:6%;
        z-index:100;
        background: #f2f3f2;
        font-size:24px;
        border-top-left-radius:3px;
        border-top-right-radius:3px;
    }
    .scroll-top a:link,.scroll-top a:visited 
    {
        color:#222;
    }

    table.responsive-table 
    {
        display: table;
        /* required for table-layout to be used (not normally necessary; included for completeness) */
        table-layout: fixed;
        /* this keeps your columns with fixed with exactly the right width */
        width: auto;
        /* table must have width set for fixed layout to work as expected */
        height: auto;
    }

    #footer
    {
        text-align: center;
        padding: 10px;
    }

    .sidenav {
        height: 100%;
        width: 160px;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        background-color: #39ace7;
        overflow-x: hidden;
        padding-top: 20px;
    }

    .sidenav a {
        padding: 6px 8px 6px 16px;
        text-decoration: none;
        font-size: 25px;
        color: white;
        display: block;
    }

    .sidenav a:hover {
        color: #f1f1f1;
    }

    .main {
        margin-left: 160px; /* Same as the width of the sidenav */
        font-size: 28px; /* Increased text to enable scrolling */
        padding: 0px 10px;
    }

    @media screen and (max-height: 450px) {
        .sidenav {padding-top: 15px;}
        .sidenav a {font-size: 18px;}
    }

    body 
    {
    font-family: arial,verdana;
    font-size: 12px;
    }

    #footer
    {
    text-align: center;
    padding: 10px;
    }

    #tieuptable
    {
    border-collapse:collapse;
    font-family: arial,verdana;
    }

    
    /*#tieuptable
    {
    width:100%;
}*/

    #tieuptable th
    {
color: #000;
background-color: #e5f1ff;
    font-weight: bold;
height: 30px;
    font-size: 12px;
    padding-left: 10px;
    padding-right: 30px;
    padding-top: 10px;
    padding-bottom: 10px;
vertical-align: bottom;
    } 

    #tieuptable td
    {
    text-align: left;
vertical-align: middle;
height: 20px;
    font-size: 12px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 10px;
    padding-right: 10px;
    }
    .user_event
    {
        background:transparent;
        color:black;
    }
    .multiselect-container{
      max-height: 190px;
      width:400px;
      overflow-y:scroll;
    }
</style>
</head>
<body>
<div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href='index.php'><h3 align="center"><b>Asset Verification</b></h3>
                <strong>AV</strong></a>
            </div>

            <ul class="list-unstyled components">
                <li>
                   <div class="dropdown">
              <a class="btn-primary dropdown-toggle" data-toggle="dropdown">Hi <?php echo $_SESSION['user']; ?></a>
              <ul class="dropdown-menu">
                <li><a href="index.php"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;Home</a></li>
                <li><a href="admin-self-change-password.php"  class="user_event"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;Change Password</a></li>
                <li><a href="logout.php?logout" class="user_event"><i class="fa fa-sign-out-alt" aria-hidden="true"></i>&nbsp;Sign Out</a></li>
              </ul>
           </div>                    
                    <a href="userinfo.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Roles
                    </a>
                    <a href="circleinfo.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Circles
                    </a>
                    <a href="vendorinfo.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Vendors
                    </a>
                    <a href="locationinfo.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Locations
                    </a>
                    <a href="jobinfo.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Jobs
                    </a>
                    <a href="view_job_notification.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Notifications
                    </a>
                    <a href="helpdesk.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Helpdesk
                    </a>
                    <a href="user-events-log.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        User Event Logs
                    </a>
                    <a href="asset-tree.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Asset Tree
                    </a>
                    <a href="barcode-inventory-master.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Barcode Inventory Master
                    </a>
                    <a href="barcode-matching-log.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Barcode Matching Log
                    </a>
                    <a href="items-pending-verification.php">
                        <i class="fas fa-chevron-circle-right"></i>
                        Item Verification
                    </a>
                </li>
            </ul>
        </nav>
<?php

include 'php/config.php';

$firstname = $_SESSION['user'];
$emailid = $_SESSION['emailid'];
$conn = pg_connect($conn_string);

if(!$conn)
{
    echo "ERROR : Unable to open database";
    exit;
}

$query = "SELECT admininfoid FROM admininfo WHERE firstname='$firstname' AND emailid='$emailid'";
$result = pg_query($conn, $query);

if (!$result)
{
    echo "ERROR : " . pg_last_error($conn);
    exit;
}

$row = pg_fetch_array($result);
$admininfoid = $row['admininfoid'];
?>
<!-- Page Content  -->
        <div id="content" style="overflow: auto;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%;">
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
                                <a href="index.php" style="color:blue;text-align:right;" class="nav-link">Home</a>
                            </li>
                            <li class="nav-item">
                                <a href="admininfo.php" style="color:blue;text-align:right;" class="nav-link">Admin List</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

        <div  class="col-md-4">
            <h3>Change password</h3>
            <input type='hidden' class='admininfoid' value="<?php echo $admininfoid; ?>">
            <input type='hidden' class='emailid' value="<?php echo $emailid; ?>">
            <input type='hidden' class='firstname' value="<?php echo $firstname; ?>">

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                    <input class="form-control form-control-sm current_password" id="current_password" type="password" placeholder="Current Password" name="current_password" >
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                    <input class="form-control form-control-sm new_password" id="new_password" type="password" placeholder="New Password" name="new_password" >
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                    <input class="form-control form-control-sm confirm_password" id="confirm_password" type="password" placeholder="Confirm New Password" name="confirm_password" >
                </div>
            </div>
            <div class="form-group">
                <div class="status">
                                    
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <button type="button" class="btn btn-sm btn-info btn_update">Update</button>
                </div>
            </div>
        </div>
    </div>
        

<?php include 'footer.php'; }?>

<script>
$(document).ready(function(){
    //event.preventDefault();
    $('.btn_update').click(function(){      //click on btn_update
        var admininfoid = $('.admininfoid').val();
        var emailid = $('.emailid').val();
        var firstname = $('.firstname').val();
        var current_password = $('.current_password').val();
        var new_password = $('.new_password').val();
        var confirm_password = $('.confirm_password').val();
        var task = "admin_change_self_password";
        if(current_password == '' || current_password == null)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Empty! </strong> Current password.</div>");
            return false;
        }
        if (current_password.length < 6)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Password Error! </strong>Current Password should be of minimum 6 characters</div>");
            return false;
        }
        if (current_password.length > 15)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Password Error! </strong>Current Password should be of maximum 15 characters</div>");
            return false;
        }
        if(new_password == '' || new_password == null)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Empty! </strong> New password.</div>");
            return false;
        }
        if (new_password.length < 6)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Password Error! </strong>Password should be of minimum 6 characters</div>");
            return false;
        }
        if (new_password.length > 15)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Password Error! </strong>Password should be of maximum 15 characters</div>");
            return false;
        }
        if(confirm_password == '' || confirm_password == null)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Empty! </strong> Confirm New Password!</div>");
            return false;
        }
        if(new_password != confirm_password)
        {
            $('.status').html("<div class='alert alert-danger'><strong>Password Match Error! </strong>New password & Confirm password not match</div>");
            return false;
        }
        $('.loading_img').show();
        $.ajax({
            type : 'post',
            url : 'updation_helper.php',
            data : 'admininfoid='+admininfoid+'&emailid='+emailid+'&firstname='+firstname+'&current_password='+current_password+'&new_password='+new_password+'&confirm_password='+confirm_password+'&task='+task+'&csrf_token='+encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>'),
            success : function(res)
            {
                $('.loading_img').hide();
                if(res == 'success')
                {
                    $('.status').html("<div class='alert alert-success'><strong>Success! </strong>Password changed successfully</div>");
                    return false;
                }
                else
                {
                    $('.status').html("<div class='alert alert-danger'><strong>Fail! </strong>"+res+"</div>");
                    return false;
                }
            }
        });
    });
});
</script>
</body>
</html>
