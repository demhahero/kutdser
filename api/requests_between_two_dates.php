<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "dbconfig.php";
$fields = array(
    "request_id" => "request_id",
    "action" => "action",
    "action_value" => "action_value",
    "action_on_date" => "action_on_date",
    "creation_date" => "creation_date",
    "product_price" => "product_price",
    "product_title" => "product_title",
    "note" => "note",
    "verdict" => "verdict",
    "modem_id" => "modem_id",
);
$childFields = array(
    "admin_id" => "admin_id",
    "username" => "username",
);
$child2Fields = array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);
$child3Fields = array(
    "order_id" => "order_id",
);

$child4Fields = array(
    "customer_id" => "customer_id",
    "full_name" => "full_name",
);

$child = "admin";
$child2 = "reseller";
$child3 = "order";
$child4 = "customer";

if($_GET['type']=='terminate')
    $requests = $dbTools->request_query_api("SELECT requests.request_id,requests.product_price,requests.product_title,requests.verdict,requests.note,orders.order_id,customers.customer_id,customers.full_name,requests.action,requests.action_value,requests.action_on_date,requests.creation_date,requests.modem_id,resellers.full_name as 'reseller_name',resellers.customer_id as 'reseller_id',admins.admin_id,admins.username FROM requests
    left JOIN `customers` resellers on resellers.`customer_id` = requests.reseller_id
    left JOIN orders on orders.order_id = requests.order_id
    left JOIN customers on customers.customer_id=orders.customer_id
    left JOIN admins on requests.admin_id = admins.admin_id
    where `requests`.action_on_date >= '".$_GET['date1']."' and `requests`.action_on_date <= '".$_GET['date2']."' and requests.action='terminate'
    ORDER BY requests.request_id"
            , $fields
            , $child, $childFields
            , $child2, $child2Fields
            , $child3, $child3Fields
            , $child4, $child4Fields);
else{
    $requests = $dbTools->request_query_api("SELECT requests.request_id,requests.product_price,requests.product_title,requests.verdict,requests.note,orders.order_id,customers.customer_id,customers.full_name,requests.action,requests.action_value,requests.action_on_date,requests.creation_date,requests.modem_id,resellers.full_name as 'reseller_name',resellers.customer_id as 'reseller_id',admins.admin_id,admins.username FROM requests
    left JOIN `customers` resellers on resellers.`customer_id` = requests.reseller_id
    left JOIN orders on orders.order_id = requests.order_id
    left JOIN customers on customers.customer_id=orders.customer_id
    left JOIN admins on requests.admin_id = admins.admin_id
    where `requests`.action_on_date >= '".$_GET['date1']."' and `requests`.action_on_date <= '".$_GET['date2']."' and requests.action != 'terminate'  and requests.action != 'customer_information_modification'
    ORDER BY requests.request_id"
            , $fields
            , $child, $childFields
            , $child2, $child2Fields
            , $child3, $child3Fields
            , $child4, $child4Fields);    
}

$json = json_encode($requests);
echo "{\"requests\" :", $json, "}";
