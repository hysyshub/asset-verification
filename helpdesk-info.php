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

?>
<html>
<head>
<title>Helpdesk info</title>
<style>

    /* General CSS Setup */
   
    /* container */
    .container {
      padding: 5% 5%;
    }

    /* CSS talk bubble */
    .talk-bubble {
        margin: 40px;
      display: inline-block;
      position: relative;
        width: 200px;
        height: auto;
        background-color: lightyellow;
    }
    .border{
      border: 8px solid #666;
    }
    .round{
      border-radius: 30px;
        -webkit-border-radius: 30px;
        -moz-border-radius: 30px;

    }

    /* Right triangle placed top left flush. */
    .tri-right.border.left-top:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: -40px;
        right: auto;
      top: -8px;
        bottom: auto;
        border: 32px solid;
        border-color: #666 transparent transparent transparent;
    }
    .tri-right.left-top:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: -20px;
        right: auto;
      top: 0px;
        bottom: auto;
        border: 22px solid;
        border-color: lightyellow transparent transparent transparent;
    }

    /* Right triangle, left side slightly down */
    .tri-right.border.left-in:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: -40px;
        right: auto;
      top: 30px;
        bottom: auto;
        border: 20px solid;
        border-color: #666 #666 transparent transparent;
    }
    .tri-right.left-in:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: -20px;
        right: auto;
      top: 38px;
        bottom: auto;
        border: 12px solid;
        border-color: lightyellow lightyellow transparent transparent;
    }

    /*Right triangle, placed bottom left side slightly in*/
    .tri-right.border.btm-left:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
        left: -8px;
      right: auto;
      top: auto;
        bottom: -40px;
        border: 32px solid;
        border-color: transparent transparent transparent #666;
    }
    .tri-right.btm-left:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
        left: 0px;
      right: auto;
      top: auto;
        bottom: -20px;
        border: 22px solid;
        border-color: transparent transparent transparent lightyellow;
    }

    /*Right triangle, placed bottom left side slightly in*/
    .tri-right.border.btm-left-in:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
        left: 30px;
      right: auto;
      top: auto;
        bottom: -40px;
        border: 20px solid;
        border-color: #666 transparent transparent #666;
    }
    .tri-right.btm-left-in:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
        left: 38px;
      right: auto;
      top: auto;
        bottom: -20px;
        border: 12px solid;
        border-color: lightyellow transparent transparent lightyellow;
    }

    /*Right triangle, placed bottom right side slightly in*/
    .tri-right.border.btm-right-in:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: 30px;
        bottom: -40px;
        border: 20px solid;
        border-color: #666 #666 transparent transparent;
    }
    .tri-right.btm-right-in:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: 38px;
        bottom: -20px;
        border: 12px solid;
        border-color: lightyellow lightyellow transparent transparent;
    }
    /*
        left: -8px;
      right: auto;
      top: auto;
        bottom: -40px;
        border: 32px solid;
        border-color: transparent transparent transparent #666;
        left: 0px;
      right: auto;
      top: auto;
        bottom: -20px;
        border: 22px solid;
        border-color: transparent transparent transparent lightyellow;

    /*Right triangle, placed bottom right side slightly in*/
    .tri-right.border.btm-right:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: -8px;
        bottom: -40px;
        border: 20px solid;
        border-color: #666 #666 transparent transparent;
    }
    .tri-right.btm-right:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: 0px;
        bottom: -20px;
        border: 12px solid;
        border-color: lightyellow lightyellow transparent transparent;
    }

    /* Right triangle, right side slightly down*/
    .tri-right.border.right-in:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: -40px;
      top: 30px;
        bottom: auto;
        border: 20px solid;
        border-color: #666 transparent transparent #666;
    }
    .tri-right.right-in:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: -20px;
      top: 38px;
        bottom: auto;
        border: 12px solid;
        border-color: lightyellow transparent transparent lightyellow;
    }

    /* Right triangle placed top right flush. */
    .tri-right.border.right-top:before {
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: -40px;
      top: -8px;
        bottom: auto;
        border: 32px solid;
        border-color: #666 transparent transparent transparent;
    }
    .tri-right.right-top:after{
        content: ' ';
        position: absolute;
        width: 0;
        height: 0;
      left: auto;
        right: -20px;
      top: 0px;
        bottom: auto;
        border: 20px solid;
        border-color: lightyellow transparent transparent transparent;
    }


    /* talk bubble contents */
    .talktext{
      padding: 1em;
        text-align: left;
      line-height: 1.5em;
    }
    .talktext p{
      /* remove webkit p margins */
      -webkit-margin-before: 0em;
      -webkit-margin-after: 0em;
    }

    
</style>
</head>
<body>
<?php

include 'header.php';
include 'php/config.php';

$conn = pg_connect($conn_string);

if(!$conn)
{
    echo "ERROR : Unable to open database";
    exit;
}
$querymasterid = $_GET['querymasterid'];
$query = "SELECT * FROM queryalloc WHERE querymasterid='$querymasterid' ORDER BY textedon DESC";
$result = pg_query($conn, $query);

if (!$result)
{
    echo "ERROR : " . pg_last_error($conn);
    exit;
}

