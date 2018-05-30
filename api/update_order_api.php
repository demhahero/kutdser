<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
 if (isset($_POST["order_id"])) {

    include_once "dbconfig.php";


    $OrderPostOption=$_POST["options"];
    unset($_POST["options"]);
    unset($OrderPostOption["subscriber_other"]);
    unset($OrderPostOption["you_have_phone_number"]);

    $OrderPost=$_POST;
    unset($OrderPost["order_id"]);



    $PostFieldsOrder = array(
      "order_id" => "",
      "status" => "",
      "product" => "",
      "reseller" => "",
      "customer" => "",
      "extra_order_recurring_status" =>"",
      "admin" => "",
      "product_title"=>"",
      "product_category"=>"",
      "product_subscription_type"=>"",
    );
    $PostFieldsOrderOptions = array(
      "plan" =>"",
      "modem" => "",
      "modem_id" => "",
      "modem_serial_number" => "",
      "modem_mac_address" => "",
      "modem_modem_type" => "",
      "router" => "",
      "cable_subscriber" => "",
      "current_cable_provider" => "",
      "cancellation_date" => "",
      "installation_date_1" => "",
      "installation_time_1" => "",
      "installation_date_2" => "",
      "installation_time_2" => "",
      "installation_date_3" => "",
      "installation_time_3" => "",
      "additional_service" => "",
      "product_price" => "",
      "additional_service_price" => "",
      "setup_price" => "",
      "modem_price" => "",
      "router_price" => "",
      "remaining_days_price" => "",
      "total_price" => "",
      "qst_tax" => "",
      "gst_tax" => "",
      "adapter_price" => "",
      "completion" => "",
      "actual_installation_date" => "",
      "actual_installation_time_from" => "",
      "actual_installation_time_to" => "",
      "current_phone_number" => "",
      "phone_province" => "",
      "note" => "",
    );


    if (!isset($_POST["product_id"])) {

        echo "{\"updated\" :false,\"error\" :\"error: not all values sent in POST\"}";
        exit();
    }
    foreach ($OrderPostOption as $key => $value) {
        if ($key === "cancellation_date"
        || $key === "installation_date_1"
        || $key === "installation_date_2"
        || $key === "installation_date_3"
        || $key === "actual_installation_date") {
          $dateValue="NULL";
          if (DateTime::createFromFormat('m/d/Y', $value) !== FALSE) {
            // it's a date
            $dateValueObject=new DateTime($value);
            $dateValue=$dateValueObject->format('Y-m-d H:i:s');
          }
            $PostFieldsOrderOptions[$key] = $dateValue;
        } else {
            $PostFieldsOrderOptions[$key] = $value;
        }
    }


    $product = $dbTools->query("SELECT * FROM `products` where product_id=" . $OrderPost["product_id"]);

    while ($product_row = $dbTools->fetch_assoc($product)) {
        $OrderPost["product_title"] = $product_row["title"];
        $OrderPost["product_category"] = $product_row["category"];
        $OrderPost["product_subscription_type"] = $product_row["subscription_type"];
    }

    $updateOrderQuery = "UPDATE `orders` SET ";
    $values = "";
    foreach ($OrderPost as $column => $value) {
        $updateOrderQuery .= "`" . $column . "`=";
        if($value=="NULL")
          $updateOrderQuery .= "NULL,";
        else
          $updateOrderQuery .= "N'".$dbTools->getConnection()->real_escape_string($value)."',";
    }
    $query = substr($updateOrderQuery, 0, -1)." where `order_id`=".$_POST['order_id'];

    $order = $dbTools->query($query);
    if($order)
    {
      $updateOrderOptionQuery = "UPDATE `order_options` SET ";
      $values = "";
      foreach ($PostFieldsOrderOptions as $column => $value) {
          $updateOrderOptionQuery .= "`" . $column . "`=";
          if($value=="NULL")
            $updateOrderOptionQuery .= "NULL,";
          else
          $updateOrderOptionQuery .= "N'".$dbTools->getConnection()->real_escape_string($value)."',";
      }
      $queryOptions = substr($updateOrderOptionQuery, 0, -1)." where `order_id`=".$_POST['order_id'];

      $orderOptions = $dbTools->query($queryOptions);
      if($orderOptions)
      {
        $json = json_encode($orderOptions);
        echo "{\"updated\" :", $json, ",\"error\" :null}";
      }
      else {
        echo "{\"updated\" :false,\"error\" :\"error in update the db check the manual\"}";
      }
    }



}
