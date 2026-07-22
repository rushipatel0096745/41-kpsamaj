<?php

require("phpmailer/mail.php");

$to = 'hariwebcreation@gmail.com';
$bodyhtml = '<h1>Test Mail</h1>';
$subject = 'Test Mail Harsh';

echo phpmail($to,$bodyhtml,$subject);

?>