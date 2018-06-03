<?php

include_once "dbconfig.php";
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

$child3Fields=array(
    "modem_id" => "modem_id",
    "mac_address" => "mac_address",
);

$child4Fields = array(
    "merchantref" => "merchantref",
    "is_credit" => "is_credit",
);

$customers = $dbTools->tik_monitoring_query_api("SELECT `merchantrefs`.`merchantref`, `merchantrefs`.`order_id`, 
    `merchantrefs`.`is_credit`, `customers`.`customer_id` , `customers`.`phone` , customers.address, 
    customers.email, customers.full_name, orders.order_id, resellers.full_name AS 'reseller_name', 
    customers.reseller_id, `modems`.`mac_address`, `modems`.`modem_id`
FROM customers
INNER JOIN `customers` resellers ON resellers.`customer_id` = customers.`reseller_id`
LEFT JOIN orders ON orders.customer_id = customers.customer_id
LEFT JOIN modems ON orders.customer_id = modems.customer_id
INNER JOIN `merchantrefs` on `merchantrefs`.`customer_id` = `customers`.`customer_id` and `merchantrefs`.`type`!='payment' "
        , $fields
        , "reseller",$childFields
        , "orders",$child2Fields
        , "modem",$child3Fields
        , "merchantref", $child4Fields);


$json = json_encode($customers);
echo "{\"customers\" :" ,$json , "}";
    

?>