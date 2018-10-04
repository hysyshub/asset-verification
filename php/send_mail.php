<?php
	
	function SendEmail($subject,$message,$mailto,$mailtoname)
	{
		include "php/config.php";
		require_once "PHPMailerAutoload.php";
		date_default_timezone_set('Asia/Calcutta');
		$to = $mailto;
	    $name = $mailtoname;
	    $mail = new PHPMailer();
	    $mail->IsSMTP();
	    $mail->Mailer = 'smtp';
	    //$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
	    $mail->Host = $host; // "ssl://smtp.gmail.com" didn't worked
	    $mail->Port = $port;
	        
	     
	    $mail->Username = $emailUsername;
	    $mail->Password = $emailPassword;
	     
	    $mail->IsHTML(true); // if you are going to send HTML formatted emails
	    $mail->SingleTo = true; // if you want to send a same email to multiple users. multiple emails will be sent one-by-one.
	     
	    $mail->From = $emailUsername;
	    $mail->FromName = $fromname;
	     
	    $mail->addAddress($to,$name);
	         
	    $mail->Subject = $subject;
	    $mail->Body = $message;
	     
	    if(!$mail->Send())
	    {
	        return "danger";
	    }   
	    else
	    {
	        return "success";
	    }
	}
	
?>