?>
<!-- Page Content start -->
        <div id="content" style='width:100%'>

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info" style='background:#030dcf;'>
                        <i class="fas fa-align-left"></i>
                        
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>
                    <?php
                        $sql="SELECT * FROM queryalloc WHERE querymasterid='$querymasterid' ORDER BY textedon DESC LIMIT 1";
                        $result_sql = pg_query($conn, $sql);
                        $row_sql = pg_fetch_array($result_sql);
                        $querymasterid = $row_sql['querymasterid'];
                        $userid = $row_sql['userid'];
                        $laststatus = $row_sql['laststatus'];
                    ?>
                    <div class="collapse navbar-collapse pull-right" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="helpdesk.php"  style="color:blue;text-align:right;" class="nav-link">Helpdesk Dashboard</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>  
        <div  class="col-md-12">
            
            <div  class="col-md-6">
        <?php
            $sql2="SELECT X.*,U.emailid, U.firstname, U.lastname FROM queryalloc as X JOIN userinfo as U ON X.userid=U.userid WHERE X.querymasterid='$querymasterid' AND X.usertype='0' ORDER BY X.textedon DESC LIMIT 1";
            $result_sql2 = pg_query($conn, $sql2);
            $row_sql2 = pg_fetch_array($result_sql2);
            $username = $row_sql2['firstname']." ".$row_sql2['lastname'];
            $emailid = $row_sql2['emailid'];
            $message = $row_sql2['message'];
            $textedon = $row_sql2['textedon'];

            $sql3="SELECT X.userid, A.emailid, A.firstname, A.lastname FROM queryalloc as X JOIN admininfo as A ON X.userid=A.admininfoid WHERE X.querymasterid='$querymasterid' AND X.usertype='1' ORDER BY X.textedon DESC LIMIT 1";
            $result_sql3 = pg_query($conn, $sql3);
            $row_sql3 = pg_fetch_array($result_sql3);
            $admin_name = $row_sql3['firstname']." ".$row_sql3['lastname'];
        ?>
        <h3>Helpdesk info</h3>
            <!--   reply to query -->
            <?php
                if($laststatus!='2')
                {
                    
            ?>
        
            <form>
                <b class="text-center">Reply Here</b>
                <input type='hidden' class='querymasterid' value='<?php echo $querymasterid;?>'>
                <input type='hidden' class='userid' value='<?php echo $userid;?>'>
                <div class="form-group">
                    <table>
                        <tr>
                            <td style="width:330px;">
                                <input type="text" class="form-control form-control-sm message round" name="message" placeholder="Message for reply" id="message" > 
                            </td>
                            <td>
                                &nbsp;&nbsp;&nbsp;Close query: <input type='checkbox' class='laststatus' style='width:20px;'/>
                            </td>
                            <td>
                                <img src="images/send_message.png" style="width:60px;height:60px;" class="btn_submit">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='alert alert-success success_status' style='display:none;'>
                    <strong>Success!</strong> Reply sent successfully.
                </div>
            </form>
            <hr/>
            <?php
                }
            ?>
            <?php

            while($row = pg_fetch_array($result))
            {
                if($row['usertype']=='0')
                {
                echo "<div class='talk-bubble tri-right round talktext' style='width:75%;border:1px solid red;float:left;'>
                    <p style='color:#673ab7;align:left;'>User : ".$username." wrotes:</p>
                    <p style='color:red;align:left;'>Message : ".$row['message']."</p>
                    <span>on : ".$row['textedon']."</span>
                </div>";

                }
                else
                if($row['usertype']=='1')
                {
                echo "<div class='talk-bubble tri-right round talktext'  style='width:75%;border:1px solid blue;float:right;'>
                        <p style='color:#673ab7;text-align:right;'><u>Admin:</u> ".$admin_name." reply</p>
                        <p style='color:blue;text-align:right;'><u>Message:</u> ".$row['message']."</p>
                        <p style='text-align:right;'>on: ".$row['textedon']."</p>
                    </div>";
                }
            }

            pg_close($conn);
            //echo "admin ID=".$_SESSION['emailid'];
        ?>
            </div>

        </div>
    </div>
        

<?php include 'footer.php'; }?>
<script>
$('.btn_submit').click(function(){                            // btn_submit click
    var querymasterid = $('.querymasterid').val();
    var userid = $('.userid').val();
    var message = $('.message').val();
    var laststatus = $('.laststatus').is(":checked");
    var task = 'reply_query';
    if(laststatus==true)
    {
        laststatus = '2';
    }
    else
    {
        laststatus = '1';
    }
    if(message=='' || message==null)
    {
        $('.status').html("<div class='alert alert-danger'><strong>Empty message!</strong> You have to enter message to reply on this query.</div>");
        return false;
    }
    var data = 'querymasterid='+querymasterid+'&userid='+userid+'&message='+message+'&laststatus='+laststatus+'&task='+task;
    //alert(data);return false;
    $.ajax({
        type : 'post',
        url : 'helpdesk-info-helper.php',
        data : data,
        success : function(res)
        {
            if(res=='conn_error')
            {
                $('.status').html("<div class='alert alert-danger'><strong>DB Connection error!</strong></div>");
                return false;
            }
            else
            if(res=='success')
            {
                $('.success_status').show();
                window.setTimeout(function () {
                    $(".success_status").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    window.location.reload();    
                    });
                }, 2000);
            }
            else
            if(res=='success2')
            {
                $('.success_status').show();
                window.setTimeout(function () {
                    $(".success_status").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    window.location.reload();    
                    });
                }, 2000);
            }
            else
            {
                $('.status').html("<div class='alert alert-danger'><strong>"+res+"</strong></div>");
                return false;
            }
        }
    });
});
</script>
</body>
</html>
