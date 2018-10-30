<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "../dbconfig.php";


$condition=" `requests`.`action` != 'terminate'  AND `requests`.`action` != 'customer_information_modification'";
if($_GET['type']=='terminate')
{
  $condition=" `requests`.`action`='terminate'";


}
$query="SELECT `requests`.`request_id`,
               `requests`.`product_price`,
               `requests`.`product_title`,
               `requests`.`verdict`,
               `requests`.`note`,
               `orders`.`order_id`,
               `customers`.`customer_id`,
               `customers`.`full_name`,
               `requests`.`action`,
               `requests`.`action_value`,
               `requests`.`action_on_date`,
               `requests`.`creation_date`,
               `requests`.`modem_id`,
               `resellers`.`full_name` as 'reseller_name',
               `resellers`.`customer_id` as 'reseller_id',
               `admins`.`admin_id`,
               `admins`.`username`
        FROM `requests`
            LEFT JOIN `customers` `resellers` ON `resellers`.`customer_id` = `requests`.`reseller_id`
            LEFT JOIN `orders` ON `orders`.`order_id` = `requests`.`order_id`
            LEFT JOIN `customers` ON `customers`.`customer_id`=`orders`.`customer_id`
            LEFT JOIN `admins` ON `requests`.`admin_id` = `admins`.`admin_id`
       WHERE `requests`.`action_on_date` >= ? AND `requests`.`action_on_date` <= ? AND ".$condition."
        ORDER BY `requests`.`request_id`";

  $stmt1 = $dbTools->getConnection()->prepare($query);

  $stmt1->bind_param('ss',
                    $_GET['date1'],
                    $_GET['date2']);


  $stmt1->execute();

  $requests_result = $stmt1->get_result();

  $requests=array();
  while($request=$dbTools->fetch_assoc($requests_result))
  {
    array_push($requests,$request);
  }

$json = json_encode($requests);
echo "{\"requests\" :", $json, "}";
