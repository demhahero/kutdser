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
            $result_customer = $conn_routers->query("INSERT INTO `customers` (
                `full_name` ,
                `address_line_1` ,
                `address_line_2` ,
                `postal_code` ,
                `city` ,
                `email`,
                `phone`,
                `is_reseller`,
                `reseller_id`,
                `note`
            )
            VALUES (
                '" . mysql_real_escape_string($_POST["full_name"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["address_line_1"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["address_line_2"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["postal_code"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["city"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["email"]) . "',"
                    . " '" . mysql_real_escape_string($_POST["phone"]) . "',"
                    . " '0' ,"
                    . " '" . $reseller_id . "' ,"
                    . " '" . mysql_real_escape_string($_POST["note"]) . "'
            );");

            $customer_id = $conn_routers->insert_id; //New customer's ID
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

        $result_product = $conn_routers->query("select * from `products` where `product_id`='" . $product_id . "'");
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
                . "`extra_order_recurring_status` "
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
                . "'" . $extra_order_recurring_status . "'"
                . ")";

        $result_order = $conn_routers->query($order_query);

        $order_id = $conn_routers->insert_id; //New order's ID


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
                    . "'" . $options['free_transfer'] . "'"
                    . ")";
            //3- Add order options

            $result_order_options = $conn_routers->query($order_option_query);

            //Assgin the modem to the new customer if it is from inventory
            if ($options['modem'] == "inventory") {
                $conn_routers->query("update `modems` set `customer_id`='" . $customer_id . "' where `modem_id`='" . $options['modem_id'] . "'");
            }
        }

        //if existed customer, do not add new merchantref
        if (!isset($_POST["existed_merchant_reference"])) {
            //4- insert Order Merchant Ref
            $result_merchantrefs = $conn_routers->query("insert into `merchantrefs` ("
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
            $result_merchantrefs = $conn_routers->query("insert into `merchantrefs` ("
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

        
        if ($result_customer && $result_order && $result_order_options && $result_merchantrefs)
        {
            $orid = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
            echo $order_id . "_" . (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));

            try {
                $printOrder = new PrintOrder();
                file_put_contents('last_order.pdf', $printOrder->output($order_id));

                $to = mysqli_real_escape_string($conn_routers,$_POST["email"]);
                $body = "Dear Customer,\nWe would like to thank you for using our services,\nYour order (".$orid.") has been received and your invoice is attached\nTo finalize your order, please read our Terms and Conditions on (https://www.amprotelecom.com/terms-and-conditions/) and agree by replying to this email with 'I agree'\nBest,\nAmProTelecom INC.";

                // Create the Transport
                $transport = (new Swift_SmtpTransport('mail.amprotelecom.com', 25))
                        ->setUsername('alialsaffar')
                        ->setPassword('zOIq6dX$@Pq44M')
                ;

                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);

                // Create a message
                $message = (new Swift_Message('AmProTelecom INC. - Your Order'))
                        ->setFrom(['info@amprotelecom.com' => 'AmProTelecom INC.'])
                        ->setTo([$to, 'info@amprotelecom.com'])
                        ->setBody($body)
                        ->attach(Swift_Attachment::fromPath(__DIR__ . "/last_order.pdf"))
                ;

                // Send the message
                $result = $mailer->send($message);

            } catch (Exception $e) {

            }
            die();
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
?>
