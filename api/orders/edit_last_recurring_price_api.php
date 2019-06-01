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
    
    $product_price = $_POST["product_price"];
    
    
    $invoice_query = "SELECT * FROM `invoices` "
            . "where order_id= ? and YEAR(valid_date_from) = '2019' and MONTH(valid_date_from) = '5'";

    $stmt1 = $dbTools->getConnection()->prepare($invoice_query);

    $param_value = $_POST["order_id"];
    $stmt1->bind_param('s', $param_value
    ); // 's' specifies the variable type => 'string'


    $stmt1->execute();
    
    if($stmt1->errno != 0)
        $succeeded = FALSE;
    
    $result1 = $stmt1->get_result();
    $invoice_row = $dbTools->fetch_assoc($result1);
    if ($invoice_row) {    
        $invoice_row["invoice_id"];
        
        
        $invoice_items_query = "SELECT * FROM `invoice_items` "
            . "where `invoice_id`= ?  order by `invoice_item_id` asc";

        $stmt1 = $dbTools->getConnection()->prepare($invoice_items_query);

        $param_value = $invoice_row["invoice_id"];
        $stmt1->bind_param('s', $param_value
        ); // 's' specifies the variable type => 'string'


        $stmt1->execute();
        
        $result1 = $stmt1->get_result();
        $total_price = 0;
        
        
        if($stmt1->errno != 0)
            $succeeded = FALSE;
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
