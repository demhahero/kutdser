<?php
include_once $_SERVER['DOCUMENT_ROOT']."/kutdser/mikrotik/dbconfig.php";

include "tools/DBTools.php";


$dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);
$conn_routers->set_charset('utf8mb4');

?>
