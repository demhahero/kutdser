<?php

include_once "../mikrotik/dbconfig.php";

$fields = array(
    "modem_id" => "modem_id",
    "mac_address" => "mac_address",
    "type" => "type",
    "serial_number" => "serial_number",
    "is_ours" => "is_ours",
    );
$childFields=array(
    "customer_id" => "customer_id",
    "full_name" => "full_name",
);
$child2Fields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);


$child="customer";
$child2="reseller";

$modems = $dbTools->modems_query_api("SELECT `modems`.`modem_id`,modems.mac_address,modems.type,modems.serial_number,modems.is_ours,customers.customer_id,customers.full_name,resellers.full_name as 'reseller_name',resellers.customer_id as 'reseller_id' 
        FROM modems 
        INNER JOIN customers on modems.customer_id = customers.customer_id
        INNER JOIN `customers` resellers on resellers.`customer_id` = modems.`reseller_id`
        ORDER BY modem_id"
        , $fields
        , $child,$childFields
        , $child2,$child2Fields);

    //convert to json
    //print_r($orders);
$json = json_encode($modems);
echo "{\"modems\" :" ,$json , "}";
