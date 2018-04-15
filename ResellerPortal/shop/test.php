<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../dbconfig.php';

$order_query = $conn_wordpress->query("select
    p.ID as order_id,
    p.post_date,
    max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
    max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
    max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
    max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
    max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
    max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
    max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
    max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
    max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
    max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
    max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
    max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
    max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
    max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
    max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
    max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
    max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,
    max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
    ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID ) as order_items
from
    wp_posts p 
    join wp_postmeta pm on p.ID = pm.post_id
    join wp_woocommerce_order_items oi on p.ID = oi.order_id
where
    post_type = 'shop_order' and
	p.ID = '1301'

group by
p.ID");
while ($order_row = $order_query->fetch_assoc()) {
    print_r($order_row);

    $wp_woocommerce_order_items_query = $conn_wordpress->query("select * from `wp_woocommerce_order_items` "
            . "where `order_id`='" . $order_row["order_id"] . "'");
    $order_id = $order_row["order_id"];
    $order_date = $order_row["post_date"];
    $full_name = $order_row["_shipping_first_name"] . " " . $order_row["_shipping_last_name"];
    while ($wp_woocommerce_order_items_row = $wp_woocommerce_order_items_query->fetch_assoc()) {

        $wp_woocommerce_order_itemmeta_query = $conn_wordpress->query("select * from `wp_woocommerce_order_itemmeta` "
                . "where `order_item_id`='" . $wp_woocommerce_order_items_row["order_item_id"] . "'");
        while ($wp_woocommerce_order_itemmeta_row = $wp_woocommerce_order_itemmeta_query->fetch_assoc()) {
            if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '_fee_amount') {
                echo $wp_woocommerce_order_items_row["order_item_name"] . " - "
                . $wp_woocommerce_order_itemmeta_row["meta_key"]
                . " : " . $wp_woocommerce_order_itemmeta_row["meta_value"] . "<br/>";
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '_product_id') {
                $product_id = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '_line_subtotal') {
                $product_price = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == 'Are you currently a cable subscriber?') {
                $cable_subscriber = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '1st choice') {
                $installation_date_1 = new DateTime($wp_woocommerce_order_itemmeta_row["meta_value"]);
                $installation_date_1 = $installation_date_1->format("Y-m-d H:i:s");
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '1st choice time') {
                $installation_time_1 = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '2nd choice') {
                $installation_date_2 = new DateTime($wp_woocommerce_order_itemmeta_row["meta_value"]);
                $installation_date_2 = $installation_date_2->format("Y-m-d H:i:s");
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '2nd choice time') {
                $installation_time_2 = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '3rd choice') {
                $installation_date_3 = new DateTime($wp_woocommerce_order_itemmeta_row["meta_value"]);
                $installation_date_3 = $installation_date_3->format("Y-m-d H:i:s");
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == '3rd choice time') {
                $installation_time_3 = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if ($wp_woocommerce_order_itemmeta_row["meta_key"] == 'Select a Plan') {
                $plan = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if (strpos($wp_woocommerce_order_itemmeta_row["meta_key"], "Modem ") !== false) {
                $modem = $wp_woocommerce_order_itemmeta_row["meta_value"];
            } else if (strpos($wp_woocommerce_order_itemmeta_row["meta_key"], "Router ") !== false) {
                $router = $wp_woocommerce_order_itemmeta_row["meta_value"];
            }
        }
    }

    $customer_query = $conn_routers->query("select * from `customers` "
            . "where `full_name` like '" . $full_name . "' and `product_id`='" . $product_id . "' ");

    while ($customer_row = $customer_query->fetch_assoc()) {
        $orders_insert = "insert into `orders` ("
        . "`order_id`,"
        . "`reseller_id`,"
        . "`status`,"
        . "`creation_date`,"
        . "`customer_id`,"
        . " `product_id`"
        . ") VALUES ("
        . "'" . $order_id . "', "
        . "'119', "
        . "'complete', "
        . "'" . $order_date . "', "
        . "'" . $customer_row["customer_id"] . "', "
        . "'" . $product_id . "'"
        . ")";

        echo "<br/>";

        $order_options_insert = "insert into `order_options` ("
        . "`order_id`,"
        . "`plan`,"
        . "`modem`,"
        . "`router`,"
        . "`cable_subscriber`,"
        . "`current_cable_provider`,"
        . "`cancellation_date`,"
        . "`installation_date_1`,"
        . "`installation_time_1`,"
        . "`installation_date_2`,"
        . "`installation_time_2`,"
        . "`installation_date_3`,"
        . "`installation_time_3`,"
        . "`modem_serial_number`,"
        . "`modem_mac_address`,"
        . "`modem_modem_type`,"
        . "`additional_service`,"
        . "`modem_id`,"
        . "`product_price`,"
        . "`additional_service_price`,"
        . "`setup_price`,"
        . "`modem_price`,"
        . "`router_price`,"
        . "`remaining_days_price`,"
        . "`total_price`,"
        . "`gst_tax`,"
        . "`qst_tax`,"
        . "`completion`,"
        . "`adapter_price`,"
        . "`current_phone_number`,"
        . "`phone_province`,"
        . " `adapter`"
        . ") VALUES ("
        . "'" . $order_id . "', "
        . "'" . $plan . "', "
        . "'" . $modem . "', "
        . "'" . $router . "', "
        . "'" . $cable_subscriber . "', "
        . "'" . $current_cable_provider . "', "
        . "'" . $cancellation_date . "', "
        . "'" . $installation_date_1 . "', "
        . "'" . $installation_time_1 . "', "
        . "'" . $installation_date_2 . "', "
        . "'" . $installation_time_2 . "', "
        . "'" . $installation_date_3 . "', "
        . "'" . $installation_time_3 . "', "
        . "'" . $modem_serial_number . "', "
        . "'" . $modem_mac_address . "', "
        . "'" . $modem_modem_type . "', "
        . "'" . $additional_service . "', "
        . "'" . $modem_id . "', "
        . "'" . $product_price . "', "
        . "'" . $additional_serivce_price . "', "
        . "'" . $setup_price . "', "
        . "'" . $modem_price . "', "
        . "'" . $router_price . "', "
        . "'" . $remaining_days_price . "', "
        . "'" . $total_price . "', "
        . "'" . $gst_tax . "', "
        . "'" . $qst_tax . "', "
        . "'" . $customer_row["completion"] . "', "
        . "'" . $adapter_price . "', "
        . "'" . $current_phone_number . "', "
        . "'" . $phone_province . "', "
        . "'" . $adapter . "'"
        . ")";
    }
    echo $orders_insert;
    echo "<br/>";
    $conn_routers->query($orders_insert);
    echo $order_options_insert;
    $conn_routers->query($order_options_insert);
}