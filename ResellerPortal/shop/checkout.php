<?php
include_once "../header.php";
?>

<?php
include 'GlobalOnePaymentXMLTools.php';

$mGlobalOnePaymentXMLTools = new GlobalOnePaymentXMLTools();


/*
  // For testing purposes
  $_POST["card_number"] = "5526123000333124";
  //$_POST["card_type"] = "MasterCard";
  $_POST["card_expiry"] = "1021";
  $_POST["card_holders_name"] = "Ali Al-Saffar";
  $_POST["card_cvv"] = "580";
  //
 */
$product_id = intval($_POST["product"]);
$has_discount = $_POST['has_discount']==='yes';
$free_modem = $_POST['free_modem']==='yes';
$free_router = $_POST['free_router']==='yes';
$free_adapter = $_POST['free_adapter']==='yes';
$free_installation = $_POST['free_installation']==='yes';
$free_transfer = $_POST['free_transfer']==='yes';

if(!isset($_POST["options"]["inventory_modem_price"]))
{
  $_POST["options"]["inventory_modem_price"]="no";
}
$_POST["options"]["free_modem"] = $_POST['free_modem'];
$_POST["options"]["free_router"] = $_POST['free_router'];
$_POST["options"]["free_adapter"] = $_POST['free_adapter'];
$_POST["options"]["free_installation"] = $_POST['free_installation'];
$_POST["options"]["free_transfer"] = $_POST['free_transfer'];
$_POST["options"]["discount"] = 0;
$_POST["options"]["discount_duration"] = "three_months";

//if user selects phone change product type to phone
$product_type = "internet";
if ($product_id == 619 || $product_id == 654 || $product_id == 653 || $product_id == 661)
    $product_type = "phone";


