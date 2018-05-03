<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include 'tools/DBTools.php';
$dbTools = new DBTools();

$site_url="http://localhost/kutdser-master/ResellerPortal";
$api_url = "http://localhost/kutdser-master/api/";

$db_host 	= "localhost";
$db_username 	= "root";
$db_password 	= "";
$db_name     	= 'router'; //database name



$conn_routers = new mysqli($db_host, $db_username, $db_password, $db_name);


//Authentication
$username = null;
$reseller_id;
$is_new_system = false;
$page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
if ($page != "login.php") {

    $session_id = stripslashes(filter_input(INPUT_COOKIE, 'session_id', FILTER_SANITIZE_SPECIAL_CHARS));

    if ($session_id == FALSE)
        header('Location: '.$site_url.'/login.php');

    $query = $conn_routers->query("select * from `customers` where `session_id`='" . $_COOKIE["session_id"] . "'");
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
if (isset($_GET["do"])) {
    if ($_GET["do"] == "logout") {
        setcookie("session_id", $row["session_id"], time() - (86400 * 30), "/");
        header('Location: '.$site_url.'/login.php');
    }
}
?>