<?php

include_once "dbconfig.php";
if(isset($_GET['reseller_id']))
{

$modems=[];
$result_modems = $conn_routers->query("select * from `modems` where `reseller_id`='" . $_GET['reseller_id'] . "' and `customer_id`='0'");
while ($row_modem = $result_modems->fetch_assoc()) {
  array_push($modems,$row_modem);
}
$json = json_encode($modems);
echo "{\"modems\" :" ,$json , "}";
}
else{
  echo "{\"modems\" :[]}";
}

?>
