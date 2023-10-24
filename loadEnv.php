<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dontEnv = Dotenv::createImmutable(__DIR__);
$dontEnv->safeLoad();