//If it is internet product
if ($product_type == "internet") {

    //Get start date
    if ($_POST["options"]["cable_subscriber"] == "yes") {
        $cancellation_date = $_POST["options"]["cancellation_date"];
        $start_date = new DateTime($cancellation_date);
    } else {
        $installation_date_1 = $_POST["options"]["installation_date_1"];
        $start_date = new DateTime($installation_date_1);
    }

    //Get product info
    $subscription_period_type = "MONTHLY";
    $sql="SELECT * FROM `products` INNER JOIN `reseller_discounts`
      on `products`.`product_id`=`reseller_discounts`.`product_id`
      WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "'
      and `products`.`product_id`='".$product_id."'";
    $result_product = $dbTools->query($sql);

    if($result_product->num_rows ==0)
    $result_product = $dbTools->query("SELECT * FROM `products` where `products`.`product_id`='".$product_id."'");


    if ($result_product->num_rows > 0) {
        $row_product = $result_product->fetch_assoc();
        if (strpos($row_product["subscription_type"], 'yearly') !== false) { // Check they type of payment (yearly or monthly)
            $subscription_period_type = "YEARLY";
        }
        $product_price = $row_product["price"];

        if($has_discount && isset($row_product["discount"]) && (int)$row_product["discount"] > 0)
        {
          $_POST["options"]["discount"] = $row_product["discount"];
          $_POST["options"]["discount_duration"]=$row_product["discount_duration"];
          $product_price=(float)$row_product['price']-((float)$row_product['price']*(((float)$row_product['discount']/100)));
          $product_price=round($product_price,2);
        }

    }

    $total_price = 0;
    $price_of_remaining_days = 0;
    $installation_transfer_cost = 0;
    $router_cost = 0;
    $modem_cost = 0;
    $remainingDays = 0; //Remaining days in the month
    $value_has_no_tax = 0; // Exclude items that have no tax such as deposits
    $gst_tax = 0;
    $qst_tax = 0;
    $additional_service = 0;
    $static_ip = 0;



    //If rent modem
    //If rent modem
    if ($_POST["options"]["inventory_modem_price"] == "yes") {
        $modem_cost = 59.90;

        //Deposit has no tax
        //$value_has_no_tax = $modem_cost;
    }
    if ($_POST["options"]["modem"] == "rent") {
        $modem_cost = 59.90;

        if($has_discount && $free_modem)
        $modem_cost=0;
        //Deposit has no tax
        //$value_has_no_tax = $modem_cost;
    }
    if ($_POST["options"]["modem"] == "buy") {
        $modem_cost = 200;
    }

    if ($_POST["options"]["router"] == "rent") { //If rent router
        $router_cost = 2.90;
        if($has_discount && $free_router)
        $router_cost=0;

    }
    else if($_POST["options"]["router"] == "rent_hap_lite") { //If rent hap lite router
      $router_cost = 4.90;
    } else if ($_POST["options"]["router"] == "buy_hap_ac_lite") { //if buy hap ac lite
        $router_cost = 74.00;
    } else if ($_POST["options"]["router"] == "buy_hap_mini") { //if buy hap mini
        $router_cost = 39.90;
    }


//Check additional service
    if (isset($_POST["options"]["additional_service"]) && $_POST["options"]["additional_service"] == "yes") {
        $additional_service = 5;
    }
//Check static ip
    if (isset($_POST["options"]["static_ip"]) && $_POST["options"]["static_ip"] == "yes") {
        $static_ip = 20;
    }

//if NOT yearly payment, check monthly (no contract) for transfer or installation fees.
    //If user selects 60 or 120, then charge him the setup fees anyways.
    if ($subscription_period_type == "MONTHLY") {
        if ($_POST["options"]["plan"] == "monthly"
            || $product_id == 416
            || $product_id == 418) {
            if ($_POST["options"]["cable_subscriber"] == "yes")
            {
              $installation_transfer_cost = 19.90;
              if($has_discount && $free_transfer)
              $installation_transfer_cost=0;
            }
            else
            {
              $installation_transfer_cost = 60.00;
              if($has_discount && $free_installation)
              $installation_transfer_cost=0;
            }
        }

    }


    //get number of days in this month
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $start_date->format('m'), $start_date->format('Y'));

    //Calculate the remaining days
    $remainingDays = $days_in_month - $start_date->format('d') + 1;

    //If 1st day selected, price_of_remaining_days is 0
    if ((int) $start_date->format('d') == 1) {
        $price_of_remaining_days = 0;
    } else {
        if ($subscription_period_type == "YEARLY") { //if yearly payment, divide price by 12 months
            $price_of_remaining_days = (($product_price / 12) / $days_in_month) * $remainingDays;
            $price_of_remaining_days += $additional_service / $days_in_month * $remainingDays; // add additional service fees
            $price_of_remaining_days += $static_ip / $days_in_month * $remainingDays; // add static ip fees
        } else { // if monthly payment
            $price_of_remaining_days = ($product_price / $days_in_month) * $remainingDays;
            $price_of_remaining_days += $additional_service / $days_in_month * $remainingDays; // add additional service fees
            $price_of_remaining_days += $static_ip / $days_in_month * $remainingDays; // add static_ip fees
            if ($_POST["options"]["router"] == "rent") { //if rent router, add rent cost of the remaining day
                $price_of_remaining_days += ($router_cost / $days_in_month) * $remainingDays;
            }
            else if ($_POST["options"]["router"] == "rent_hap_lite") { //if rent hap lite router, add rent cost of the remaining day
                $price_of_remaining_days += ($router_cost / $days_in_month) * $remainingDays;
            }
        }
    }

    //Calculate total price
    $total_price = $product_price + $price_of_remaining_days + $installation_transfer_cost + $router_cost + $modem_cost + $additional_service + $static_ip;

    //Calculate texes
    $qst_tax = ($total_price - $value_has_no_tax) * 0.09975;
    $gst_tax = ($total_price - $value_has_no_tax) * 0.05;

    //Add taxes to total price
    $total_price += $qst_tax + $gst_tax;

    //To save current prices in order_options
    $_POST["options"]["product_price"] = $product_price;
    $_POST["options"]["remaining_days_price"] = $price_of_remaining_days;
    $_POST["options"]["setup_price"] = $installation_transfer_cost;
    $_POST["options"]["modem_price"] = $modem_cost;
    $_POST["options"]["router_price"] = $router_cost;
    $_POST["options"]["additional_service_price"] = $additional_service;
    $_POST["options"]["static_ip_price"] = $static_ip;
    $_POST["options"]["total_price"] = $total_price;
    $_POST["options"]["qst_tax"] = $qst_tax;
    $_POST["options"]["gst_tax"] = $gst_tax;

    //Calculate recurring amount
    $subscription_recurring_amount = $product_price + $additional_service + $static_ip;
    if ($_POST["options"]["router"] == "rent") { //If rent router, add $2.90 on the recurring amount

        if(!($has_discount && $free_router))
        $subscription_recurring_amount += 2.90;
    }
    else if($_POST["options"]["router"] == "rent_hap_lite") { //If rent hap lite router, add $4.90 on the recurring amount
      $subscription_recurring_amount +=4.90;
    }
} else if ($product_type == "phone") {
    //Get start date
    $start_date = new DateTime();

    //Get product info

    $subscription_period_type = "MONTHLY";
    $sql="SELECT * FROM `products` INNER JOIN `reseller_discounts`
      on `products`.`product_id`=`reseller_discounts`.`product_id`
      WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "'
      and `products`.`product_id`='".$product_id."'";
    $result_product = $dbTools->query($sql);

    if($result_product->num_rows ==0)
    $result_product = $dbTools->query("SELECT * FROM `products` where `products`.`product_id`='".$product_id."'");


    if ($result_product->num_rows > 0) {
        $row_product = $result_product->fetch_assoc();
        if (strpos($row_product["subscription_type"], 'yearly') !== false) { // Check they type of payment (yearly or monthly)
            $subscription_period_type = "YEARLY";
        }
        $product_price = $row_product["price"];
        if($has_discount && isset($row_product["discount"]) && (int)$row_product["discount"] > 0)
        {
          $_POST["options"]["discount"] = $row_product["discount"];
          $_POST["options"]["discount_duration"]=$row_product["discount_duration"];
          $product_price=(float)$row_product['price']-((float)$row_product['price']*(((float)$row_product['discount']/100)));
          $product_price=round($product_price,2);
        }

    }
    $total_price = 0;
    $price_of_remaining_days = 0;
    $transfer_cost = 0;
    $adapter_cost = 0;
    $remainingDays = 0; //Remaining days in the month
    $value_has_no_tax = 0; // Exclude items that have no tax such as deposits
    $gst_tax = 0;
    $qst_tax = 0;

    //If rent modem
    if ($_POST["options"]["adapter"] == "buy_Cisco_SPA112") {
        $adapter_cost = 59.90;
        if($has_discount && $free_adapter)
        $adapter_cost=0;
    }
    //NOTICE: Have to be changed later
    //$adapter_cost = 0;

    //If transfer
    if ($_POST["options"]["you_have_phone_number"] == "yes") {
        $transfer_cost = 15;
    }

    //get number of days in this month
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $start_date->format('m'), $start_date->format('Y'));

    //Calculate the remaining days
    $remainingDays = $days_in_month - $start_date->format('d') + 1;

    //If 1st day selected, price_of_remaining_days is 0
    if ((int) $start_date->format('d') == 1) {
        $price_of_remaining_days = 0;
    } else {
        if ($subscription_period_type == "YEARLY") { //if yearly payment, divide price by 12 months
            $price_of_remaining_days = (($product_price / 12) / $days_in_month) * $remainingDays;
        } else { // if monthly payment
            $price_of_remaining_days = ($product_price / $days_in_month) * $remainingDays;
        }
    }

    //Calculate total price
    $total_price = $product_price + $price_of_remaining_days + $transfer_cost + $adapter_cost;

    //Calculate texes
    $qst_tax = ($total_price - $value_has_no_tax) * 0.09975;
    $gst_tax = ($total_price - $value_has_no_tax) * 0.05;

    //Add taxes to total price
    $total_price += $qst_tax + $gst_tax;

    //To save current prices in order_options
    $_POST["options"]["product_price"] = $product_price;
    $_POST["options"]["remaining_days_price"] = $price_of_remaining_days;
    $_POST["options"]["setup_price"] = $transfer_cost;
    $_POST["options"]["adapter_price"] = $adapter_cost;
    $_POST["options"]["total_price"] = $total_price;
    $_POST["options"]["qst_tax"] = $qst_tax;
    $_POST["options"]["gst_tax"] = $gst_tax;


    $subscription_recurring_amount = $product_price;
}

