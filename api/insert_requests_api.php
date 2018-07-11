<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//function checkRequest($action_on_date,$order_id,$dbTools){
function checkRequest($order_id, $dbTools) {

    //$getOrder=$dbTools->query("SELECT actual_installation_date from order_options where order_id=".$order_id);
    $getOrder = $dbTools->query("SELECT * from orders inner join order_options on orders.order_id= order_options.order_id where  orders.order_id=" . $order_id);

    $start_active_date = null;
    while ($order_row = $dbTools->fetch_assoc($getOrder)) {
        if ($order_row["product_category"] === "phone") {
            $orderChild["start_active_date"] = $order_row["creation_date"];
        } else if ($order_row["product_category"] === "internet") {
            if ($order_row["cable_subscriber"] === "yes") {
                $orderChild["start_active_date"] = $order_row["cancellation_date"];
            } else {
                $orderChild["start_active_date"] = $order_row["installation_date_1"];
            }
        }
        $start_active_date = $orderChild["start_active_date"];
        break;
    }

    $getrequest = $dbTools->query("SELECT action_on_date,action from requests where order_id=" . $order_id . " and verdict='approve' order by action_on_date desc limit 1");
    while ($request = $dbTools->fetch_assoc($getrequest)) {
        if ($request["action"] === "terminate") {
            echo "{\"canInsert\" :false,\"error\" :\"error: ordere already terminated\"}";
            return false;
        } else {
            $action_on_date = new DateTime($request["action_on_date"]);
            $action_on_datePlus = new DateTime($action_on_date->format('Y') . "-" . $action_on_date->format('m') . "-01 00:00:00");
            $interval = new DateInterval('P1M');
            $action_on_datePlus->add($interval);
            $start_active_date = $action_on_datePlus->format('Y-m-d');
        }
    }
    /*
      $getRequestOnMonth=$dbTools->query("SELECT action_on_date from requests
      where order_id=".$order_id." and year(action_on_date)=".$action_on_date->format('Y')." and month(action_on_date) = ".$action_on_date->format('m')." and verdict='approve'");
      while ($request = $dbTools->fetch_assoc($getRequestOnMonth)) {
      echo "{\"canInsert\" :false,\"error\" :\"error: there is request regestered in this month for this order\"}";
      return false;
      }
     */

    return $start_active_date;
}

