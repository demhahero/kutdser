<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (isset($_GET["customer_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "dbconfig.php";

    //$action_on_dateString=$_GET["action_on_date"];
    //$action_on_date=new DateTime($action_on_dateString);
    $customer_id = $_GET["customer_id"];


    $fields = array(
        "customer_log_id" => "customer_log_id",
        "log_date" => "log_date",
        "type" => "type",
        "note" => "note",
        "completion" => "completion",
    );
    $childFields = array(
        "customer_id" => "customer_id",
        "full_name" => "full_name",
    );
    $child2Fields = array(
        "admin_id" => "admin_id",
        "username" => "username",
    );
    $child2 = "admin";


    $child = "customer";

    $query = "SELECT
     `customer_log_id`, customer_log.`customer_id`, `customer_log`.`admin_id`, `admins`.`username`, `admins`.`admin_id`, `log_date`, customer_log.`type`, customer_log.`note`, customer_log.`completion`
     ,customers.`full_name`
     FROM `customer_log`
     INNER JOIN customers on customer_log.customer_id=customers.customer_id
     left JOIN admins on customer_log.admin_id = admins.admin_id
      WHERE customer_log.customer_id=" . $customer_id . " ORDER BY log_date DESC";
    //echo $query;
    //exit();
    $customer_logs = $dbTools->customer_log_query_api($query
            , $fields
            , $child, $childFields
            , $child2, $child2Fields);

    //convert to json
    //print_r($orders);
    $json = json_encode($customer_logs);
    echo "{\"customer_logs\" :", $json, "}";
} else if (isset($_POST["customer_id"])) {

    include_once "dbconfig.php";



    $PostFields = array(
        "customer_id" => "",
        "log_date" => "",
        "type" => "",
        "note" => "",
        "completion" => "",
        "admin_id" => "",
    );

    $InsertFieldValues = array(
        "customer_id" => "",
        "log_date" => "",
        "type" => "",
        "note" => "",
        "completion" => "",
        "admin_id" => "",
    );

    foreach ($PostFields as $key => $value) {
        if (isset($_POST[$key])) {
            $InsertFieldValues[$key] = $_POST[$key];
        } else if ($key === "note" //|| $key === "verdict_date" || $key === "action_value" || $key === "admin_id" || $key === "note" || $key === "product_title" || $key === "product_category" || $key === "product_price" || $key === "product_subscription_type" || $key === "creation_date"
        ) {
            $InsertFieldValues[$key] = "";
        } else {
            echo "{\"inserted\" :false,\"error\" :\"error: not all values sent in POST\"}";
            exit();
        }
    }

    $columns = "";
    $values = "";
    foreach ($InsertFieldValues as $column => $value) {
        $columns .= "`" . $column . "`,";
        if ($value == "NULL")
            $values .= "NULL,";
        else
            $values .= "N'" . $value . "',";
    }
    $query = "INSERT INTO `customer_log`(" . substr($columns, 0, -1) . ") VALUES (" . substr($values, 0, -1) . ")";

    $customer_log = $dbTools->query($query);

    if ($customer_log) {
        $json = json_encode($customer_log);
        echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
