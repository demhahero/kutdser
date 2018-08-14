<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (
        (isset($_GET['month']) && ctype_digit($_GET['month']) && ((int) $_GET['month'] >= 1 && (int) $_GET['month'] <= 12 )) &&
        (isset($_GET['year']) && ctype_digit($_GET['year'])) &&
        (isset($_GET['reseller_id']) && ctype_digit($_GET['reseller_id']))
) {

    include_once "dbconfig.php";


    $reseller_id = $_GET['reseller_id'];
    $year = $_GET['year'];
    $month = $_GET['month'];

    $getReseller = $dbTools->query("SELECT full_name,customer_id,reseller_commission_percentage from customers where customer_id=" . $reseller_id);

    $reseller = array();
    while ($reseller_row = $dbTools->fetch_assoc($getReseller)) {
      $reseller["customer_id"] = $reseller_row["customer_id"];
      $reseller["full_name"] = $reseller_row["full_name"];
      $reseller["reseller_commission_percentage"] = $reseller_row["reseller_commission_percentage"];

    }

    $getCustomers = $dbTools->query("SELECT full_name,customer_id,reseller_commission_percentage from customers where reseller_id=" . $reseller_id);

    $start_active_date = null;
    $customers = array();
    while ($customer_row = $dbTools->fetch_assoc($getCustomers)) {
        $customer = array();
        $customer["customer_id"] = $customer_row["customer_id"];
        $customer["full_name"] = $customer_row["full_name"];
        $customer["reseller_commission_percentage"] = $customer_row["reseller_commission_percentage"];

        //$orders = $dbTools->orders_by_month($customer_row["customer_id"], $year, $month);
        $ordersMonthly=$dbTools->orders_by_month($customer_row["customer_id"], $year, $month);
        $ordersYearly=$dbTools->orders_by_month_yearly($customer_row["customer_id"], $year, $month);

        $orders=array_merge($ordersMonthly,$ordersYearly);
        $customer["orders"] = $orders;

        array_push($customers, $customer);
    }
    $json_reseller = json_encode($reseller);
    $json = json_encode($customers);
    echo "{\"reseller\" :", $json_reseller, ",\"customers\" :", $json, ",\"error\" :null}";
} else {
    echo "{\"customers\" :null,\"error\" :\"invalid params\"}";
}
?>
