<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include "tools/DBTools.php";
$dbTools = new DBTools();

$site_url="http://localhost/kutdser/mikrotik";
$api_url = "http://localhost/kutdser/api/";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "routers_2018_10_17";

$conn_routers = new mysqli($servername, $username, $password, $dbname);

$admin_id = 0;
//Authentication

?>
