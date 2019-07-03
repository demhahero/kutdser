<?php

//include connection file
include_once "../dbconfig.php";


$sqlTot = "SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,
    orders.product_title,orders.product_category,orders.product_subscription_type,resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber`,
    `order_options`.`cancellation_date`, `order_options`.`installation_date_1`, `order_options`.`product_price`
FROM `orders`
left JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
left JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
left JOIN `invoices` on `invoices`.`order_id`=`orders`.`order_id` and `invoices`.`invoice_type_id` in (1,2,3)
and MONTH(`invoices`.`valid_date_from`)='" . date("m") . "' and YEAR(`invoices`.`valid_date_from`)='" . date("Y") . "'
left JOIN `customers` resellers on resellers.`customer_id` = `customers`.`reseller_id`
where product_title LIKE '%discount%' and `orders`.`order_id` not in (select `order_id` from `requests`
where `requests`.`order_id`=`orders`.`order_id` and `requests`.`action` in ('terminate', 'change_speed')) ";

$sqlRec = $sqlTot;

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data = [];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {


    $start_active_date = "";
    if ($row["product_category"] === "phone") {
        $start_active_date = $row["creation_date"];
    } else if ($row["product_category"] === "internet") {
        if ($row["cable_subscriber"] === "yes") {
            $start_active_date = $row["cancellation_date"];
        } else {
            $start_active_date = $row["installation_date_1"];
        }
    }
    $row["start_active_date"] = $start_active_date;

    $converted = DateTime::createFromFormat("Y-m-d H:i:s", $row["start_active_date"]);
    $start_active_date = clone $converted;
    $converted1Year = $converted->add(new DateInterval("P1Y"));
    $dateNow = new DateTime();
    if ($converted1Year->format("d") != "1")
        $converted1Year->modify('first day of next month');

    $original_price = "538.90$";
    if($row['product_subscription_type'] == "monthly" && $converted1Year< $dateNow){
        $original_price = 44.90;


        $succeeded = true;


        $order_id = $row['order_id'];
        $product_price = $original_price;


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
        $product_title="Internet 30 Mbps";

        $query = "UPDATE `orders` SET `product_title`=? WHERE `order_id`=?";
        $stmt1 = $dbTools->getConnection()->prepare($query);
        $stmt1->bind_param('ss', $product_title, $row['order_id']);
        $stmt1->execute();

        if($stmt1->errno != 0)
            $succeeded = FALSE;
        $query = "UPDATE `order_options` SET `product_price`=? WHERE `order_id`=?";
        $stmt1 = $dbTools->getConnection()->prepare($query);
        $stmt1->bind_param('ss', $original_price, $row['order_id']);
        $stmt1->execute();

        if($stmt1->errno != 0)
            $succeeded = FALSE;


        if ($succeeded) {

            echo "{\"inserted\" :true,\"error\" :\"null\"}";
        } else {
            echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
        }
        echo "true".$row["order_id"]."</br>";
        continue;
    }
    else{
      echo "false".$row["order_id"]."</br>";
      continue;
    }
}

?>