$subscription_recurring_amount += $subscription_recurring_amount * 0.09975 + $subscription_recurring_amount * 0.05; // Add tax for recurring amount
$subscription_recurring_amount = number_format((float) $subscription_recurring_amount, 2, '.', '');

//Initial amount is same as total_price
$subscription_initial_amount = number_format((float) $total_price, 2, '.', '');

//Find out 1st recurring date
$subscription_start_date = "";
if ($subscription_period_type == "YEARLY") { //If yearly payment
    if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
        $start_date->add(new DateInterval('P1Y'));
        $subscription_start_date = $start_date->format('d-m-Y');
    } else { // if not 1st day, add 1 year plus one month
        $start_date->add(new DateInterval('P1Y'));
        $start_date->add(new DateInterval('P1M'));
        $start_date->modify('first day of this month');
        $subscription_start_date = $start_date->format('d-m-Y');
    }
} else { // if payment monthly
    if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
        $start_date->add(new DateInterval('P1M'));
        $subscription_start_date = $start_date->format('d-m-Y');
    } else { // if not 1st day, add 1 year plus one month
        $start_date->add(new DateInterval('P2M'));
        $start_date->modify('first day of this month');
        $subscription_start_date = $start_date->format('d-m-Y');
    }
}

//Get unique random merchant reference
$merchantref = uniqid();

