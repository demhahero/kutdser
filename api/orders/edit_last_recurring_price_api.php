<?php

if (isset($_GET["order_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "../dbconfig.php";
    
    $order_query = "SELECT `orders`.*,`order_options`.*,`customers`.`full_name`,`orders`.`order_id` as `this_order_id`,
        `invoices`.`product_price` as `last_invoice_product_price` FROM `orders`
        INNER JOIN `order_options` on `order_options`.`order_id`=`orders`.`order_id`
        INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
        LEFT JOIN `invoices` on `invoices`.`order_id`=`orders`.`order_id` and `invoices`.`invoice_type_id` in (1,2,3) 
        and MONTH(`invoices`.`valid_date_from`)='".date("m")."' and YEAR(`invoices`.`valid_date_from`)='".date("Y")."'  
        LEFT JOIN `requests` on `requests`.`order_id`=`orders`.`order_id` 
        and `requests`.`action` in ('terminate', 'change_speed')
        where `orders`.`order_id`=? order by `invoices`.valid_date_from desc";

    $stmt1 = $dbTools->getConnection()->prepare($order_query);

    $param_value = $_GET["order_id"];
    $stmt1->bind_param('s', $param_value
    ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $order_row = $dbTools->fetch_assoc($result1);
    if ($order_row) {

        $start_active_date = "";
        if ($order_row["product_category"] === "phone") {
            $start_active_date = $order_row["creation_date"];
        } else if ($order_row["product_category"] === "internet") {
            if ($order_row["cable_subscriber"] === "yes") {
                $start_active_date = $order_row["cancellation_date"];
            } else {
                $start_active_date = $order_row["installation_date_1"];
            }
        }
        $order_row["start_active_date"] = $start_active_date;
        
        $converted = DateTime::createFromFormat("Y-m-d H:i:s", $order_row["start_active_date"]);
        $converted1Year = $converted->add(new DateInterval("P1Y"));
        
        if($converted1Year->format("d") != "1")
            $converted1Year->modify('first day of next month');
        
        $order_row["offer_end"] = $converted1Year->format("Y-m-d H:i:s");
        $json = json_encode($order_row);
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
    
        
        $invoice_items_query = "SELECT * FROM `invoice_items` "
            . "where `invoice_id`= ?  order by `invoice_item_id` asc";
        $stmt1 = $dbTools->getConnection()->prepare($invoice_items_query);
        $param_value = $invoice_row["invoice_id"];
        $stmt1->bind_param('s', $param_value
        ); // 's' specifies the variable type => 'string
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        
        if($stmt1->errno != 0)
                    $succeeded = FALSE;
        
        $total_price = 0;    

        while($invoice_items_row = $dbTools->fetch_assoc($result1)){
            if(substr($invoice_items_row["item_name"], 0, 7)=="product"){
                $total_price+= $product_price;
                $query = "UPDATE `invoice_items` SET `item_price`=?,"
                . "`item_duration_price`=? WHERE `invoice_item_id`=?";
                $stmt1 = $dbTools->getConnection()->prepare($query);
                $stmt1->bind_param('sss', $product_price, $product_price, $invoice_items_row['invoice_item_id']);
                $stmt1->execute();
                
                if($stmt1->errno != 0)
                    $succeeded = FALSE;
            } else if($invoice_items_row["item_name"] == "Router price"
                    || $invoice_items_row["item_name"] == "Additional service price"
                    || $invoice_items_row["item_name"] == "Static IP price"){
                $total_price+= $invoice_items_row["item_price"];
            } else if($invoice_items_row["item_name"] == "QST Tax"){
                $qst = $total_price * 0.09975;
                $query = "UPDATE `invoice_items` SET `item_price`=?,"
                . "`item_duration_price`=? WHERE `invoice_item_id`=?";
                $stmt1 = $dbTools->getConnection()->prepare($query);
                $stmt1->bind_param('sss', $qst, $qst, $invoice_items_row['invoice_item_id']);
                $stmt1->execute();
                
                if($stmt1->errno != 0)
                    $succeeded = FALSE;
            } else if($invoice_items_row["item_name"] == "GST Tax"){
                $gst = $total_price * 0.05;
                $query = "UPDATE `invoice_items` SET `item_price`=?,"
                . "`item_duration_price`=? WHERE `invoice_item_id`=?";
                $stmt1 = $dbTools->getConnection()->prepare($query);
                $stmt1->bind_param('sss', $gst, $gst, $invoice_items_row['invoice_item_id']);
                $stmt1->execute();
                
                if($stmt1->errno != 0)
                    $succeeded = FALSE;
            }
        }
    }

    
    if ($succeeded) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
