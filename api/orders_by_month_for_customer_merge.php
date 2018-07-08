<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (
        (isset($_GET['month']) && ctype_digit($_GET['month']) && ((int) $_GET['month'] >= 1 && (int) $_GET['month'] <= 12 )) &&
        (isset($_GET['year']) && ctype_digit($_GET['year']))
//	&&
//	(isset($_GET['customer_id']) && ctype_digit($_GET['customer_id']))
) {

    include_once "dbconfig.php";


//$customer_id=$_GET['customer_id'];
    $year = $_GET['year'];
    $month = $_GET['month'];

    $orders = $dbTools->customers_need_merge_monthly($year, $month);
//$ordersMonthly=$dbTools->orders_by_month($customer_id,$year,$month);
//$ordersYearly=$dbTools->orders_by_month_yearly($customer_id,$year,$month);
//$orders=array_merge($ordersMonthly,$ordersYearly);
    $json = json_encode($orders);
    
    if($_GET["do"] == "merge"){
        foreach ($orders as $customer){
            $merchantref = $customer["merchantref"];
            $recurring = (int)$customer['orders'][0]['monthInfo']['recurring_price']
                    +(int)$customer['orders'][1]['monthInfo']['recurring_price'];
            include 'GlobalOnePaymentXMLTools.php';
            $GlobalOnePaymentXMLTools = new GlobalOnePaymentXMLTools();
            echo $GlobalOnePaymentXMLTools->updateSubscription("SS_".$merchantref, "SS_".$merchantref, "CARD_".$merchantref, $recurring);
            die();
        }
    }
    
    echo "{\"customers\" :", $json, ",\"error\" :null}";
} else {
    echo "{\"customers\" :null,\"error\" :\"invalid params\"}";
}


?>
