<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include 'tools/DBTools.php';
$dbTools = new DBTools();

$site_url="https://www.amprotelecom.com/draft/ResellerPortal";

$db_host 	= "localhost";
$db_username 	= "i3702914_wp1";
$db_password 	= "D@fH(9@QUrGOC7Ki5&*61]&0";
$db_name     	= 'router_copy'; //database name

$link=mysql_connect($db_host,$db_username,$db_password);
$conn=mysql_select_db($db_name,$link);

$connection = new mysqli($db_host,$db_username,$db_password, $db_name);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$servername = "localhost";
$username = "i3702914_wp1";
$password = "D@fH(9@QUrGOC7Ki5&*61]&0";
$dbname = "i3702914_wp1";

// Create connection
$conn_wordpress = new mysqli($servername, $username, $password, $dbname);


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

    $query = mysql_query("select * from `customers` where `session_id`='" . $_COOKIE["session_id"] . "'");
    while ($row = mysql_fetch_array($query)) {
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