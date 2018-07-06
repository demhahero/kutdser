<?php

if (isset($_GET["customer_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "dbconfig.php";
    
    $customer_query = $dbTools->query("SELECT *
     FROM `customers`
      WHERE `customer_id`='" . $_GET["customer_id"] . "'");
    
    $customer = $dbTools->fetch_assoc($customer_query);


    $json = json_encode($customer);
    echo "{\"customer\" :", $json, "}";
} else if (isset($_POST["customer_id"])) {

    include_once "dbconfig.php";

    $query = "update `customers` set `address_line_1`='".$_POST["address_line_1"]."',"
            . "`address_line_2`='".$_POST["address_line_2"]."',"
            . "`postal_code`='".$_POST["postal_code"]."',"
            . "`city`='".$_POST["city"]."' where `customer_id`='".$_POST["customer_id"]."'";

    $customer_log = $dbTools->query($query);

    if ($customer_log) {
        $json = json_encode($customer_log);
        echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
