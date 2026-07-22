<?php

date_default_timezone_set("Asia/Calcutta");
@session_start();
@ob_start();

include_once("../config/class.MySQLCN.php");
$obj = new MySQLCN();

$appname = "Shree 41 Gam Kadava Patidar Samaj-Foundation";
$title = $appname;
$server = "dev.41kp.com";
$server_dir = "api/";
$tdate = date("Y-m-d");
$tdatetime = date("Y-m-d H:i:s");
$baseurl = "https://".$server."/".$server_dir;   
$infoEmailId = "info@41kpsamaj-foundation.org"; 

$_DEBUG = 0;

if($_DEBUG == 1){

	error_reporting( E_ALL );
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(-1);

}

?>