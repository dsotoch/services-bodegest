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
define("SERVERNAME", $servername);
define("USERNAMEDB", $username);
define("PASSWORDDB", $password);
define("DATABASE", $database);