//If reseller selects an already existed customer, dont create customer and use previous card.
$secure_card_merchantref = false;
if (intval($_POST["customer_id"]) > 0) {
    $secure_card_merchantref_sql = "select * from `merchantrefs` where `customer_id`='" . intval($_POST["customer_id"]) . "' and `is_credit`='yes'";
    $secure_card_merchantref_result = $conn_routers->query($secure_card_merchantref_sql);
    if ($secure_card_merchantref_result->num_rows > 0) {
        $secure_card_merchantref_row = $secure_card_merchantref_result->fetch_assoc();
        $secure_card_merchantref = $secure_card_merchantref_row["merchantref"];
    }
}

// **** FOR TESTING PURPOSES ONLY - START
//echo $subscription_initial_amount . " - " . $subscription_recurring_amount . " - " . $subscription_start_date;

//$subscription_initial_amount = "0.01";
//$subscription_recurring_amount = "1";
// **** FOR TESTING PURPOSES ONLY - END
?>
<title>Checkout</title>
<script>
<?php
if ($_POST["card_type"] != "cache_on_delivery") {
    if ($secure_card_merchantref == false) {
        ?>
            $(document).ready(function () {
                var is_complete = false;
                $(window).bind('beforeunload', function () {
                    if (is_complete == false)
                        return 'Please do not leave before checkout processing completes';
                });

                $("div.processing-content").show();
                $("div.succeeded-content").hide();
                $("div.failed-content").hide();

                //1- register
                $("div.process-caption").html("Registering Card...");
                $.post("checkout_processes.php?do=register", {
                    card_number: '<?= $_POST["card_number"] ?>',
                    card_type: '<?= $_POST["card_type"] ?>',
                    card_expiry: '<?= $_POST["card_expiry"] ?>',
                    card_holders_name: '<?= $_POST["card_holders_name"] ?>',
                    card_cvv: '<?= $_POST["card_cvv"] ?>',
                    merchant_reference: '<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data == 1) {
                                $("div.process-caption").html("1-Registering Card Done<br/>");

                                //2- Subscription
                                $("div.process-caption").html("Making subscription...");
                                $.post("checkout_processes.php?do=subscription", {
                                    subscription_start_date: '<?= $subscription_start_date ?>',
                                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                                    initial_amount: '<?= $subscription_initial_amount ?>',
                                    period_type: '<?= $subscription_period_type ?>',
                                    merchant_reference: '<?= $merchantref ?>'})
                                        .done(function (data) {
                                            if (data == 1) {
                                                $("div.process-caption").html("Subscription Done...");

                                                //3- Add Order to customer
                                                $("div.process-caption").html("Adding order...");
                                                $.post("checkout_processes.php?do=registerCustomerAndAddOrder", {
                                                    product: '<?= $product_id ?>',
                                                    full_name: '<?= $_POST["full_name"] ?>',
                                                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                                                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                                                    postal_code: `<?= $_POST["postal_code"] ?>`,
                                                    city: `<?= $_POST["city"] ?>`,
                                                    email: '<?= $_POST["email"] ?>',
                                                    phone: '<?= $_POST["phone"] ?>',
                                                    note: `<?= $_POST["note"] ?>`,
                                                    customer_id: '<?= $_POST["customer_id"] ?>',
                                                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                                                    merchantref: '<?= $merchantref ?>'})
                                                        .done(function (data) {
                                                            if (data != 0) {
                                                                $("div.process-caption").html("Order Sent Successfully");
                                                                orderSubmittedSuccessfully(data);
                                                            } else {
                                                                $("div.process-caption").html("Order failed. " + data);
                                                                orderFailed("Order failed. " + data);
                                                            }
                                                        });
                                            } else {
                                                $("div.process-caption").html("Subscription failed. " + data);
                                                orderFailed("Subscription failed. " + data);
                                            }
                                        });

                            } else {
                                $("div.process-caption").html("Subscription failed. " + data);
                                orderFailed("Regisatrtion failed. " + data);
                            }
                        });

                function orderSubmittedSuccessfully(order_id) {
                    var myarr = order_id.split("_");
                    $('.print-button').attr("href", "print_order.php?order_id=" + myarr[0]);
                    $("h3.order-id").html("Order id: " + myarr[1]);
                    $("div.processing-content").hide();
                    $("div.succeeded-content").show();
                    is_complete = true;
                }
                function orderFailed(reason) {
                    $("div.processing-content").hide();
                    $("div.failed-content").show();
                    $("span.failed-reason").html("Error: " + reason);
                    is_complete = true;
                }
            });
        <?php
    } else { //if existed customer
        ?>
            $(document).ready(function () {
                var is_complete = false;
                $(window).bind('beforeunload', function () {
                    if (is_complete == false)
                        return 'Please do not leave before checkout processing completes';
                });

                $("div.processing-content").show();
                $("div.succeeded-content").hide();
                $("div.failed-content").hide();

                //1- Do payment
                $("div.process-caption").html("Adding order...");
                $.post("checkout_processes.php?do=payment", {
                    card_number: '<?= $_POST["card_number"] ?>',
                    card_type: '<?= $_POST["card_type"] ?>',
                    card_expiry: '<?= $_POST["card_expiry"] ?>',
                    card_holders_name: '<?= $_POST["card_holders_name"] ?>',
                    card_cvv: '<?= $_POST["card_cvv"] ?>',
                    merchant_reference: '<?= $merchantref ?>',
                    amount: '<?= $subscription_initial_amount ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                $("div.process-caption").html("Payment done Successfully");

                                //2- Add Order to customer
                                $("div.process-caption").html("Adding order...");
                                $.post("checkout_processes.php?do=registerCustomerAndAddOrder", {
                                    product: '<?= $product_id ?>',
                                    full_name: '<?= $_POST["full_name"] ?>',
                                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                                    postal_code: `<?= $_POST["postal_code"] ?>`,
                                    city: `<?= $_POST["city"] ?>`,
                                    email: '<?= $_POST["email"] ?>',
                                    phone: '<?= $_POST["phone"] ?>',
                                    note: `<?= $_POST["note"] ?>`,
                                    customer_id: '<?= $_POST["customer_id"] ?>',
                                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                                    existed_merchant_reference: '<?= $secure_card_merchantref ?>',
                                    merchantref: '<?= $merchantref ?>'})
                                        .done(function (data) {
                                            if (data != 0) {
                                                $("div.process-caption").html("Order Sent Successfully");
                                                orderSubmittedSuccessfully(data);
                                            } else {
                                                $("div.process-caption").html("Order failed. " + data);
                                                orderFailed("Order failed. " + data);
                                            }
                                        });
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });

                function orderSubmittedSuccessfully(order_id) {
                    var myarr = order_id.split("_");
                    $('.print-button').attr("href", "print_order.php?order_id=" + myarr[0]);
                    $("h3.order-id").html("Order id: " + myarr[1]);
                    $("div.processing-content").hide();
                    $("div.succeeded-content").show();
                    is_complete = true;
                }
                function orderFailed(reason) {
                    $("div.processing-content").hide();
                    $("div.failed-content").show();
                    $("span.failed-reason").html("Error: " + reason);
                    is_complete = true;
                }
            });
        <?php
    }
} else { //if cache on delivery
    if ($secure_card_merchantref == false) {
        ?>
            $(document).ready(function () {
                var is_complete = false;
                $(window).bind('beforeunload', function () {
                    if (is_complete == false)
                        return 'Please do not leave before checkout processing completes';
                });

                $("div.processing-content").show();
                $("div.succeeded-content").hide();
                $("div.failed-content").hide();

                //1- register
                $.post("checkout_processes.php?do=registerCustomerAndAddOrder", {
                    product: '<?= $product_id ?>',
                    full_name: '<?= $_POST["full_name"] ?>',
                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                    postal_code: `<?= $_POST["postal_code"] ?>`,
                    city: `<?= $_POST["city"] ?>`,
                    email: '<?= $_POST["email"] ?>',
                    phone: '<?= $_POST["phone"] ?>',
                    note: `<?= $_POST["note"] ?>`,
                    customer_id: '<?= $_POST["customer_id"] ?>',
                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                    merchantref: 'cache_on_delivery_<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                $("div.process-caption").html("Order Sent Successfully");
                                orderSubmittedSuccessfully(data);
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });

                function orderSubmittedSuccessfully(order_id) {
                    var myarr = order_id.split("_");
                    $('.print-button').attr("href", "print_order.php?order_id=" + myarr[0]);
                    $("h3.order-id").html("Order id: " + myarr[1]);
                    $("div.processing-content").hide();
                    $("div.succeeded-content").show();
                    is_complete = true;
                }
                function orderFailed(reason) {
                    $("div.processing-content").hide();
                    $("div.failed-content").show();
                    $("span.failed-reason").html("Error: " + reason);
                    is_complete = true;
                }
            });
        <?php
    } else { //if existed customer
        ?>
            $(document).ready(function () {
                var is_complete = false;
                $(window).bind('beforeunload', function () {
                    if (is_complete == false)
                        return 'Please do not leave before checkout processing completes';
                });

                $("div.processing-content").show();
                $("div.succeeded-content").hide();
                $("div.failed-content").hide();

                //1- Add order
                $.post("checkout_processes.php?do=registerCustomerAndAddOrder", {
                    product: '<?= $product_id ?>',
                    full_name: '<?= $_POST["full_name"] ?>',
                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                    postal_code: `<?= $_POST["postal_code"] ?>`,
                    city: `<?= $_POST["city"] ?>`,
                    email: '<?= $_POST["email"] ?>',
                    phone: '<?= $_POST["phone"] ?>',
                    note: `<?= $_POST["note"] ?>`,
                    customer_id: '<?= $_POST["customer_id"] ?>',
                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                    existed_merchant_reference: 'cache_on_delivery_<?= $merchantref ?>',
                    merchantref: 'cache_on_delivery_<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                $("div.process-caption").html("Order Sent Successfully");
                                orderSubmittedSuccessfully(data);
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });

                function orderSubmittedSuccessfully(order_id) {
                    var myarr = order_id.split("_");
                    $('.print-button').attr("href", "print_order.php?order_id=" + myarr[0]);
                    $("h3.order-id").html("Order id: " + myarr[1]);
                    $("div.processing-content").hide();
                    $("div.succeeded-content").show();
                    is_complete = true;
                }
                function orderFailed(reason) {
                    $("div.processing-content").hide();
                    $("div.failed-content").show();
                    $("span.failed-reason").html("Error: " + reason);
                    is_complete = true;
                }
            });
        <?php
    }
}
?>
</script>

<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>


<center>
    <div class="processing-content">
        <h2 style="color:red;">Important: Do not leave or refresh this page until checkout process is done.</h1>
            <h3>Please wait while processing...</h2>
        <div class="loader"></div>
        <h5 class="process-caption" style="color:#00cc00;">Register</h4>
    </div>

    <div class="succeeded-content">
        <div class="alert alert-success order-result">
            <strong>Congratulation!</strong> Order sent successfully!
        </div>
        <h3 class="order-id" style="color: #990099">Order id: 111</h2>
            <a href="" target="_blank" class="print-button"><image class="img-thumbnail" style="width: 50px;" src="<?= $site_url ?>/img/print-icon.png" /></a>
    </div>
    <div class="failed-content">
        <div class="alert alert-danger">
            <strong>Failed!</strong> Error occurred, please call the administrator for more information.<br/>
            <span class="failed-reason"></span>
        </div>
    </div>

</center>

<?php
include_once "../footer.php";
?>
