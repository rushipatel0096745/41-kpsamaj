<?php

require("class.phpmailer.php");

function phpmail($to,$bodyhtml,$subject){

	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->Host = "smtpout.secureserver.net";  /*SMTP server*/

	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Port = 465;
	$mail->Username = "info@41kpsamaj-foundation.org";  /*Username*/
	$mail->Password = "41Kadva@507";    /**Password**/

	$mail->From = "info@41kpsamaj-foundation.org";    /*From address required*/
	$mail->FromName = "41 KP Samaj";
	$mail->AddAddress($to);
	//$mail->AddReplyTo("mail@mail.com");

	$mail->IsHTML(true);

	$mail->Subject = $subject;
	$mail->Body = $bodyhtml;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

	if(!$mail->Send())
	{
		/*echo "Message could not be sent. <p>";
		echo "Mailer Error: " . $mail->ErrorInfo;
		exit;*/
		return 'false';
	}else{
		return 'true';
	}

}
?>