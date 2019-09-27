<?php

include '../dbconfig.php';
include 'GlobalOnePaymentXMLTools.php';
require_once '../../mikrotik/swiftmailer/vendor/autoload.php';
include 'print_order_class.php';


$mGlobalOnePaymentXMLTools = new GlobalOnePaymentXMLTools();


if ($_GET["do"] == "register") {
    echo $mGlobalOnePaymentXMLTools->secureCardRegister("CARD_" . $_POST["merchant_reference"], $_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"]);
} else if ($_GET["do"] == "payment") {
    echo $mGlobalOnePaymentXMLTools->payment($_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"], "P_" . $_POST["merchant_reference"], $_POST["amount"]);
} else if ($_GET["do"] == "subscription") {
    echo $mGlobalOnePaymentXMLTools->subscriptionRegister("SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["merchant_reference"], $_POST["subscription_start_date"], $_POST["recurring_amount"], $_POST["initial_amount"], $_POST["period_type"]);
} else if ($_GET["do"] == "subscriptionWithMerchantref") {
    echo $mGlobalOnePaymentXMLTools->subscriptionRegister("SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["existed_merchant_reference"], $_POST["subscription_start_date"], $_POST["recurring_amount"], $_POST["initial_amount"], $_POST["period_type"]);
} else if ($_GET["do"] == "registerCustomerAndAddOrder") {

    $creation_date = date("Y-m-d H:i:s");

    $product_id = $_POST["product"];

    if (isset($_POST["full_name"])) {

        if ($_POST["customer_id"] == 0) {


            $is_credit = "yes";

            //Create new customer
            $query="INSERT INTO `customers` (
                `full_name` ,
                `address_line_1` ,
                `address_line_2` ,
                `postal_code` ,
                `city` ,
                `email`,
                `phone`,
                `is_reseller`,
                `reseller_id`,
                `address`,
                `stripe_id`,
                `order_id`,
                `product_id`,
                `start_date`,
                `username`,
                `password`,
                `session_id`,
                `actual_installation_date`,
                `actual_installation_time_from`,
                `actual_installation_time_to`,
                `mac_address`,
                `completion`,
                `join_type`,
                `ip_address`,
                `note`
            )
            VALUES (
                '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["full_name"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["address_line_1"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["address_line_2"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["postal_code"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["city"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["email"]) . "',"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["phone"]) . "',"
                    . " '0' ,"
                    . " '" . $reseller_id . "' ,"
                    . " '' ,"
                    . " '' ,"
                    . " 0 ,"
                    . " 0 ,"
                    . " '1990-01-01' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '1990-01-01' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '' ,"
                    . " '" . mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["note"]) . "'
            );";
            //$result_customer = $dbToolsReseller->query($query);

            $customer_id=-1;
                if($dbToolsReseller->query($query)===TRUE){
                  $last_insert_id=mysqli_fetch_assoc( $dbToolsReseller->query("SELECT last_insert_id() as 'customer_id'"));
                  $customer_id=$last_insert_id["customer_id"];
                }//New customer's ID
        } else {
            $customer_id = $_POST["customer_id"];
            $is_credit = "no";
            $result_customer = true;
        }

        //if existed customer, set extra_order_recurring_status = pending to modify the recurring amount later by the engine
        $extra_order_recurring_status = "pending";
        if (!isset($_POST["existed_merchant_reference"])) { // new Customer
            $extra_order_recurring_status = "";
        }

        $result_product = $dbToolsReseller->query("select * from `products` where `product_id`='" . $product_id . "'");
        $row_product = $result_product->fetch_assoc();
        //2- Create new Order
        $order_query="insert into `orders` ("
                . "`product_id`, "
                . "`creation_date`, "
                . "`status`, "
                . "`reseller_id`, "
                . "`customer_id`, "
                . "`product_title`, "
                . "`product_category`, "
                . "`product_subscription_type`, "
                . "`admin_id`, "
                . "`extra_order_recurring_status`, "
                . "`vl_number` "

                . ") VALUES ("
                . "'" . $product_id . "',"
                . "'" . $creation_date . "', "
                . "'sent', "
                . "'" . $reseller_id . "', "
                . "'" . $customer_id . "', "
                . "'" . $row_product["title"] . "', "
                . "'" . $row_product["category"] . "', "
                . "'" . $row_product["subscription_type"] . "', "
                . "N'0', "
                . "'" . $extra_order_recurring_status . "', "
                . "N''"
                . ")";


        //$result_order = $dbToolsReseller->query($order_query);

        $order_id=-1;
            if($dbToolsReseller->query($order_query)===TRUE){
              $last_insert_id=mysqli_fetch_assoc( $dbToolsReseller->query("SELECT last_insert_id() as 'order_id'"));
              $order_id=$last_insert_id["order_id"];
            }

        if (isset($_POST["options"])) {



            $options = json_decode($_POST['options'], true);
            $properties=[
              "order_id"
              ,"plan"
              ,"modem"
              ,"router"
              ,"cable_subscriber"
              ,"current_cable_provider"
              ,"cancellation_date"
              ,"installation_date_1"
              ,"installation_time_1"
              ,"installation_date_2"
              ,"installation_time_2"
              ,"installation_date_3"
              ,"installation_time_3"
              ,"modem_serial_number"
              ,"modem_mac_address"
              ,"additional_service"
              ,"static_ip"
              ,"product_price"
              ,"additional_service_price"
              ,"static_ip_price"
              ,"setup_price"
              ,"modem_price"
              ,"router_price"
              ,"adapter_price"
              ,"current_phone_number"
              ,"phone_province"
              ,"remaining_days_price"
              ,"total_price"
              ,"gst_tax"
              ,"qst_tax"
              ,"modem_id"
              ,"note"
              , "modem_modem_type"
            ];

            foreach ($properties as $property) {
              if(!isset($options[$property]))
              {
                if($property==="modem_id")
                {
                  $options[$property]=0;
                }
                else{
                  $options[$property]="";
                }

              }
            }
            $cancellation_date="NULL";
            if(isset($options['cancellation_date'])&& strlen($options['cancellation_date'])>9)
            {
              $cancellation_date = "N'".date("Y-m-d G:i:s", strtotime($options['cancellation_date']))."'";
            }
            $installation_date_1="NULL";
            if(isset($options['installation_date_1'])&& strlen($options['installation_date_1'])>9)
            {
              $installation_date_1 = "N'".date("Y-m-d G:i:s", strtotime($options['installation_date_1']))."'";
            }
            $installation_date_2="NULL";
            if(isset($options['installation_date_2'])&& strlen($options['installation_date_2'])>9)
            {
              $installation_date_2 = "N'".date("Y-m-d G:i:s", strtotime($options['installation_date_2']))."'";
            }
            $installation_date_3="NULL";
            if(isset($options['installation_date_3'])&& strlen($options['installation_date_3'])>9)
            {
              $installation_date_3 = "N'".date("Y-m-d G:i:s", strtotime($options['installation_date_3']))."'";
            }

            $order_option_query="insert into `order_options` ("
                    . "`order_id`, "
                    . "`plan`, "
                    . "`modem`, "
                    . "`router`, "
                    . "`cable_subscriber`, "
                    . "`current_cable_provider`, "
                    . "`cancellation_date`, "
                    . "`installation_date_1`, "
                    . "`installation_time_1`, "
                    . "`installation_date_2`, "
                    . "`installation_time_2`, "
                    . "`installation_date_3`, "
                    . "`installation_time_3`, "
                    . "`modem_serial_number`, "
                    . "`modem_mac_address`, "
                    . "`additional_service`, "
                    . "`static_ip`, "
                    . "`product_price`, "
                    . "`additional_service_price`, "
                    . "`static_ip_price`, "
                    . "`setup_price`, "
                    . "`modem_price`, "
                    . "`router_price`, "
                    . "`adapter_price`, "
                    . "`current_phone_number`, "
                    . "`phone_province`, "
                    . "`remaining_days_price`, "
                    . "`total_price`, "
                    . "`gst_tax`, "
                    . "`qst_tax`, "
                    . "`modem_id`, "
                    . "`note`, "
                    . "`modem_modem_type`, "
                    . "`discount`, "
                    . "`discount_duration`, "
                    . "`free_router`, "
                    . "`free_modem`, "
                    . "`free_adapter`, "
                    . "`free_installation`, "
                    . "`completion`, "
                    . "`adapter`, "
                    . "`actual_installation_time_from`, "
                    . "`actual_installation_time_to`, "
                    . "`join_type`, "
                    . "`free_transfer`"
                    . ") VALUES ('"
                    . $order_id . "', "
                    . "'" . $options['plan'] . "', "
                    . "'" . $options['modem'] . "', "
                    . "'" . $options['router'] . "', "
                    . "'" . $options['cable_subscriber'] . "', "
                    . "'" . $options['current_cable_provider'] . "', "
                    . "" . $cancellation_date . ", "
                    . "" . $installation_date_1 . ", "
                    . "'" . $options['installation_time_1'] . "', "
                    . "" . $installation_date_2 . ", "
                    . "'" . $options['installation_time_2'] . "', "
                    . "" . $installation_date_3 . ", "
                    . "'" . $options['installation_time_3'] . "', "
                    . "'" . $options['modem_serial_number'] . "', "
                    . "'" . $options['modem_mac_address'] . "', "
                    . "'" . $options['additional_service'] . "', "
                    . "'" . $options['static_ip'] . "', "
                    . "'" . $options['product_price'] . "', "
                    . "'" . $options['additional_service_price'] . "', "
                    . "'" . $options['static_ip_price'] . "', "
                    . "'" . $options['setup_price'] . "', "
                    . "'" . $options['modem_price'] . "', "
                    . "'" . $options['router_price'] . "', "
                    . "'" . $options['adapter_price'] . "', "
                    . "'" . $options['current_phone_number'] . "', "
                    . "'" . $options['phone_province'] . "', "
                    . "'" . $options['remaining_days_price'] . "', "
                    . "'" . $options['total_price'] . "', "
                    . "'" . $options['gst_tax'] . "', "
                    . "'" . $options['qst_tax'] . "', "
                    . "'" . $options['modem_id'] . "', "
                    . "'', "
                    . "'" . $options['modem_modem_type'] . "', "
                    . "'" . $options['discount'] . "', "
                    . "'" . $options['discount_duration'] . "', "
                    . "'" . $options['free_router'] . "', "
                    . "'" . $options['free_modem'] . "', "
                    . "'" . $options['free_adapter'] . "', "
                    . "'" . $options['free_installation'] . "', "
                    . "'', "
                    . "'', "
                    . "'', "
                    . "'', "
                    . "'', "
                    . "'" . $options['free_transfer'] . "'"
                    . ")";
            //3- Add order options

            $result_order_options = $dbToolsReseller->query($order_option_query);

            //Assgin the modem to the new customer if it is from inventory
            if ($options['modem'] == "inventory") {
                $dbToolsReseller->query("update `modems` set `customer_id`='" . $customer_id . "' where `modem_id`='" . $options['modem_id'] . "'");
            }
        }

        //if existed customer, do not add new merchantref
        if (!isset($_POST["existed_merchant_reference"])) {
            //4- insert Order Merchant Ref
            $result_merchantrefs = $dbToolsReseller->query("insert into `merchantrefs` ("
                    . "`merchantref`, "
                    . "`customer_id`, "
                    . "`order_id`, "
                    . "`is_credit`, "
                    . "`type`"
                    . ") VALUES ("
                    . "'" . $_POST["merchantref"] . "', "
                    . "'" . $customer_id . "', "
                    . "'" . $order_id . "', "
                    . "'" . $is_credit . "', "
                    . "'internet_order'"
                    . ")");
        } else {
            $result_merchantrefs = $dbToolsReseller->query("insert into `merchantrefs` ("
                    . "`merchantref`, "
                    . "`customer_id`, "
                    . "`order_id`, "
                    . "`is_credit`, "
                    . "`type`"
                    . ") VALUES ("
                    . "'" . $_POST["merchantref"] . "', "
                    . "'" . $customer_id . "', "
                    . "'" . $order_id . "', "
                    . "'" . $is_credit . "', "
                    . "'payment'"
                    . ")");
        }


        if ($customer_id>0 && $order_id>0 && $result_order_options && $result_merchantrefs)
        {

          $recurring_date = DateTime::createFromFormat('d-m-Y', $_POST["recurring_date"]);
          $recurring_date->sub(new DateInterval('P1D'));
          $invoice_query="INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`order_id`,`product_price`,`reseller_id`) VALUES (N'".$customer_id."',N'".$_POST["start_active_date"]."',N'".$recurring_date->format('Y-m-d')."',N'".$order_id."',N'".$options["product_price"]."',N'".$reseller_id."')";
          $invoice_id=-1;
          if($dbToolsReseller->query($invoice_query)===TRUE)
          {
            $last_insert_id=mysqli_fetch_assoc( $dbToolsReseller->query("SELECT last_insert_id() as 'invoice_id'"));
            $invoice_id=$last_insert_id["invoice_id"];
          }
          else {
            echo "0";
            die();
          }
          $invoice_item_query="INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`) VALUES ";
          $_POST["invoice_items"]=json_decode($_POST["invoice_items"], true);
          foreach ($_POST["invoice_items"] as $key => $value)
          {
            $invoice_item_query.="(N'".$invoice_id."',N'".$value["item_name"]."',N'".$value["item_price"]."',N'".$value["item_duration_price"]."',N'".$value["item_type"]."'),";
          }
          $invoice_item_query=rtrim($invoice_item_query, ",");
          if($dbToolsReseller->query($invoice_item_query)===TRUE){
              $orid = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
              echo $order_id . "_" . (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));

              try {
                  $printOrder = new PrintOrder();
                  file_put_contents('last_order.pdf', $printOrder->output($order_id));

                  $to = mysqli_real_escape_string($dbToolsReseller->getConnection(),$_POST["email"]);
                  $body = "Dear Customer,\nWe would like to thank you for using our services,"
                          . "\nYour order (".$orid.") has been received and your invoice is attached"
                          . "\nTo finalize your order, please read our Terms and Conditions and agree "
                          . "by replying to this email with 'I agree'\nBest,\n";

                  sendEmail($to, 'Your Order', $body, __DIR__ . "/last_order.pdf");

              } catch (Exception $e) {

              }

              die();
            }
            else
            {
              echo "0";
              die();
            }
        } else {
            echo "0";
            die();
        }
    }
    echo "0";
    die();
} else if ($_GET["do"] == "updateSubscription") {
    echo $mGlobalOnePaymentXMLTools->updateSubscription("SS_" . $_POST["merchant_reference"], "SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["merchant_reference"], $_POST["recurring_amount"]);
}



function sendEmail($to, $title, $body, $attachement) {
    try {
        global $dbTools;
        $request_query = "select * from `settings` where `setting_id` = '1'"; 

        $stmt1 = $dbTools->getConnection()->prepare($request_query);

        $stmt1->execute();

        $result1 = $stmt1->get_result();
        
        $row = mysqli_fetch_array($result1);
        
        // Create the Transport
        $transport = (new Swift_SmtpTransport($row["mail_swift_url"], 25))
                ->setUsername($row["email_swift_username"])
                ->setPassword($row["email_swift_password"])
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($row['mail_name'].' - ' . $title))
                ->setFrom([$row['mail_sender'] => $row['mail_name']])
                ->setTo([$to])
                ->setBody($body)
                ->attach(Swift_Attachment::fromPath($attachement));
        ;

        // Send the message
        $result = $mailer->send($message);
    } catch (Exception $e) {
        
    }
}
?>
