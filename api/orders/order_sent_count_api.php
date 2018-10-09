<?php

include_once "../dbconfig.php";

$query="SELECT count(*) as total_order_sent FROM `orders` WHERE `status`='sent'";
$query_result = $dbTools->query($query);
$result = $dbTools->fetch_assoc($query_result);
if($result)
{
    echo "{\"total_order_sent\" :", $result["total_order_sent"]
      , ",\"error\":false}";
}
else {
  echo "{\"total_order_sent\" :", "0"
    , ",\"error\":true}";
}
?>
