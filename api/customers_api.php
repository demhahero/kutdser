<?php

include_once "../mikrotik/dbconfig.php";
$fields = array(
    "customer_id" => "customer_id",
    "phone" => "phone",
    "address" => "address",
    "email" => "email",
    "full_name" => "full_name",
    );
$childFields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);
$child2Fields=array(
    "order_id" => "order_id",
);

$customers = $dbTools->customer_query_api("SELECT `customers`.`customer_id`,`customers`.`phone`,customers.address,customers.email,customers.full_name,orders.order_id,resellers.full_name as 'reseller_name',customers.reseller_id 
FROM customers INNER JOIN `customers` resellers on resellers.`customer_id` = customers.`reseller_id`
LEFT JOIN orders on orders.customer_id=customers.customer_id"
        , $fields
        , "reseller",$childFields
        , "orders",$child2Fields);


$json = json_encode($customers);
echo "{\"customers\" :" ,$json , "}";
    

?>