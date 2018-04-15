<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../dbconfig.php';

$orders_query = $conn_routers->query("select * from `orders`");
while ($orders_row = $orders_query->fetch_assoc()) {

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
	p.ID = '".$orders_row["order_id"]."'

group by
p.ID");
    while ($order_row = $order_query->fetch_assoc()) {
        //print_r($order_row);

        $wp_woocommerce_order_items_query = $conn_wordpress->query("select * from `wp_woocommerce_order_items` "
                . "where `order_id`='" . $order_row["order_id"] . "'");
        $order_id = $order_row["order_id"];
        $order_date = $order_row["post_date"];
        $full_name = $order_row["_shipping_first_name"] . " " . $order_row["_shipping_last_name"];
        while ($wp_woocommerce_order_items_row = $wp_woocommerce_order_items_query->fetch_assoc()) {

            $wp_woocommerce_order_itemmeta_query = $conn_wordpress->query("select * from `wp_woocommerce_order_itemmeta` "
                    . "where `order_item_id`='" . $wp_woocommerce_order_items_row["order_item_id"] . "'");
            while ($wp_woocommerce_order_itemmeta_row = $wp_woocommerce_order_itemmeta_query->fetch_assoc()) {
                echo $wp_woocommerce_order_items_row["order_item_name"] . " - "
                . $wp_woocommerce_order_itemmeta_row["meta_key"]
                . " : " . $wp_woocommerce_order_itemmeta_row["meta_value"] . "<br/>";
                
                if($wp_woocommerce_order_items_row["order_item_name"] == "Options Costs" && $wp_woocommerce_order_itemmeta_row["meta_key"] == "_fee_amount" ){
                    //echo "update `orders` set `setup_costs`='".$wp_woocommerce_order_itemmeta_row["meta_value"]."' "
                            //. "where `order_id`='".$orders_row["order_id"]."'";
                    //$conn_routers->query("update `orders` set `setup_costs`='".$wp_woocommerce_order_itemmeta_row["meta_value"]."' "
                          //  . "where `order_id`='".$orders_row["order_id"]."'");
                    
                }
                
            }
        }
    }
    
    $merchantref = uniqid();
                    $result_merchantrefs = $conn_routers->query("insert into `merchantrefs` ("
                    . "`merchantref`, "
                    . "`customer_id`, "
                    . "`order_id`, "
                    . "`is_credit`, "
                    . "`type`"
                    . ") VALUES ("
                    . "'" . $merchantref . "', "
                    . "'" . $orders_row["customer_id"] . "', "
                    . "'" . $orders_row["order_id"] . "', "
                    . "'yes', "
                    . "'internet_order'"
                    . ")");
}