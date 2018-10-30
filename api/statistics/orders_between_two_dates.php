<?php

include_once "../dbconfig.php";


$query="SELECT `orders`.`order_id`,
               `orders`.`creation_date`,
               `orders`.`status`,
               `orders`.`product_title`,
               `orders`.`product_category`,
               `orders`.`product_subscription_type`,
               `resellers`.`full_name` as 'reseller_name',
               `customers`.`full_name`,
               `resellers`.`customer_id` as 'reseller_id',
               `customers`.`customer_id`,
               `order_options`.`modem_mac_address`,
               `order_options`.`cable_subscriber`
          FROM `orders`
                INNER JOIN `order_options` ON `order_options`.`order_id`= `orders`.`order_id`
                INNER JOIN `customers` ON `orders`.`customer_id`=`customers`.`customer_id`
                INNER JOIN `customers` `resellers` ON `resellers`.`customer_id` = `orders`.`reseller_id`
          WHERE `orders`.`creation_date` >= ? AND `orders`.`creation_date` <= ?
          ORDER BY `orders`.`order_id` ASC";

  $stmt1 = $dbTools->getConnection()->prepare($query);

  $stmt1->bind_param('ss',
                    $_GET['date1'],
                    $_GET['date2']);


  $stmt1->execute();

  $orders_result = $stmt1->get_result();

  $orders=array();
  while($order=$dbTools->fetch_assoc($orders_result))
  {
    $order["displayed_order_id"]=$order["order_id"];

    if ((int) $order["order_id"] > 10380)
        $order["displayed_order_id"] = (((0x0000FFFF & (int) $order["order_id"]) << 16) + ((0xFFFF0000 & (int) $order["order_id"]) >> 16));

    array_push($orders,$order);
  }
$json = json_encode($orders);
echo "{\"orders\" :" ,$json , "}";


?>
