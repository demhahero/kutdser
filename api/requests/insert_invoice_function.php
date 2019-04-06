<?php

function changeSpeedMonthly($dbTools, $postData) {
    $next_days = 0;
    $new_product_price = 0;

    $customer_id = $postData["customer_id"];
    $order_id = $postData["order_id"];
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $postData["action_on_date"] = $request["action_on_date"];
    $previous_invoice_query = "SELECT *,DATEDIFF(`valid_date_to`,`valid_date_from`) AS `duration` FROM `invoices` WHERE `invoice_type_id` in (1,2,3) AND `customer_id`=? ORDER BY `valid_date_from` DESC LIMIT 1";
    $stmt1 = $dbTools->getConnection()->prepare($previous_invoice_query);
    $stmt1->bind_param('s', $customer_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    //there is must be at least one invoice as the initial one is new order must be found before make request
    $previous_invoice = $dbTools->fetch_assoc($result1);

    $valid_date_from = new DateTime($previous_invoice["valid_date_from"]);
    $valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    $previous_durationDays = (int) $previous_invoice["duration"];

    $previous_product_query = "SELECT * FROM `invoice_items` WHERE `invoice_id`=? AND (`item_name` LIKE '%Product%')  ORDER BY `invoice_item_id` ASC LIMIT 1";
    $stmt2 = $dbTools->getConnection()->prepare($previous_product_query);
    $stmt2->bind_param('s', $previous_invoice["invoice_id"]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $previous_product = $dbTools->fetch_assoc($result2);
    $previous_product_price = (double) $previous_product["item_duration_price"];
    $new_valid_date_from = new DateTime($postData["action_on_date"]);
    // if price divided between two months (remaining days from previous month)
    if ($new_valid_date_from->format('Y-m') == $valid_date_from->format("Y-m") && $valid_date_from->format("Y-m") != $valid_date_to->format("Y-m")) {
        $new_valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
        // if new invoice during first month then refund the rest of the first and second month

        $service_used_days = (int) $new_valid_date_from->format("d") - (int) $valid_date_from->format("d");
        $service_used_days_price = ((double) $previous_invoice["product_price"] / (int) $valid_date_from->format("t")) * $service_used_days;
        $refund_price = $previous_product_price - $service_used_days_price;
        /// now calculate price for new period with new product for the rest of the first and second month
        $new_service_used_days_first_month = (int) $new_valid_date_from->format("t") - (int) $new_valid_date_from->format("d") + 1;
        $next_days = $new_service_used_days_first_month + (int) $new_valid_date_to->format("t");
        $new_service_used_days_first_month_price = ((double) $postData["product_price"] / (int) $new_valid_date_from->format("t")) * $new_service_used_days_first_month;
        $new_product_price = $new_service_used_days_first_month_price + (double) $postData["product_price"];
    } else {//if($new_valid_date_from->format('Y-m')==$valid_date_to->format("Y-m") && $valid_date_from->format("Y-m") != $valid_date_to->format("Y-m"))
        // if new invoice during second month then refund the rest of the second month
        // or // if there is no  (remaining days) from previous month
        // set new invoice valid date to is to the end of the action date month
        $new_valid_date_to = new DateTime($new_valid_date_from->format('Y-m-t'));
        $service_not_used_days = (int) $new_valid_date_to->format("d") - (int) $new_valid_date_from->format("d") + 1;

        $service_not_used_days_price = ((double) $previous_invoice["product_price"] / (int) $new_valid_date_to->format("t")) * $service_not_used_days;
        $refund_price = $service_not_used_days_price;
        /// now calculate price for new period with new product for the rest of the second month
        $next_days = $service_not_used_days;
        $new_service_used_days_second_month_price = ((double) $postData["product_price"] / (int) $new_valid_date_from->format("t")) * $service_not_used_days;
        $new_product_price = $new_service_used_days_second_month_price;
    }
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'0',?,?,?)";
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_from->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $previous_invoice["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $refund_price;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`,`item_price`,`item_duration_price`,`item_type`)
    VALUES (?,?,?,?,'duration'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Refund for the next " . $next_days . " day(s)";
    $param3 = $previous_invoice["product_price"];
    $param4 = $refund_price;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('ssssssssssss', $param1, $param2, $param3, $param4, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
    /////////// end add refund invoice
    ////////// add new change speed invoice
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'3',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_to->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $new_product_price + (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;


    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
    VALUES (?,?,?,?,'duration'),(?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Product " . $postData["product_title"] . " for the next " . $next_days . " day(s)";
    $param3 = $postData["product_price"];
    $param4 = $new_product_price;
    $param5 = "Change Speed fees";
    $param6 = $fees_charged;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssssssss', $param1, $param2, $param3, $param4, $param1, $param5, $param6, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
}

function terminateMonthly($dbTools, $postData) {
    $next_days = 0;
    $new_product_price = 0;

    $customer_id = $postData["customer_id"];
    $order_id = $postData["order_id"];
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $postData["action_on_date"] = $request["action_on_date"];
    $previous_invoice_query = "SELECT *,DATEDIFF(`valid_date_to`,`valid_date_from`) AS `duration` FROM `invoices` WHERE `invoice_type_id` in (1,2,3) AND `customer_id`=? ORDER BY `valid_date_from` DESC LIMIT 1";
    $stmt1 = $dbTools->getConnection()->prepare($previous_invoice_query);
    $stmt1->bind_param('s', $customer_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    //there is must be at least one invoice as the initial one is new order must be found before make request
    $previous_invoice = $dbTools->fetch_assoc($result1);

    $valid_date_from = new DateTime($previous_invoice["valid_date_from"]);
    $valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    $previous_durationDays = (int) $previous_invoice["duration"];

    $previous_product_query = "SELECT * FROM `invoice_items` WHERE `invoice_id`=? AND (`item_name` LIKE '%Product%')  ORDER BY `invoice_item_id` ASC LIMIT 1";
    $stmt2 = $dbTools->getConnection()->prepare($previous_product_query);
    $stmt2->bind_param('s', $previous_invoice["invoice_id"]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $previous_product = $dbTools->fetch_assoc($result2);
    $previous_product_price = (double) $previous_product["item_duration_price"];
    $new_valid_date_from = new DateTime($postData["action_on_date"]);
    // if price divided between two months (remaining days from previous month)
    if ($new_valid_date_from->format('Y-m') == $valid_date_from->format("Y-m") && $valid_date_from->format("Y-m") != $valid_date_to->format("Y-m")) {
        $new_valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
        // if new invoice during first month then refund the rest of the first and second month
        $service_used_days = (int) $new_valid_date_from->format("d") - (int) $valid_date_from->format("d");
        $service_used_days_price = ((double) $previous_invoice["product_price"] / (int) $valid_date_from->format("t")) * $service_used_days;
        $refund_price = $previous_product_price - $service_used_days_price;
        /// now calculate price for new period with new product for the rest of the first and second month
        $new_service_used_days_first_month = (int) $new_valid_date_from->format("t") - (int) $new_valid_date_from->format("d") + 1;
        $next_days = $new_service_used_days_first_month + (int) $new_valid_date_to->format("t");
        $new_service_used_days_first_month_price = ((double) $postData["product_price"] / (int) $new_valid_date_from->format("t")) * $new_service_used_days_first_month;
        $new_product_price = $new_service_used_days_first_month_price + (double) $postData["product_price"];
    } else {//if($new_valid_date_from->format('Y-m')==$valid_date_to->format("Y-m") && $valid_date_from->format("Y-m") != $valid_date_to->format("Y-m"))
        // if new invoice during second month then refund the rest of the second month
        // or // if there is no  (remaining days) from previous month
        // set new invoice valid date to is to the end of the action date month
        $new_valid_date_to = new DateTime($new_valid_date_from->format('Y-m-t'));
        $service_not_used_days = (int) $new_valid_date_to->format("d") - (int) $new_valid_date_from->format("d") + 1;

        $service_not_used_days_price = ((double) $previous_invoice["product_price"] / (int) $new_valid_date_to->format("t")) * $service_not_used_days;
        $refund_price = $service_not_used_days_price;
        /// now calculate price for new period with new product for the rest of the second month
        $next_days = $service_not_used_days;
        $new_service_used_days_second_month_price = ((double) $postData["product_price"] / (int) $new_valid_date_from->format("t")) * $service_not_used_days;
        $new_product_price = $new_service_used_days_second_month_price;
    }
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'0',?,?,?)";
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_from->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $previous_invoice["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $refund_price;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`,`item_price`,`item_duration_price`,`item_type`)
      VALUES (?,?,?,?,'duration'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Refund for the next " . $next_days . " day(s)";
    $param3 = $previous_invoice["product_price"];
    $param4 = $refund_price;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('ssssssssssss', $param1, $param2, $param3, $param4, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
    /////////// end add refund invoice
    ////////// add new change speed invoice
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'6',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_to->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;

    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
    VALUES (?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Terminate fees";
    $param3 = $fees_charged;
    $param4 = "QST Tax";
    $param5 = $qst_tax;
    $param6 = "GST Tax";
    $param7 = $gst_tax;


    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssss', $param1, $param2, $param3, $param1, $param4, $param5, $param5, $param1, $param6, $param7, $param7);
    $stmt4->execute();
}

function swapModem($dbTools, $postData) {
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $order_id = $postData["order_id"];
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'5',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $postData["customer_id"];
    $param2 = $request["action_on_date"];
    $param3 = $request["action_on_date"];
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
  VALUES (?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Swap modem fees";
    $param3 = $fees_charged;
    $param4 = "QST Tax";
    $param5 = $qst_tax;
    $param6 = "GST Tax";
    $param7 = $gst_tax;
    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssss', $param1, $param2, $param3, $param1, $param4, $param5, $param5, $param1, $param6, $param7, $param7);
    $stmt4->execute();
}

function moving($dbTools, $postData) {
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $order_id = $postData["order_id"];
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'4',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $postData["customer_id"];
    $param2 = $request["action_on_date"];
    $param3 = $request["action_on_date"];
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
  VALUES (?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Moving fees";
    $param3 = $fees_charged;
    $param4 = "QST Tax";
    $param5 = $qst_tax;
    $param6 = "GST Tax";
    $param7 = $gst_tax;
    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssss', $param1, $param2, $param3, $param1, $param4, $param5, $param5, $param1, $param6, $param7, $param7);
    $stmt4->execute();
}

function recurring($dbTools, $postData, $start, $end) {

    /// check if recuring already exist
    $suppose_start_recurring_date = new DateTime($end);
    $suppose_start_recurring_date->add(new DateInterval('P1D'));
    $sql = "SELECT `invoice_type_id` FROM `invoices`
          WHERE  date(`valid_date_from`)=?
          AND `invoices`.`invoice_type_id` = 2
          AND `invoices`.`customer_id`=?
          AND `invoices`.`order_id`=?
          ORDER BY `invoice_id` DESC
          LIMIT 1";

    $stmt_product = $dbTools->getConnection()->prepare($sql);
    $param1 = $suppose_start_recurring_date->format("Y-m-d");
    $param2 = $postData["customer_id"];
    $param3 = $postData["order_id"];
    $stmt_product->bind_param('sss', $param1, $param2, $param3);
    $stmt_product->execute();

    $result_product = $stmt_product->get_result();
    $hasValue = FALSE;
    while ($product = $dbTools->fetch_assoc($result_product)) {
        //already has recuring in this month
        return FALSE; //FALSE;
    }
    /// check if during the first period after start active date
    $suppose_start_recurring_date = new DateTime($end);
    $suppose_start_recurring_date->add(new DateInterval('P1D'));
    $sql_ = "SELECT `invoice_type_id`,`valid_date_from`,`valid_date_to` FROM `invoices`
          WHERE `invoices`.`customer_id`=?
          AND `invoices`.`order_id`=?
          AND `invoices`.`invoice_type_id`=1";

    $stmt_ = $dbTools->getConnection()->prepare($sql_);
    $param1 = $postData["customer_id"];
    $param2 = $postData["order_id"];
    $stmt_->bind_param('ss', $param1, $param2);
    $stmt_->execute();
    $result_ = $stmt_->get_result();
    while ($invoice_ = $dbTools->fetch_assoc($result_)) {
        $new_order_valid_from=new DateTime($invoice_["valid_date_from"]);
        $new_order_valid_to=new DateTime($invoice_["valid_date_to"]);
        $end_recurring_date_=new DateTime($end);
        if($new_order_valid_to->format("Y-m-d")>$end_recurring_date_->format("Y-m-d"))
        {
          // don't add recurring as this means new order paid for remaining_days plus one month
          // and we want to let him pay again as recuring for the month
          return FALSE;
        }
        else if($new_order_valid_to->format("Y-m-d") ==$end_recurring_date_->format("Y-m-d"))
        {
          $start=$new_order_valid_from->format("Y-m-d");
        }



    }
    /// get last product price
    $sql = "SELECT `invoice_type_id`,`product_price` FROM `invoices`
          WHERE  (`valid_date_from`>=? AND `valid_date_from`<=?)
          AND `invoices`.`invoice_type_id` IN (1,2,3,6)
          AND `invoices`.`customer_id`=?
          AND `invoices`.`order_id`=?
          ORDER BY `invoice_id` DESC
          LIMIT 1";

    $stmt_product = $dbTools->getConnection()->prepare($sql);
    $param1 = $start;
    $param2 = $end;
    $param3 = $postData["customer_id"];
    $param4 = $postData["order_id"];
    $stmt_product->bind_param('ssss', $param1, $param2, $param3, $param4);
    $stmt_product->execute();

    $result_product = $stmt_product->get_result();
    $hasValue = FALSE;
    $product_price = -1;
    while ($product = $dbTools->fetch_assoc($result_product)) {
        $hasValue = TRUE;
        if ($product["invoice_type_id"] == 6) {//then this is terminated
            return "terminate"; //FALSE;
        }

        $product_price = $product["product_price"];
    }


    if ($product_price < 0) {

        return FALSE; // already terminated because doesn't have any invoice int this month
    }
    // get product title
    $sql = "(SELECT `requests`.`product_title`,`requests`.`action_on_date` AS `date_active` FROM `requests` INNER JOIN `orders` ON `orders`.`order_id`=`requests`.`order_id` WHERE `customer_id`=? AND `requests`.`verdict`='approve' AND `requests`.`action`='change_speed' AND `action_on_date`<?)
  UNION
  (SELECT `product_title`,`creation_date` AS `date_active` FROM `orders` WHERE `customer_id`=? )
  ORDER BY `date_active` DESC LIMIT 1";
    $stmt_product_title = $dbTools->getConnection()->prepare($sql);
    $param1 = $postData["customer_id"];
    $param2 = $end;
    $stmt_product_title->bind_param('sss', $param1, $param2, $param1);
    $stmt_product_title->execute();

    $result_product_title = $stmt_product_title->get_result();
    $product_title = $dbTools->fetch_assoc($result_product_title);
    // get all duration items from previous except product
    $sql = "SELECT `invoice_items`.`item_name`,`invoice_items`.`item_price`, `invoice_type_id`,`item_type`,DATE_FORMAT(`valid_date_from`, '%Y-%m') AS `month` FROM `invoices`
        INNER JOIN `invoice_items` ON `invoices`.`invoice_id`=`invoice_items`.`invoice_id`
        WHERE  (`valid_date_from`>=? AND `valid_date_from`<=?)
        AND `invoices`.`invoice_type_id` IN (1,2,3,6)
        AND `invoice_items`.`item_name` NOT LIKE '%product%'
        AND `invoice_items`.`item_type`='duration'
        AND `invoices`.`customer_id`=?
        AND `invoices`.`order_id`=?";
    $stmt_items = $dbTools->getConnection()->prepare($sql);
    $param1 = $start;
    $param2 = $end;
    $param3 = $postData["customer_id"];
    $param4 = $postData["order_id"];
    $stmt_items->bind_param('ssss', $param1, $param2, $param3, $param4);
    $stmt_items->execute();

    $result_items = $stmt_items->get_result();
    $items = [];
    $item_product = ["item_name" => "product " . $product_title["product_title"], "item_price" => $product_price, "item_type" => "duration"];
    array_push($items, $item_product);
    while ($item = $dbTools->fetch_assoc($result_items)) {
        array_push($items, $item);
    }
    $invoice_query = "INSERT INTO `invoices`
  (`customer_id`,`invoice_type_id`,`valid_date_from`,`valid_date_to`,`order_id`,`product_price`,`reseller_id`)
   VALUES (?,2,?,?,?,?,?)";
    $stmt_invoice = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $postData["customer_id"];

    $start_recurring_date = new DateTime($end);
    $start_recurring_date->add(new DateInterval('P1D'));
    $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-t"));
    if ($postData["product_subscription_type"] == "YEARLY" || $postData["product_subscription_type"] == "yearly") {

        $start_recurring_date = new DateTime($end);
        $start_recurring_date->add(new DateInterval('P1D'));
        $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-d"));
        $end_recurring_date->add(new DateInterval('P1Y'));
        $end_recurring_date->sub(new DateInterval('P1D'));
    }


    $param2 = $start_recurring_date->format("Y-m-d");
    $param3 = $end_recurring_date->format("Y-m-d");
    $param4 = $postData["order_id"];
    $param5 = $product_price;
    $param6 = $postData["reseller_id"];
    $stmt_invoice->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt_invoice->execute();
    $invoice_id = -1;
    if ($stmt_invoice->insert_id > 0) {

        $invoice_id = $stmt_invoice->insert_id;
    } else {

        return FALSE;
    }
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`) VALUES ";

    $total = 0;
    foreach ($items as $key => $value) {
        $total += (double) $value["item_price"];
    }
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $qst_tax_product = ["item_name" => "QST Tax", "item_price" => $qst_tax, "item_type" => "once"];
    array_push($items, $qst_tax_product);
    $gst_tax_product = ["item_name" => "GST Tax", "item_price" => $gst_tax, "item_type" => "once"];
    array_push($items, $gst_tax_product);
    foreach ($items as $key => $value) {

        $invoice_item_query .= "(N'" . $invoice_id . "',N'" . $value["item_name"] . "',N'" . $value["item_price"] . "',N'" . $value["item_price"] . "',N'" . $value["item_type"] . "'),";
    }
    $invoice_item_query = rtrim($invoice_item_query, ",");
    if ($dbTools->query($invoice_item_query) === TRUE) {
        return TRUE;
    }
}

function suspension($dbTools, $postData) {
      $request = "SELECT `action_on_date`,`end_of_suspension` FROM `requests` WHERE `request_id`=?";
      $stmt_request = $dbTools->getConnection()->prepare($request);
      $param1 = $postData["request_id"];
      $stmt_request->bind_param('s', $param1);
      $stmt_request->execute();

      $result_request = $stmt_request->get_result();
      $request = $dbTools->fetch_assoc($result_request);

      $action_on_date = new DateTime($request["action_on_date"]);
      $end_of_suspension = new DateTime($request["end_of_suspension"]);

      $startDate=new DateTime($action_on_date->format("Y-m-1"));
      $startDate->sub(new DateInterval('P1D'));

      $endDate=new DateTime($end_of_suspension->format("Y-m-1"));
      $endDate->sub(new DateInterval('P1D'));


      /// get last product price
      $sql = "SELECT `invoice_type_id`,`product_price` FROM `invoices`
            WHERE  (`valid_date_from`>=? AND `valid_date_from`<=?)
            AND `invoices`.`invoice_type_id` IN (1,2,3,6)
            AND `invoices`.`customer_id`=?
            AND `invoices`.`order_id`=?
            ORDER BY `invoice_id` DESC
            LIMIT 1";
      $stmt_product = $dbTools->getConnection()->prepare($sql);
      $param1 = $startDate->format("Y-m-1");
      $param2 = $startDate->format("Y-m-t");
      $param3 = $postData["customer_id"];
      $param4 = $postData["order_id"];
      $stmt_product->bind_param('ssss', $param1, $param2, $param3, $param4);
      $stmt_product->execute();

      $result_product = $stmt_product->get_result();
      $hasValue = FALSE;
      $product_price = -1;
      while ($product = $dbTools->fetch_assoc($result_product)) {
          $hasValue = TRUE;
          if ($product["invoice_type_id"] == 6) {//then this is terminated
              return "terminate"; //FALSE;
          }

          $product_price = $product["product_price"];
      }
      if ($product_price < 0) {

          return FALSE; // already terminated because doesn't have any invoice int this month
      }
      // get product title
      $sql = "(SELECT `requests`.`product_title`,`requests`.`action_on_date` AS `date_active` FROM `requests` INNER JOIN `orders` ON `orders`.`order_id`=`requests`.`order_id` WHERE `customer_id`=? AND `requests`.`verdict`='approve' AND `requests`.`action`='change_speed' AND `action_on_date`<?)
    UNION
    (SELECT `product_title`,`creation_date` AS `date_active` FROM `orders` WHERE `customer_id`=? )
    ORDER BY `date_active` DESC LIMIT 1";
      $stmt_product_title = $dbTools->getConnection()->prepare($sql);
      $param1 = $postData["customer_id"];
      $param2 = $startDate->format("Y-m-d");
      $stmt_product_title->bind_param('sss', $param1, $param2, $param1);
      $stmt_product_title->execute();

      $result_product_title = $stmt_product_title->get_result();
      $product_title = $dbTools->fetch_assoc($result_product_title);
      // get all duration items from previous except product
      $sql = "SELECT `invoice_items`.`item_name`,`invoice_items`.`item_price`, `invoice_type_id`,`item_type`,DATE_FORMAT(`valid_date_from`, '%Y-%m') AS `month` FROM `invoices`
          INNER JOIN `invoice_items` ON `invoices`.`invoice_id`=`invoice_items`.`invoice_id`
          WHERE  (`valid_date_from`>=? AND `valid_date_from`<=?)
          AND `invoices`.`invoice_type_id` IN (1,2,3,6)
          AND `invoice_items`.`item_name` NOT LIKE '%product%'
          AND `invoice_items`.`item_type`='duration'
          AND `invoices`.`customer_id`=?
          AND `invoices`.`order_id`=?";
      $stmt_items = $dbTools->getConnection()->prepare($sql);
      $param1 = $startDate->format("Y-m-1");
      $param2 = $startDate->format("Y-m-t");
      $param3 = $postData["customer_id"];
      $param4 = $postData["order_id"];
      $stmt_items->bind_param('ssss', $param1, $param2, $param3, $param4);
      $stmt_items->execute();

      $result_items = $stmt_items->get_result();
      $items = [];
      $items_zero = [];
      $item_product = ["item_name" => "product " . $product_title["product_title"], "item_price" => $product_price, "item_type" => "duration"];
      array_push($items, $item_product);
      $item_product_zero = ["item_name" => "product " . $product_title["product_title"], "item_price" => "0", "item_type" => "duration"];
      array_push($items_zero, $item_product_zero);
      while ($item = $dbTools->fetch_assoc($result_items)) {
          array_push($items, $item);
      }



      /// start add start suspension recurring invoice
      $invoice_query = "INSERT INTO `invoices`
    (`customer_id`,`invoice_type_id`,`valid_date_from`,`valid_date_to`,`order_id`,`product_price`,`reseller_id`)
     VALUES (?,2,?,?,?,?,?)";
      $stmt_invoice = $dbTools->getConnection()->prepare($invoice_query);
      $param1 = $postData["customer_id"];

      $start_recurring_date = new DateTime($startDate->format("Y-m-t"));
      $start_recurring_date->add(new DateInterval('P1D'));
      $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-t"));
      if ($postData["product_subscription_type"] == "YEARLY" || $postData["product_subscription_type"] == "yearly") {

          $start_recurring_date = new DateTime($startDate->format("Y-m-t"));
          $start_recurring_date->add(new DateInterval('P1D'));
          $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-d"));
          $end_recurring_date->add(new DateInterval('P1Y'));
          $end_recurring_date->sub(new DateInterval('P1D'));
      }


      $param2 = $start_recurring_date->format("Y-m-d");
      $param3 = $end_recurring_date->format("Y-m-d");
      $param4 = $postData["order_id"];
      $param5 = $product_price;
      $param6 = $postData["reseller_id"];
      $stmt_invoice->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
      $stmt_invoice->execute();
      $invoice_id = -1;
      if ($stmt_invoice->insert_id > 0) {

          $invoice_id = $stmt_invoice->insert_id;
      } else {

          return FALSE;
      }
      $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`) VALUES ";

      $total = 0;
      foreach ($items as $key => $value) {
          $total += (double) $value["item_price"];
      }
      $qst_tax = 0;
      $gst_tax = 0;
      $qst_tax_product = ["item_name" => "QST Tax", "item_price" => $qst_tax, "item_type" => "once"];
      array_push($items_zero, $qst_tax_product);
      $gst_tax_product = ["item_name" => "GST Tax", "item_price" => $gst_tax, "item_type" => "once"];
      array_push($items_zero, $gst_tax_product);
      foreach ($items_zero as $key => $value) {

          $invoice_item_query .= "(N'" . $invoice_id . "',N'" . $value["item_name"] . "',N'" . $value["item_price"] . "',N'" . $value["item_price"] . "',N'" . $value["item_type"] . "'),";
      }
      $invoice_item_query = rtrim($invoice_item_query, ",");
      if ($dbTools->query($invoice_item_query) !== TRUE) {
          return FALSE;
      }

      /// end add start suspension recurring invoice

      /// insert suspension invoice

      $order_id = $postData["order_id"];
      $fees_charged = (double) $postData["fees_charged"];
      $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'8',?,?,?)";
      $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
      $param1 = $postData["customer_id"];
      $param2 = $request["action_on_date"];
      $param3 = $request["action_on_date"];
      $param4 = $order_id;
      $param5 = $postData["product_price"];
      $param6 = $postData["reseller_id"];
      $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
      $stmt3->execute();
      $invoice_id = -1;
      if ($stmt3->insert_id > 0) {
          $invoice_id = $stmt3->insert_id;
      } else {
          return false;
      }
      $total = (double) $fees_charged;
      $qst_tax = $total * 0.09975;
      $gst_tax = $total * 0.05;
      $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
    VALUES (?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
      $param1 = $invoice_id;
      $param2 = "Suspension fees";
      $param3 = $fees_charged;
      $param4 = "QST Tax";
      $param5 = $qst_tax;
      $param6 = "GST Tax";
      $param7 = $gst_tax;
      $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
      $stmt4->bind_param('sssssssssss', $param1, $param2, $param3, $param1, $param4, $param5, $param5, $param1, $param6, $param7, $param7);
      $stmt4->execute();
      /// end insert suspension invoice

      /// insert end_of_suspension recurring invoice
        $invoice_query = "INSERT INTO `invoices`
      (`customer_id`,`invoice_type_id`,`valid_date_from`,`valid_date_to`,`order_id`,`product_price`,`reseller_id`)
       VALUES (?,2,?,?,?,?,?)";
        $stmt_invoice = $dbTools->getConnection()->prepare($invoice_query);
        $param1 = $postData["customer_id"];

        $start_recurring_date = new DateTime($endDate->format("Y-m-t"));
        $start_recurring_date->add(new DateInterval('P1D'));
        $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-t"));
        if ($postData["product_subscription_type"] == "YEARLY" || $postData["product_subscription_type"] == "yearly") {

            $start_recurring_date = new DateTime($endDate->format("Y-m-t"));
            $start_recurring_date->add(new DateInterval('P1D'));
            $end_recurring_date = new DateTime($start_recurring_date->format("Y-m-d"));
            $end_recurring_date->add(new DateInterval('P1Y'));
            $end_recurring_date->sub(new DateInterval('P1D'));
        }


        $param2 = $start_recurring_date->format("Y-m-d");
        $param3 = $end_recurring_date->format("Y-m-d");
        $param4 = $postData["order_id"];
        $param5 = $product_price;
        $param6 = $postData["reseller_id"];
        $stmt_invoice->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
        $stmt_invoice->execute();
        $invoice_id = -1;
        if ($stmt_invoice->insert_id > 0) {

            $invoice_id = $stmt_invoice->insert_id;
        } else {

            return FALSE;
        }
        $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`) VALUES ";

        $total = 0;
        foreach ($items as $key => $value) {
            $total += (double) $value["item_price"];
        }
        $qst_tax = $total * 0.09975;
        $gst_tax = $total * 0.05;
        $qst_tax_product = ["item_name" => "QST Tax", "item_price" => $qst_tax, "item_type" => "once"];
        array_push($items, $qst_tax_product);
        $gst_tax_product = ["item_name" => "GST Tax", "item_price" => $gst_tax, "item_type" => "once"];
        array_push($items, $gst_tax_product);
        foreach ($items as $key => $value) {

            $invoice_item_query .= "(N'" . $invoice_id . "',N'" . $value["item_name"] . "',N'" . $value["item_price"] . "',N'" . $value["item_price"] . "',N'" . $value["item_type"] . "'),";
        }
        $invoice_item_query = rtrim($invoice_item_query, ",");
        if ($dbTools->query($invoice_item_query) !== TRUE) {
            return FALSE;
        }
        /// end add end_of_suspension recurring invoice
}

function insertInvoice($dbTools, $postData) {
    $query = "SELECT
                `customers`.`reseller_id`
          FROM `customers`
          WHERE `customer_id` = ?
          ";
    $stmt1 = $dbTools->getConnection()->prepare($query);
    $customer_id = $postData["customer_id"];
    $stmt1->bind_param('s', $customer_id);


    $stmt1->execute();

    $getCustomers = $stmt1->get_result();


    $postData["reseller_id"] = 0;
    while ($customer_row = $dbTools->fetch_assoc($getCustomers)) {
        $postData["reseller_id"] = $customer_row["reseller_id"];
    }
    if ($postData["product_subscription_type"] == "yearly") {
        return insertInvoiceYearly($dbTools, $postData);
    }
    if ($postData["verdict"] === "approve") {
        if ($postData["action"] === "change_speed") {// change speed
            changeSpeedMonthly($dbTools, $postData);
        } else if ($postData["action"] === "swap_modem") {
            //swap modem
            ////////// add new change speed invoice
            swapModem($dbTools, $postData);
        } else if ($postData["action"] === "change_speed" && is_numeric($postData["modem_id"]) && (int) $postData["modem_id"] > 0) {
            // swap modem and change speed
            changeSpeedMonthly($dbTools, $postData);
            swapModem($dbTools, $postData);
        } else if ($postData["action"] === "moving") {
            // moving
            moving($dbTools, $postData);
        } else if ($postData["action"] === "terminate") {
            // terminate
            terminateMonthly($dbTools, $postData);
        } else if ($postData["action"] === "suspension") {
            // terminate
            suspension($dbTools, $postData);
        }
    }
}

function changeSpeedYearly($dbTools, $postData) {
    $next_days = 0;
    $new_product_price = 0;

    $customer_id = $postData["customer_id"];
    $order_id = $postData["order_id"];
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $postData["action_on_date"] = $request["action_on_date"];
    $previous_invoice_query = "SELECT *,DATEDIFF(`valid_date_to`,`valid_date_from`) AS `duration` FROM `invoices` WHERE `invoice_type_id` in (1,2,3) AND `customer_id`=? ORDER BY `valid_date_from` DESC LIMIT 1";
    $stmt1 = $dbTools->getConnection()->prepare($previous_invoice_query);
    $stmt1->bind_param('s', $customer_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    //there is must be at least one invoice as the initial one is new order must be found before make request
    $previous_invoice = $dbTools->fetch_assoc($result1);
    $valid_date_from = new DateTime($previous_invoice["valid_date_from"]);
    $valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    $previous_durationDays = (int) $previous_invoice["duration"];

    $previous_product_query = "SELECT * FROM `invoice_items` WHERE `invoice_id`=? AND (`item_name` LIKE '%Product%')  ORDER BY `invoice_item_id` ASC LIMIT 1";
    $stmt2 = $dbTools->getConnection()->prepare($previous_product_query);
    $stmt2->bind_param('s', $previous_invoice["invoice_id"]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $previous_product = $dbTools->fetch_assoc($result2);
    $previous_product_price = (double) $previous_product["item_duration_price"];
    $new_valid_date_from = new DateTime($postData["action_on_date"]);
    $new_valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    // if price divided between two months (remaining days from previous month)
    if ($new_valid_date_from >= $valid_date_to) {
        // if action on date (new_valid_date_from) after end of previous invoice (after valid_date_to)
        // then calculate new_valid_date_to and assume recurring invoice before
        // set valid date from and to as new recuring duration
        $previous_product_price = (double) $previous_product["item_price"];
        $valid_date_from = $valid_date_to;
        $valid_date_to = new DateTime($valid_date_from->format("Y-m-d"));
        $valid_date_to->add(new DateInterval('P1Y'));
        /// calculate new_valid_date_to
        $new_valid_date_to = new DateTime($valid_date_to->format('Y-m-d'));
    }
    $previous_duration = $valid_date_from->diff($valid_date_to);
    $previous_days = (int) $previous_duration->days + 1;
    $pricePerDay = $previous_product_price / $previous_days;


    $used_duration = $valid_date_from->diff($new_valid_date_from);
    $used_days = (int) $used_duration->days;
    $refund_price = $previous_product_price - ($pricePerDay * $used_days);



    //calculate new invoice price by divide new price over the year to get price of the day then multiply by the new used days
    $date_first_day = new DateTime($new_valid_date_from->format('Y-01-01'));
    $date_last_day = new DateTime($new_valid_date_from->format('Y-12-31'));
    $full_year = (int) $date_first_day->diff($date_last_day)->days + 1;
    $new_price_per_day = (double) $postData["product_price"] / $full_year;
    $next_days = $new_valid_date_from->diff($new_valid_date_to)->days + 2;
    $new_product_price = $new_price_per_day * ($next_days);




    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'0',?,?,?)";
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_from->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $previous_invoice["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $refund_price;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`,`item_price`,`item_duration_price`,`item_type`)
    VALUES (?,?,?,?,'duration'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Refund for the next " . $next_days . " day(s)";
    $param3 = $previous_invoice["product_price"];
    $param4 = $refund_price;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('ssssssssssss', $param1, $param2, $param3, $param4, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
    /////////// end add refund invoice
    ////////// add new change speed invoice
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'3',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_to->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $new_product_price + (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;


    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
    VALUES (?,?,?,?,'duration'),(?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Product " . $postData["product_title"] . " for the next " . $next_days . " day(s)";
    $param3 = $postData["product_price"];
    $param4 = $new_product_price;
    $param5 = "Change Speed fees";
    $param6 = $fees_charged;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssssssss', $param1, $param2, $param3, $param4, $param1, $param5, $param6, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
}

function terminateYearly($dbTools, $postData) {
    $next_days = 0;
    $new_product_price = 0;

    $customer_id = $postData["customer_id"];
    $order_id = $postData["order_id"];
    $request = "SELECT `action_on_date` FROM `requests` WHERE `request_id`=?";
    $stmt_request = $dbTools->getConnection()->prepare($request);
    $param1 = $postData["request_id"];
    $stmt_request->bind_param('s', $param1);
    $stmt_request->execute();

    $result_request = $stmt_request->get_result();
    $request = $dbTools->fetch_assoc($result_request);
    $postData["action_on_date"] = $request["action_on_date"];
    $previous_invoice_query = "SELECT *,DATEDIFF(`valid_date_to`,`valid_date_from`) AS `duration` FROM `invoices` WHERE `invoice_type_id` in (1,2,3) AND `customer_id`=? ORDER BY `valid_date_from` DESC LIMIT 1";
    $stmt1 = $dbTools->getConnection()->prepare($previous_invoice_query);
    $stmt1->bind_param('s', $customer_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    //there is must be at least one invoice as the initial one is new order must be found before make request
    $previous_invoice = $dbTools->fetch_assoc($result1);
    $valid_date_from = new DateTime($previous_invoice["valid_date_from"]);
    $valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    $previous_durationDays = (int) $previous_invoice["duration"];

    $previous_product_query = "SELECT * FROM `invoice_items` WHERE `invoice_id`=? AND (`item_name` LIKE '%Product%')  ORDER BY `invoice_item_id` ASC LIMIT 1";
    $stmt2 = $dbTools->getConnection()->prepare($previous_product_query);
    $stmt2->bind_param('s', $previous_invoice["invoice_id"]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $previous_product = $dbTools->fetch_assoc($result2);
    $previous_product_price = (double) $previous_product["item_duration_price"];
    $new_valid_date_from = new DateTime($postData["action_on_date"]);
    $new_valid_date_to = new DateTime($previous_invoice["valid_date_to"]);
    // if price divided between two months (remaining days from previous month)
    if ($new_valid_date_from >= $valid_date_to) {
        // if action on date (new_valid_date_from) after end of previous invoice (after valid_date_to)
        // then calculate new_valid_date_to and assume recurring invoice before
        // set valid date from and to as new recuring duration
        $previous_product_price = (double) $previous_product["item_price"];
        $valid_date_from = $valid_date_to;
        $valid_date_to = new DateTime($valid_date_from->format("Y-m-d"));
        $valid_date_to->add(new DateInterval('P1Y'));
        /// calculate new_valid_date_to
        $new_valid_date_to = new DateTime($valid_date_to->format('Y-m-d'));
    }
    $previous_duration = $valid_date_from->diff($valid_date_to);
    $previous_days = (int) $previous_duration->days + 1;
    $pricePerDay = $previous_product_price / $previous_days;


    $used_duration = $valid_date_from->diff($new_valid_date_from);
    $used_days = (int) $used_duration->days;
    $refund_price = $previous_product_price - ($pricePerDay * $used_days);



    //calculate new invoice price by divide new price over the year to get price of the day then multiply by the new used days
    $date_first_day = new DateTime($new_valid_date_from->format('Y-01-01'));
    $date_last_day = new DateTime($new_valid_date_from->format('Y-12-31'));
    $full_year = (int) $date_first_day->diff($date_last_day)->days + 1;
    $new_price_per_day = (double) $postData["product_price"] / $full_year;
    $next_days = $new_valid_date_from->diff($new_valid_date_to)->days + 2;
    $new_product_price = $new_price_per_day * ($next_days);




    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'0',?,?,?)";
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_from->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $previous_invoice["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $refund_price;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;
    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`,`item_price`,`item_duration_price`,`item_type`)
    VALUES (?,?,?,?,'duration'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Refund for the next " . $next_days . " day(s)";
    $param3 = $previous_invoice["product_price"];
    $param4 = $refund_price;
    $param7 = "QST Tax";
    $param8 = $qst_tax;
    $param9 = "GST Tax";
    $param10 = $gst_tax;

    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('ssssssssssss', $param1, $param2, $param3, $param4, $param1, $param7, $param8, $param8, $param1, $param9, $param10, $param10);
    $stmt4->execute();
    /////////// end add refund invoice
    ////////// add new change speed invoice
    $fees_charged = (double) $postData["fees_charged"];
    $invoice_query = "INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`invoice_type_id`,`order_id`,`product_price`,`reseller_id`) VALUES (?,?,?,N'6',?,?,?)";
    $stmt3 = $dbTools->getConnection()->prepare($invoice_query);
    $param1 = $customer_id;
    $param2 = $new_valid_date_from->format('Y-m-d');
    $param3 = $new_valid_date_to->format('Y-m-d');
    $param4 = $order_id;
    $param5 = $postData["product_price"];
    $param6 = $postData["reseller_id"];
    $stmt3->bind_param('ssssss', $param1, $param2, $param3, $param4, $param5, $param6);
    $stmt3->execute();
    $invoice_id = -1;
    if ($stmt3->insert_id > 0) {
        $invoice_id = $stmt3->insert_id;
    } else {
        return false;
    }
    $total = (double) $fees_charged;
    $qst_tax = $total * 0.09975;
    $gst_tax = $total * 0.05;

    $invoice_item_query = "INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`)
    VALUES (?,?,N'0',?,'once'),(?,?,?,?,'once'),(?,?,?,?,'once')";
    $param1 = $invoice_id;
    $param2 = "Terminate fees";
    $param3 = $fees_charged;
    $param4 = "QST Tax";
    $param5 = $qst_tax;
    $param6 = "GST Tax";
    $param7 = $gst_tax;


    $stmt4 = $dbTools->getConnection()->prepare($invoice_item_query);
    $stmt4->bind_param('sssssssssss', $param1, $param2, $param3, $param1, $param4, $param5, $param5, $param1, $param6, $param7, $param7);
    $stmt4->execute();
}

function insertInvoiceYearly($dbTools, $postData) {
    if ($postData["verdict"] === "approve") {
        if ($postData["action"] === "change_speed") {// change speed
            changeSpeedYearly($dbTools, $postData);
        } else if ($postData["action"] === "swap_modem") {
            //swap modem
            swapModem($dbTools, $postData);
        } else if ($postData["action"] === "change_speed" && is_numeric($postData["modem_id"]) && (int) $postData["modem_id"] > 0) {
            // swap modem and change speed
            changeSpeedYearly($dbTools, $postData);
            swapModem($dbTools, $postData);
        } else if ($postData["action"] === "moving") {
            // moving
            moving($dbTools, $postData);
        } else if ($postData["action"] === "terminate") {
            // terminate
            terminateYearly($dbTools, $postData);
        }
    }
}

?>
