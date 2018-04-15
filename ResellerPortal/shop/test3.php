<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "../dbconfig.php";
$reseller_id = "4";

$customer_query = $conn_routers->query("select * from `customers` where reseller_id='".$reseller_id."'");
$customer_counter = 0;
while ($customer_row = $customer_query->fetch_assoc()) {

    $start_date = new DateTime($customer_row["start_date"]);
    $start_date = $start_date->format("Y-m-d H:i:s");

    $termination_date = new DateTime($customer_row["termination_date"]);
    $termination_date = $termination_date->format("Y-m-d H:i:s");

    try {
        $actual_installation_date = new DateTime($customer_row["actual_installation_date"]);
        $actual_installation_date = $actual_installation_date->format("Y-m-d H:i:s");
    } catch (Exception $e) {
        //echo 'Message: ' . $e->getMessage();
    }


    $product_query = $conn_routers->query("select * from `products` where product_id='" . $customer_row["product_id"] . "' ");
    $product_row = $product_query->fetch_assoc();

    if ($customer_row["join_type"] == "new")
        $installation_date_1 = $start_date;
    else
        $cancellation_date = $start_date;

    $orders_insert = "insert into `orders` ("
            . "`order_id`,"
            . "`reseller_id`,"
            . "`status`,"
            . "`creation_date`,"
            . "`customer_id`,"
            . " `product_id`"
            . ") VALUES ("
            . "'" . $customer_row["order_id"] . "', "
            . "'" . $customer_row["reseller_id"] . "', "
            . "'complete', "
            . "'" . $start_date . "', "
            . "'" . $customer_row["customer_id"] . "', "
            . "'" . $customer_row["product_id"] . "'"
            . ");";

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
            . "`termination_date`,"
            . "`actual_installation_date`,"
            . "`actual_installation_time_from`,"
            . "`actual_installation_time_to`,"
            . "`join_type`,"
            . " `adapter`"
            . ") VALUES ("
            . "'" . $customer_row["order_id"] . "', "
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
            . "'" . $product_row["price"] . "', "
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
            . "'" . $termination_date . "', "
            . "'" . $actual_installation_date . "', "
            . "'" . $customer_row["actual_installation_time_from"] . "', "
            . "'" . $customer_row["actual_installation_time_to"] . "', "
            . "'" . $customer_row["join_type"] . "', "
            . "'" . $adapter . "'"
            . ");";
    
    $merchantref_insert = "insert into `merchantrefs` ("
            . "`merchantref`, "
            . "`customer_id`, "
            . "`order_id`, "
            . "`is_credit`, "
            . "`type`"
            . ") VALUES ("
            . "'old_system_". uniqid()."', "
            . "'" . $customer_row["customer_id"] . "', "
            . "'" . $customer_row["order_id"] . "', "
            . "'yes', "
            . "'internet_order'"
            . ");";
    
    //$merchantref_insert = "delete from `merchantrefs` where `order_id`='" . $customer_row["order_id"] . "';";
    
    $customer_counter++;
    //echo $customer_row["order_id"] ." , ";
    //echo $orders_insert;
    //echo "<br/>";
    echo $merchantref_insert;
    echo "<br/>";
    
    //echo $order_options_insert;
    //echo "<br/>";

    //$conn_routers->query($orders_insert);
    //$conn_routers->query($merchantref_insert);
    //$conn_routers->query($order_options_insert);
}

echo $customer_counter;


