<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include_once "db_credentials.php";


$admin_id = 0;
//Authentication
$page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);

if (strpos($_SERVER['REQUEST_URI'], 'mikrotik') !== false) {

  include $_SERVER['DOCUMENT_ROOT']."/kutdser/api/tools/DBTools.php";
  $dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);
  $site_url="http://localhost/kutdser/mikrotik";
  $api_url = "http://localhost/kutdser/api/";

  if ($page != "login.php") {
      $session_id = stripslashes($_SESSION["session_id"]);
      $admin_result = $dbTools->query("SELECT * FROM `admins` WHERE `session_id`='" . $session_id . "' AND `session_id`!=''");
      if(!$admin_row = $admin_result->fetch_assoc()){
          header('Location: '.$site_url.'/login.php');
          die();
      }
      $username = $admin_row["username"];
      $admin_id = $admin_row["admin_id"];
  }
}
else{
  include $_SERVER['DOCUMENT_ROOT']."/kutdser/api/tools/DBTools.php";
  $dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);

  include $_SERVER['DOCUMENT_ROOT']."/kutdser/ResellerPortal/tools/DBTools.php";
  $dbToolsReseller = new DBToolsReseller($servername,$dbusername,$dbpassword,$dbname);
  $site_url="http://localhost/kutdser/ResellerPortal";
  $api_url = "http://localhost/kutdser/api/";
  if ($page != "login.php") {

      $session_id =stripslashes($_SESSION["session_id"]);

      if ($session_id == FALSE)
          header('Location: '.$site_url.'/login.php');

      $query = $dbTools->query("SELECT * FROM `customers` WHERE `session_id`='" . $session_id . "'");
      while ($row  = $query->fetch_assoc()) {
          $username = $row["username"];
          $reseller_id = $row["customer_id"];
          if($row["is_new_system"] == "1")
              $is_new_system = true;
      }

      if ($username == null) {
          header('Location: '.$site_url.'/login.php');
      }
  }
}
?>
