<?php

include_once "../dbconfig.php";

$order_id = intval($_GET['order_id']);

$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
    "product_title" => "product_title",
    "product_category" => "product_category",
    "modem_mac_address" => "modem_mac_address",
    "product_subscription_type" => "product_subscription_type",
    "cable_subscriber" => "cable_subscriber",
    "completion" => "completion",
    "plan" => "plan",
    "installation_date_1" => "installation_date_1",
    "installation_date_2" => "installation_date_2",
    "installation_date_3" => "installation_date_3",
    "installation_time_1" => "installation_time_1",
    "installation_time_2" => "installation_time_2",
    "installation_time_3" => "installation_time_3",
    "cancellation_date" => "cancellation_date",
    "modem" => "modem",
    "router" => "router",
    "current_cable_provider" => "current_cable_provider",
    "actual_installation_time_from" => "actual_installation_time_from",
    "actual_installation_time_to" => "actual_installation_time_to",
    "actual_installation_date" => "actual_installation_date",
    "current_phone_number" => "current_phone_number",
    "adapter" => "adapter",
    "additional_service" => "additional_service",
    "displayed_order_id" => "order_id"
);
$childFields = array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
    "address" => "address",
    "phone" => "phone",
    "note" => "note",
    "city" => "city",
    "address_line_1" => "address_line_1",
    "address_line_2" => "address_line_2",
    "postal_code" => "postal_code",
    "email" => "email"
);
$child2Fields = array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);
$child3Fields = array(
    "merchantref" => "merchantref",
    "is_credit" => "is_credit",
);

$orders = $dbTools->order_query_api("SELECT `merchantrefs`.`merchantref`, `merchantrefs`.`order_id`,
    `merchantrefs`.`is_credit`, `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,
    `orders`.customer_id,orders.product_title,orders.product_category,orders.product_subscription_type,
    resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`,
    `order_options`.`cable_subscriber`,`customers`.`address`,`customers`.`phone`,`customers`.`note` ,`customers`.`email`,
    `order_options`.`completion`,`customers`.`city`,`customers`.`address_line_1`,`customers`.`address_line_2`,
    `customers`.`postal_code`, `order_options`.`installation_date_1`, `order_options`.`installation_date_2`,
    `order_options`.`installation_time_1`,`order_options`.`installation_time_2`,`order_options`.`installation_time_3`,
    `order_options`.`installation_date_3`, `order_options`.`cancellation_date`, `order_options`.`plan`,
    `order_options`.`modem`, `order_options`.`router`, `order_options`.`current_cable_provider`,
    `order_options`.`actual_installation_time_from`, `order_options`.`actual_installation_time_to`,
    `order_options`.`actual_installation_date`, `order_options`.`current_phone_number`, `order_options`.`adapter`
    , `order_options`.`additional_service`
FROM `orders`
left JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
left JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
left JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
left JOIN `merchantrefs` on `merchantrefs`.`customer_id` = `orders`.`customer_id` and type!='payment'
where `orders`.`order_id`='" . $order_id . "'"
        , $fields
        , "customer", $childFields
        , "reseller", $child2Fields
        , 'merchantref', $child3Fields);

$json = json_encode($orders);
echo $json;
?>
