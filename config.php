<?php
require_once __DIR__ . '/loadEnv.php';
$public_key = $_ENV["IZI_PUBLIC_KEY"];
$username = $_ENV["IZI_USER"];
$password = $_ENV["IZI_PASS"];
$sha_key = $_ENV["SHA_KEY_IZI"];
define('USERNAME', $username);
define('PASSWORD', $password);
define('PUBLIC_KEY', $public_key);
define('SHA_KEY', $sha_key);
#GENERALES

$ENTERPRISE = $_ENV["ENTERPRISE"];
$AMOUNT = $_ENV["AMOUNT"];
define("ENTERPRISE", $ENTERPRISE);
define("AMOUNT", $AMOUNT);

#db
$servername = $_ENV["SERVERNAME"];
$username = $_ENV["USERNAMEDB"];
$password = $_ENV["PASSWORDDB"];
$database = $_ENV["DATABASE"];
$soportephone=$_ENV["SOPORTPHONE"];
define("SERVERNAME", $servername);
define("USERNAMEDB", $username);
define("PASSWORDDB", $password);
define("DATABASE", $database);
define("SOPORTPHONE",$soportephone);

#SMTPHOST
$smtpHost=$_ENV["SMTP_HOST"];
$smtpUserName=$_ENV["SMTP_USERNAME"];
$smtpPass=$_ENV["SMTP_PASS"];
$smtpPort=$_ENV["SMTP_PORT"];
$sender=$_ENV["SENDER"];
$urlBodegest=$_ENV["URL_BODEGEST"];
define("SMTPHOST",$smtpHost);
define("SMTPUSERNAME",$smtpUserName);
define("SMTPPASS",$smtpPass);
define("SMTPPORT",$smtpPort);
define("SENDER",$sender);
define("URLBODEGEST",$urlBodegest);


