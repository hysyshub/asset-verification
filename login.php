<?php 
session_start();
error_reporting(0);
if(isset($_SESSION['user']) && $_SESSION['user'] != '')
{
	header('Location: index.php');
	exit;
}

require_once 'php/config.php';
//error_reporting(0);
date_default_timezone_set('Asia/Calcutta');

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

//{
//if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
if(isset($_POST['password']))
{	    
	//your site secret key
	$secret = '6Lcqo2IUAAAAABrY-AIX_EADg5N1WOrR53QFUr0G';
	//get verify response data
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if(1)
	{

		$emailid = quote_smart($_POST['emailid']);
		$password = quote_smart($_POST['password']);

		$conn = pg_connect($conn_string);


    //print_r($conn);exit;

		if(!$conn)
		{
			$errorMessage = 'Error establishing database connection.';
		}
		else
		{
			//$password = hash('sha256', $password);
			$password = hash('sha512', $password);

			$query = "SELECT * FROM admininfo WHERE emailid='$emailid'";
			$result = pg_query($conn, $query);

      //print_r($result);exit;

			if (!$result)
			{
				$errorMessage = 'Error executing query';
			}
			else
			{

				$count = pg_num_rows($result); 
				$row = pg_fetch_array($result);

				if( $count == 1) 
				{
					if( $row['password']==$password ) //For current password
					{
						session_regenerate_id();
						$_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
						$_SESSION['user'] = $row['firstname'];
						$_SESSION['emailid'] = $row['emailid'];
						$_SESSION['admininfoid'] = $row['admininfoid'];
						$_SESSION['superadmin'] = $row['superadmin'];

						$sess_qry = "UPDATE admininfo SET sessionid='" . session_id() . "', loggedinon='" . date('Y-m-d H:i:s') . "' WHERE emailid='$emailid'";
						$result = pg_query($conn, $sess_qry);
						if (!$result)
						{
							$errorMessage = 'Error updating session';
						}
						else
						{
							header('Location: index.php');
							exit;
						}
					}
					else if( $row['tmp_password']==$password ) //For temporary password
					{
						$today = date('Y-m-d H:i:s');
						$tmp_password_createdon = $row['tmp_password_createdon'];
						$tmp_pass_validity=date("Y-m-d H:i:s",strtotime('+24 hours', strtotime($tmp_password_createdon)));
						if($tmp_pass_validity>=$today)
						{
							session_regenerate_id();
							$_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
							$_SESSION['user'] = $row['firstname'];
							$_SESSION['emailid'] = $row['emailid'];
							$_SESSION['admininfoid'] = $row['admininfoid'];
							$_SESSION['superadmin'] = $row['superadmin'];

							$sess_qry = "UPDATE admininfo SET sessionid='" . session_id() . "', loggedinon='" . date('Y-m-d H:i:s') . "' WHERE emailid='$emailid'";
							$result = pg_query($conn, $sess_qry);
							if (!$result)
							{
								$errorMessage = 'Error updating session';
							}
							else
							{
								header('Location: index.php');
								exit;
							}
						}
						else
						{
							$errorMessage = "Temporary password expired.<br/>
								Please click on forgot password link to generate another temporary password.";
						}  
					}
					else
					{
						$errorMessage = 'Wrong Email Id / Password!';
					}
				} 
				else 
				{
					$errorMessage = 'Wrong Email Id / Password!';
				}
			}
		}
	}
	else
	{
		$errorMessage = 'reCAPTCHA verification failed, please try again.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In</title>
<link rel="stylesheet" media="screen" href="css/fullpage.css" />

  <!--link href='https://fonts.googleapis.com/css?family=Lato:400,900,400italic,700italic' rel='stylesheet'-->

    <style>
      /*@import url('https://fonts.googleapis.com/css?family=Poppins');*/

      /* BASIC CSS */

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
	width: 100%;
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

      input[type=button], input[type=submit], input[type=reset], button  {
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

      input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover, button:hover  {
        background-color: #39ace7;
      }

      input[type=button]:active, input[type=submit]:active, input[type=reset]:active, button:active  {
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

<script>
function onSubmit(token)
{
     document.getElementById("frmLogin").submit();
}

function validate(event) 
{
    event.preventDefault();
    if (!document.getElementById('login').value)
    {
      	alert("Please enter your Email Id!");
    }
    else if (!document.getElementById('password').value)
    {
      	alert("Please enter your Password!");
    }    
    else 
    {
      	grecaptcha.execute();
    }
}

function onload() 
{
    var element = document.getElementById('recaptcha-submit');
    element.onclick = validate;
}
</script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
            <form method="post" name="frmLogin" id="frmLogin" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
                <input type="text" id="login" class="fadeIn second" name="emailid" autocomplete="off" value="<?php echo $emailid?>" placeholder="Email Id" required />
                <input type="password" id="password" class="fadeIn third" name="password" autocomplete="off" value="<?php echo $password?>" placeholder="Password" required />
		<!--p>
			<img src="php/captcha.php" class="fadeIn fourth" style="vertical-align: middle;">
			<input type="text" id="vercode" class="fadeIn fourth" style="max-width: 250px;" name="vercode" autocomplete="off" value="<?php echo $captcha?>" placeholder="Enter Captcha" required />
		</p-->
		<div align='center' style='margin: 5px;'
			id='recaptcha' 
			class="g-recaptcha"
			data-sitekey="6Lcqo2IUAAAAAMac1Ev32prXPIvLdAcI_8UpM6Cn"
			data-callback="onSubmit"
			data-size="invisible"
			data-badge="inline">
		</div>
    <?php
      if ($errorMessage != '')
      {
        echo "<div class='alert alert-danger' style='padding: 10px; margin-bottom: 10px;'>";
        echo "<strong style='color:red;'>Error! $errorMessage</strong>";
        echo "</div>";
      }
      if ($_SESSION['errormessage'] != '')
      {
        echo "<div class='alert alert-danger' style='padding: 10px; margin-bottom: 10px;'>";
        echo "<strong style='color:red;'>Error! " . $_SESSION['errormessage'] . "</strong>";
	echo "</div>";
	unset($_SESSION['errormessage']);
      }
      if ($successMessage != '')
      {
        echo "<div class='alert alert-success' style='padding: 10px; margin-bottom: 10px;'>";
        echo "<strong style='color:green;'>Success! $successMessage </strong>";
        echo "</div>";
      }
    ?>
		<button name="recaptcha-submit" id="recaptcha-submit" class="fadeIn fifth btn btn-info">Login</button>
	    <script>onload();</script>
            </form>

            <!-- Remind Passowrd -->
            <div id="formFooter">
                <a class="underlineHover" href="admin_forgot_password.php" >Forgot Password?</a>
            </div>

        </div>
    </div>

</body>

</html>
