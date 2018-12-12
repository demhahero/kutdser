<?php

include_once "../dbconfig.php";

$query="SELECT count(*) as total_order_sent FROM `orders` WHERE `status`='sent'";
$query_result = $dbTools->query($query);
$result = $dbTools->fetch_assoc($query_result);

$query="SELECT count(*) as total_request_sent FROM `requests` WHERE `verdict` IS NULL OR (`verdict` !='approve' AND `verdict` !='disapprove')";
$query_result = $dbTools->query($query);
$result1 = $dbTools->fetch_assoc($query_result);

$query="SELECT

            IFNULL(count(*),0) as `total_reseller_request_sent`
          FROM `reseller_requests`
          LEFT JOIN
          (
            SELECT count(*) AS `number_of_items`,`reseller_request_items`.`reseller_request_id`,
              IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'approve', 1, 0)), 0) as `approved`,
            IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'disapprove', 1, 0)), 0) as `disapproved`

            FROM `reseller_request_items` GROUP BY `reseller_request_items`.`reseller_request_id`)
          AS `reseller_requests_items` ON `reseller_requests_items`.`reseller_request_id`= `reseller_requests`.`reseller_request_id`
          WHERE `number_of_items`!=(`approved`+`disapproved`)";
$query_result = $dbTools->query($query);
$result2 = $dbTools->fetch_assoc($query_result);

if($result && $result1 && $result2)
{
    echo "{\"total_order_sent\" :", $result["total_order_sent"]
      ,",\"total_request_sent\" :", $result1["total_request_sent"]
      ,",\"total_reseller_request_sent\" :", $result2["total_reseller_request_sent"]
      , ",\"error\":false}";
}
else {
  echo "{\"total_order_sent\" :", "0"
    ,"\"total_request_sent\" :", "0"
    ,"\"total_reseller_request_sent\" :", "0"
    , ",\"error\":true}";
}
?>
