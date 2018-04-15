<?php

include_once "../mikrotik/dbconfig.php";
$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
//    "reseller_id" => "reseller_id",
//    "reseller_name" => "reseller_name",
    "modem_mac_address" => "modem_mac_address",
//    "product_id" => "product_id",
//    "title" => "title",
    );
$childFields=array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
);
$child2Fields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);
$child3Fields=array(
    "product_id" => "product_id",
    "title" => "title",
);

$orders = $dbTools->order_query_api("SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name', `products`.`title`,`orders`.`product_id`, `order_options`.`modem_mac_address` FROM `orders` Inner JOIN `products` on `products`.`product_id` = `orders`.`product_id` inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` ORDER BY `orders`.`order_id` ASC"
        , $fields
        , "customer",$childFields
        , "reseller",$child2Fields
        , "product",$child3Fields);

    //convert to json
    //print_r($orders);
$json = json_encode($orders);
echo "{\"orders\" :" ,$json , "}";
    

?>