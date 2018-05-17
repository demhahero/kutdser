<?php

include_once "dbconfig.php";
$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
    "product_title" => "product_title",
    "product_category" => "product_category",
    "modem_mac_address" => "modem_mac_address",
   "product_subscription_type" => "product_subscription_type",
    "cable_subscriber" => "cable_subscriber",
    "displayed_order_id" => "order_id"
    );
$childFields=array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
);
$child2Fields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);

$orders = $dbTools->order_query_api("SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,orders.product_title,orders.product_category,orders.product_subscription_type,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber` 
FROM `orders` 
inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` 
ORDER BY `orders`.`order_id` ASC"
        , $fields
        , "customer",$childFields
        , "reseller",$child2Fields
        , null,null);

$json = json_encode($orders);
echo "{\"orders\" :" ,$json , "}";
    

?>