if (isset($_GET["order_id"]) && !isset($_GET['do'])) {
    include_once "dbconfig.php";

    //$action_on_dateString=$_GET["action_on_date"];
    //$action_on_date=new DateTime($action_on_dateString);
    $order_id = $_GET["order_id"];

    //if(checkRequest($action_on_date,$order_id,$dbTools))
    $start_active_date = checkRequest($order_id, $dbTools);
    if ($start_active_date)
        echo "{\"start_active_date\" :\"" . $start_active_date . "\",\"error\" :\"null\"}";
}
else if (isset($_GET["order_id"]) && isset($_GET['do']) == "product_list") {
    include_once "dbconfig.php";

    $dbTools->query("SET CHARACTER SET utf8");

    $getOrder = $dbTools->query("SELECT `orders`.`product_title`, `request_id`, `requests`.`product_title` as request_product_title, 
                                `orders`.`product_subscription_type`, `orders`.`product_category`
                                from orders 
                                inner join order_options on orders.order_id= order_options.order_id
                                left join requests on orders.order_id= requests.order_id 
                                where orders.order_id=" . $_GET["order_id"]. " order by `request_id` desc limit 0,1");
    
    $order_row = $dbTools->fetch_assoc($getOrder);

    $current_product_title = ($order_row["request_product_title"] != "")? $order_row["product_title"] : $order_row["request_product_title"];

    $getProducts = $dbTools->query("SELECT * from `products` where `subscription_type`='" . $order_row["product_subscription_type"] . "' "
            . "and `category`='" . $order_row["product_category"] . "' and `status`='active' "
            . "and `title` != '" . $current_product_title . "'");
    $products = array();
    while ($product_row = $dbTools->fetch_assoc($getProducts)) {
        $products[] = $product_row;
    }

    $json = json_encode($products);

    echo "{\"products\" :", $json, ",\"error\" :\"null\"}";
    die();
} else if (isset($_POST["order_id"])) {

    include_once "dbconfig.php";

    $action_on_dateString = $_POST["action_on_date"];
    $action_on_date = new DateTime($action_on_dateString);
    $order_id = $_POST["order_id"];

    //if(!checkRequest($action_on_date,$order_id,$dbTools))
    $start_active_date = checkRequest($order_id, $dbTools);
    if (!$start_active_date)
        exit();


    $PostFields = array(
        "reseller_id" => "",
        "order_id" => "",
        // "creation_date"=>"",
        "action" => "",
        "action_value" => "",
        "admin_id" => "",
        "verdict" => "",
        "verdict_date" => "",
        "action_on_date" => "",
        "modem_mac_address" => "",
        "modem_id" => "",
        "city" => "",
        "address_line_1" => "",
        "address_line_2" => "",
        "postal_code" => "",
        "note" => "",
            //"product_title"=>"",
            //"product_category"=>"",
            //"product_subscription_type"=>"",
    );

    $InsertFieldValues = array(
        "reseller_id" => "",
        "order_id" => "",
        "creation_date" => "",
        "action" => "",
        "action_value" => "",
        "admin_id" => "",
        "verdict" => "",
        "verdict_date" => "",
        "action_on_date" => "",
        "product_price" => "",
        "note" => "",
        "product_title" => "",
        "product_category" => "",
        "product_subscription_type" => "",
        "modem_mac_address" => "",
        "modem_id" => "",
        "city" => "",
        "address_line_1" => "",
        "address_line_2" => "",
        "postal_code" => "",
    );

    if (!isset($_POST["product_id"])) {

        echo "{\"inserted\" :false,\"error\" :\"error: not all values sent in POST\"}";
        exit();
    }
    foreach ($PostFields as $key => $value) {
        if (isset($_POST[$key])) {
            $InsertFieldValues[$key] = $_POST[$key];
        } else if ($key === "verdict" || $key === "verdict_date" || $key === "action_value" || $key === "admin_id" || $key === "note" || $key === "product_title" || $key === "product_category" || $key === "product_price" || $key === "product_subscription_type" || $key === "creation_date" || $key === "modem_id" || $key === "address_line_2" || $key === "modem_mac_address") {
            $InsertFieldValues[$key] = "";
        } else if (isset($_POST["action"]) && $_POST["action"] !== "moving" && ($key === "address_line_1" || $key === "city" || $key === "postal_code")
        ) {
            $InsertFieldValues[$key] = "";
        } else {
            echo "{\"inserted\" :false,\"error\" :\"error: not all values sent in POST\"}";
            exit();
        }
    }


    $action_on_date = new DateTime($InsertFieldValues["action_on_date"]);
    $start_active_date = new DateTime($start_active_date);

    if ($action_on_date < $start_active_date) {
        echo "{\"inserted\" :false,\"error\" :\"error: action_on_date can not be before " . $start_active_date->format('Y-m-d') . "\"}";
        exit();
    }


    $product = $dbTools->query("SELECT * FROM `products` where product_id=" . $_POST["product_id"]);

    while ($product_row = $dbTools->fetch_assoc($product)) {
        $InsertFieldValues["product_title"] = $product_row["title"];
        $InsertFieldValues["product_price"] = $product_row["price"];
        $InsertFieldValues["product_category"] = $product_row["category"];
        $InsertFieldValues["product_subscription_type"] = $product_row["subscription_type"];
    }

    $InsertFieldValues["action_value"] = "0";
    $InsertFieldValues["verdict_date"] = "NULL";
    $InsertFieldValues["admin_id"] = "0";

    $creation_date = new DateTime();
    $InsertFieldValues["action_on_date"] = $action_on_date->format('Y-m-d H:i:s');

    $InsertFieldValues["creation_date"] = $creation_date->format('Y-m-d H:i:s');

    $columns = "";
    $values = "";
    foreach ($InsertFieldValues as $column => $value) {
        $columns .= "`" . $column . "`,";
        if ($value == "NULL")
            $values .= "NULL,";
        else
            $values .= "N'" . $value . "',";
    }
    $query = "INSERT INTO `requests`(" . substr($columns, 0, -1) . ") VALUES (" . substr($values, 0, -1) . ")";

    $requests = $dbTools->query($query);


    $json = json_encode($requests);
    echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
}
