<?php 
    session_start();
    require_once 'php/config.php';
    include 'php/send_mail.php';
    error_reporting(0);
    date_default_timezone_set('Asia/Calcutta');

    $errorMessage = '';
	$successMessage = '';
    if(isset($_POST['submit_btn']))       //submit_btn click
    {

        $emailid = trim($_POST['emailid']);
        $emailid = strip_tags($emailid);
        $emailid = htmlspecialchars($emailid);

        $captcha = $_POST['vercode'];
        $conn = pg_connect($conn_string);

        if(!$conn)
        {
            $errorMessage = 'Error establishing database connection.';
        }


        $query = "SELECT * FROM admininfo WHERE emailid='$emailid'";
        $result = pg_query($conn, $query);

        if (!$result)
        {
            $errorMessage = 'Error executing query';
        }

        $count = pg_num_rows($result); 
        $row = pg_fetch_array($result);

        if( $count == 1) 
        {
            if (($_POST['vercode'] != $_SESSION['vercode']) || $_SESSION['vercode']=='')  
            {
                $errorMessage = 'Invalid captcha';
                //echo "<script> return false;</script>";
            }
            else
            {
            	$firstname = $row['firstname'];
            	$lastname = $row['lastname'];
            	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
    			    srand((double)microtime()*1000000);
    		      	$i = 0;
    		      	while ($i <= 5) 
    		      	{
    		            $num = rand() % 33;
    		            $tmp = substr($salt, $num, 1);
    		            $pass = $pass . $tmp;
    		            $i++;
    		      	}
      			    $email_pass = substr(trim(date('m')), -1)."T".$pass;    //random password

                //$tmp_password = hash('sha256',$email_pass);     // random password with sha256
      			    $tmp_password = hash('sha512',$email_pass);     // random password with sha512

      			    $todaytime = date('Y-m-d H:i:s');
      			    $tmp_pass_validity = date('Y-m-d H:i:s', strtotime("+60 minutes"));    //temp pass validity 60 minutes
      			    $update_query = "UPDATE admininfo SET tmp_password='$tmp_password',tmp_password_createdon='$todaytime' WHERE emailid='$emailid'";
      			    $result = pg_query($conn, $update_query);

      		        if (!$result)
      		        {
      		            $errorMessage = 'Error updating database';
      		        }
      		        else
      		        {
      		        	$subject = "Forgot Password - AVAPP Web-Interface";
      						
      					$message = "Hi, $firstname $lastname.<br/><br/>
      						Your temporary password for AVAPP Web interface <br/>
      						Please use following details to login. <br/><br/>";
      					$message .= "<table border='1' cellpadding='5' cellspacing='0'>";
      					$message .="<tr><th colspan='9' align='center' bgcolor='#d9e6f0'>AVAPP Login Details</th></tr>";
      					$message .="<tr><th align='left'>Email</th><td colspan='8'>".$emailid."</td></tr>";
      					$message .="<tr><th align='left'>Temporary Password</th><td colspan='8'>".$email_pass."</td></tr>";
      					$message .="<tr><th align='left'>Expired on</th><td colspan='8'>".$tmp_pass_validity."</td></tr>";
      					$message .= "<tr><td colspan='9'>
      						Important: This password is valid for only 60 minutes.<br/>
      						Sign-in using this temporary password & then change your password.<br/>
      						Do not share this details with anyone.  <br/> <br/><br/>
      						Thanks, <br/> The Asset Verification Team.<br/><br/><br/>
      						<b style='background-color:yellow;'><u>NOTE: This is system generated mail,Please do not reply to this mail.</u></b></td></tr></table>";
      					$mailto = $emailid;
      					$mailtoname = $firstname.' '.$lastname;
      					
      					$info = SendEmail($subject,$message,$mailto,$mailtoname);
					
      					if($info=='success')
      					{
      						$successMessage = "Password successfully send to '$emailid'.";
      					}
      	                else
      	                if($info=='danger')
      					{
      						$errorMessage = 'Error sending email';
      					}
		          }
			    
            }
            
        } 
        else 
        {
            $errorMessage = 'Email-id does not exists.<br/> Please provide correct email-id';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="XvBNaxjvfYAAmfnt75yYa2ftkG02wFSqA2aslOUmzZ2aLSGMNPdS0fad9goHPZVtVCMXXI+DBZ9Dd1viOk1PEQ==" />
<title>Forgot Password</title>
<link rel="stylesheet" media="screen" href="css/fullpage.css" />

  <link href='https://fonts.googleapis.com/css?family=Lato:400,900,400italic,700italic' rel='stylesheet'>

    <script async src="//www.google-analytics.com/analytics.js"></script>
    <!-- jQuery-CDN -->
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>

    <style>
      @import url('https://fonts.googleapis.com/css?family=Poppins');

      /* BASIC */

      html {
        background-color: #454df3;
      }

      body {
        font-family: "Poppins", sans-serif;
        height: 100vh;
      }

      a {
        color: #92badd;
        display:inline-block;
        text-decoration: none;
        font-weight: 400;
      }

      h2 {
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        display:inline-block;
        margin: 40px 8px 10px 8px; 
        color: #cccccc;
      }



      /* STRUCTURE */

      .wrapper {
        display: flex;
        align-items: center;
        flex-direction: column; 
        justify-content: center;
        width: 100%;
        min-height: 100%;
        padding: 20px;
      }

      #formContent {
        -webkit-border-radius: 10px 10px 10px 10px;
        border-radius: 10px 10px 10px 10px;
        background: #fff;
        padding: 30px;
        width: 70%;
        max-width: 400px;
        position: relative;
        padding: 0px;
        -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
        box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
        text-align: center;
      }


      #formFooter {
        background-color: #f6f6f6;
        border-top: 1px solid #dce8f1;
        padding: 25px;
        text-align: center;
        -webkit-border-radius: 0 0 10px 10px;
        border-radius: 0 0 10px 10px;
      }



      /* TABS */

      h2.inactive {
        color: #cccccc;
      }

      h2.active {
        color: #0d0d0d;
        border-bottom: 2px solid #5fbae9;
      }



      /* FORM TYPOGRAPHY*/

      input[type=button], input[type=submit], input[type=reset]  {
        background-color: #454df3;
        border: none;
        color: white;
        padding: 15px 80px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        text-transform: uppercase;
        font-size: 13px;
        -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
        box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
        margin: 5px 20px 40px 20px;
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        -ms-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
      }

      input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover  {
        background-color: #39ace7;
      }

      input[type=button]:active, input[type=submit]:active, input[type=reset]:active  {
        -moz-transform: scale(0.95);
        -webkit-transform: scale(0.95);
        -o-transform: scale(0.95);
        -ms-transform: scale(0.95);
        transform: scale(0.95);
      }

      input[type=text] {
        background-color: #f6f6f6;
        border: none;
        color: #0d0d0d;
        padding: 10px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 5px;
        width: 85%;
        border: 2px solid #f6f6f6;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
      }

      input[type=password] {
        background-color: #f6f6f6;
        border: none;
        color: #0d0d0d;
        padding: 10px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 5px;
        width: 85%;
        border: 2px solid #f6f6f6;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
        -webkit-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
      }

      input[type=text]:focus {
        background-color: #fff;
        border-bottom: 2px solid #5fbae9;
      }

      input[type=text]:placeholder {
        color: #cccccc;
      }

      input[type=password]:focus {
        background-color: #fff;
        border-bottom: 2px solid #5fbae9;
      }

      input[type=password]:placeholder {
        color: #cccccc;
      }


      /* ANIMATIONS */

      /* Simple CSS3 Fade-in-down Animation */
      .fadeInDown {
        -webkit-animation-name: fadeInDown;
        animation-name: fadeInDown;
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
      }

      @-webkit-keyframes fadeInDown {
        0% {
          opacity: 0;
          -webkit-transform: translate3d(0, -100%, 0);
          transform: translate3d(0, -100%, 0);
        }
        100% {
          opacity: 1;
          -webkit-transform: none;
          transform: none;
        }
      }

      @keyframes fadeInDown {
        0% {
          opacity: 0;
          -webkit-transform: translate3d(0, -100%, 0);
          transform: translate3d(0, -100%, 0);
        }
        100% {
          opacity: 1;
          -webkit-transform: none;
          transform: none;
        }
      }

      /* Simple CSS3 Fade-in Animation */
      @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
      @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
      @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

      .fadeIn {
        opacity:0;
        -webkit-animation:fadeIn ease-in 1;
        -moz-animation:fadeIn ease-in 1;
        animation:fadeIn ease-in 1;

        -webkit-animation-fill-mode:forwards;
        -moz-animation-fill-mode:forwards;
        animation-fill-mode:forwards;

        -webkit-animation-duration:1s;
        -moz-animation-duration:1s;
        animation-duration:1s;
      }

      .fadeIn.first {
        -webkit-animation-delay: 0.4s;
        -moz-animation-delay: 0.4s;
        animation-delay: 0.4s;
      }

      .fadeIn.second {
        -webkit-animation-delay: 0.6s;
        -moz-animation-delay: 0.6s;
        animation-delay: 0.6s;
      }

      .fadeIn.third {
        -webkit-animation-delay: 0.8s;
        -moz-animation-delay: 0.8s;
        animation-delay: 0.8s;
      }

      .fadeIn.fourth {
        -webkit-animation-delay: 1s;
        -moz-animation-delay: 1s;
        animation-delay: 1s;
      }

      .fadeIn.fifth {
        -webkit-animation-delay: 1s;
        -moz-animation-delay: 1s;
        animation-delay: 1s;
      }

      /* Simple CSS3 Fade-in Animation */
      .underlineHover:after {
        display: block;
        left: 0;
        bottom: -10px;
        width: 0;
        height: 2px;
        background-color: #56baed;
        content: "";
        transition: width 0.2s;
      }

      .underlineHover:hover {
        color: #0d0d0d;
      }

      .underlineHover:hover:after{
        width: 100%;
      }



      /* OTHERS */

      *:focus {
          outline: none;
      } 

      #icon {
        width:60%;
      }

      * {
        box-sizing: border-box;
      }
    </style>
