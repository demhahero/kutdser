<?php

if (isset($_GET["order_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "../dbconfig.php";

    $order_query = "SELECT `orders`.*,`order_options`.*,`customers`.`full_name`,`orders`.`order_id` as `this_order_id` FROM `orders`
        INNER JOIN `order_options` on `order_options`.`order_id`=`orders`.`order_id`
        INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
        where `orders`.`order_id`=?";

    $stmt1 = $dbTools->getConnection()->prepare($order_query);

    $param_value = $_GET["order_id"];
    $stmt1->bind_param('s', $param_value
    ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if ($result) {

        $start_active_date = "";
        if ($result["product_category"] === "phone") {
            $start_active_date = $result["creation_date"];
        } else if ($result["product_category"] === "internet") {
            if ($result["cable_subscriber"] === "yes") {
                $start_active_date = $result["cancellation_date"];
            } else {
                $start_active_date = $result["installation_date_1"];
            }
        }
        $result["start_active_date"] = $start_active_date;
        $json = json_encode($result);
        echo "{\"order\" :", $json, "}";
    }
} else if (isset($_POST["order_id"])) {

    include_once "../dbconfig.php";

    $succeeded = true;


    $order_id = $_POST["order_id"];
    $product_price = $_POST["product_price"];

    $previous_invoice_query = "SELECT `invoice_id` FROM `invoices` WHERE `invoice_type_id` in (1,2,3) AND `order_id`=? ORDER BY `valid_date_from` DESC LIMIT 1";
    $stmt1 = $dbTools->getConnection()->prepare($previous_invoice_query);
    $stmt1->bind_param('s', $order_id);
    $stmt1->execute();

    if($stmt1->errno != 0)
        $succeeded = FALSE;

    $result1 = $stmt1->get_result();
    $invoice_row = $dbTools->fetch_assoc($result1);
    if ($invoice_row) {
        $query = "UPDATE `invoices` SET `product_price`=? WHERE `invoice_id`=?";
        $stmt1 = $dbTools->getConnection()->prepare($query);
        $stmt1->bind_param('ss', $product_price, $invoice_row["invoice_id"]);
        $stmt1->execute();
        if($stmt1->errno != 0)
            $succeeded = FALSE;
    }



    if ($succeeded) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
