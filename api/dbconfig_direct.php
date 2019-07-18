<?php

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include_once "db_credentials.php";


$admin_id = 0;
//Authentication
$page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);

 if (strpos($_SERVER['REQUEST_URI'], 'shop_direct') !== false) {
    include $_SERVER['DOCUMENT_ROOT'] . $root_folder."/api/tools/DBTools.php";
    $dbTools = new DBTools($servername, $dbusername, $dbpassword, $dbname);

    include $_SERVER['DOCUMENT_ROOT'] . $root_folder."/ResellerPortal/tools/DBTools.php";
    $dbToolsReseller = new DBToolsReseller($servername, $dbusername, $dbpassword, $dbname);
    $site_url = $root_url."/ResellerPortal";
    // $api_url = $root_url."/api/";
    $username = "AmProTelecom";
    $reseller_id = 190;
    $is_new_system = true;
}
?>