</head>
<body>
    <div class="wrapper fadeInDown">
        <div id="formContent">
        <!-- Tabs Titles -->
            <h2 class="active"></h2>
            
            <!-- Icon -->
            <div class="fadeIn first">
                <img src="images/av192x192.png" id="icon" alt="User Icon" class='img-responsive' style="width:120px;height:120px;" />
            </div>

            <!-- Login Form -->
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
                <input type="text" id="login" class="fadeIn second" name="emailid" autocomplete="off" value="<?php echo $emailid?>"  placeholder="Registered Email Id" required />
                
		<p>
			<img src="php/captcha.php" class="fadeIn fourth" style="vertical-align: middle;">
			<input type="text" id="vercode" class="fadeIn fourth" style="max-width: 250px;" name="vercode" autocomplete="off" value="<?php echo $captcha?>" placeholder="Enter Captcha" required />
                </p>
                <?php
					if ($errorMessage != '')
					{
						echo "<div class='alert alert-danger' style='padding: 10px; margin-bottom: 10px;'>";
						echo "<strong style='color:red;'>Error! $errorMessage</strong>";
						echo "</div>";
					}
					if ($successMessage != '')
					{
						echo "<div class='alert alert-success' style='padding: 10px; margin-bottom: 10px;'>";
						echo "<strong style='color:green;'>Success! $successMessage </strong>";
						echo "</div>";
					}
				?>
                <input type="submit" class="fadeIn fifth btn btn-info submit_btn" name='submit_btn' value="Submit">
            </form>
            <div class="form-group">
                <center><img src="images/loading.gif" class='img-responsive loading_img' id='loading_img' style='widht:100px;height:100px;display:none;'/></center>
            </div>
            <!-- Remind Passowrd -->
            <div id="formFooter">
                <a class="underlineHover" href="login.php" >Login?</a>
            </div>

        </div>
    </div>
<script>
  $('.submit_btn').click(function(){
    $('.loading_img').show();
  });
</script>
</body>

</html>
