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

    $get_resellers = $dbTools->query("SELECT full_name,customer_id from customers where parent_reseller=" . $reseller_id);

    $customers = array();
    while ($reseller_row = $dbTools->fetch_assoc($get_resellers)) {
        $query="SELECT full_name,customer_id from customers where reseller_id=" . $reseller_row['customer_id'];
        $getCustomers = $dbTools->query($query);

        $start_active_date = null;

        while ($customer_row = $dbTools->fetch_assoc($getCustomers)) {
            $customer = array();
            $customer["customer_id"] = $customer_row["customer_id"];
            $customer["full_name"] = $customer_row["full_name"];
            $customer["reseller_full_name"]=$reseller_row["full_name"];
            $customer["reseller_id"]=$reseller_row["customer_id"];
            //$orders = $dbTools->orders_by_month($customer_row["customer_id"], $year, $month);
            $ordersMonthly=$dbTools->orders_by_month($customer_row["customer_id"], $year, $month);
            $ordersYearly=$dbTools->orders_by_month_yearly($customer_row["customer_id"], $year, $month);

            $orders=array_merge($ordersMonthly,$ordersYearly);
            $customer["orders"] = $orders;


            array_push($customers, $customer);
        }
      }

    $json = json_encode($customers);
    echo "{\"customers\" :", $json, ",\"error\" :null}";
} else {
    echo "{\"customers\" :null,\"error\" :\"invalid params\"}";
}
?>
