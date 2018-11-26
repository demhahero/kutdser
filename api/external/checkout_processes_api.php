<?php
require_once './init.php';
include 'GlobalOnePaymentXMLTools.php';
require_once 'print_order_class.php';
require_once '../../mikrotik/swiftmailer/vendor/autoload.php';

function validateChooseProduct($data) {
    if ($data["product_type"] === "internet") {
        //If own_moden selected, you have to enter modem information
        if ($data["options"]["modem"] == "own_modem") {
            if (strlen($data["options"]["modem_serial_number"]) < 3
                    || strlen($data["options"]["modem_mac_address"]) < 3
                    || strlen($data["options"]["modem_modem_type"]) < 3
                    ) {
                $message="Enter modem information";
                return $message;
            }
        } else if ($data["options"]["modem"] === "inventory") { // if inventory selected and has no modem
            if ($data["options"]["modem_id"] == null) {
                $message="You have no modems in your inventory";
                return $message;
            }
        }

        //If customer is currently a cable subscriber, he has to enter his provider name and cancellation date.
        if ($data["options"]["cable_subscriber"] === "yes") {
            if ((strlen($data["options"]["current_cable_provider"]) < 3 && strlen($data["options"]["subscriber_other"]) < 3)
                    || strlen($data["options"]["cancellation_date"]) < 3
                    ) {
                $message="Enter current provider's name and cancellation date";
                return $message;
            }
        }

        //If customer is not a cable subscriber, he has to pick dates and times for installation
        if ($data["options"]["cable_subscriber"] === "no") {
            if (strlen($data["options"]["installation_date_1"]) < 3
                    || strlen($data["options"]["installation_date_2"]) < 3
                    || strlen($data["options"]["installation_date_3"]) < 3
                    || strlen($data["options"]["installation_time_1"]) < 3
                    || strlen($data["options"]["installation_time_2"]) < 3
                    || strlen($data["options"]["installation_time_3"]) < 3
                    ) {
                $message="Enter three dates and times for installation";
                return $message;
            }
        }

        return true;
    } else if ($data["product_type"] === "phone") { // Check if he did not enter his current phone number
        if ($data["options"]["you_have_phone_number"] === "yes"
                && strlen($data["options"]["current_phone_number"]) <=3) {
            $message="Enter your current phone number";
            return $message;
        }
        return true;
    }
}

function validateCustomerInformation($data,$customer_id) {
    if ($customer_id <= 0 ) {
        if (strlen($data["full_name"]) < 3
                || strlen($data["email"]) < 3
                || strlen($data["phone"]) < 3
                || strlen($data["address_line_1"]) < 3
                || strlen($data["postal_code"]) < 3
                || strlen($data["city"]) < 3) {
            $message="Missing customer info";
            return $message;
        }
    }
    return true;
}

function validateCardInfo($data) {
    if (strlen($data["card_holders_name"]) < 3
            || strlen($data["card_cvv"]) != 3
            || strlen($data["card_number"]) < 3
            || strlen($data["card_expiry"]) != 4) {
        $message="Missing card info";
        return $message;
    }
    return true;
}

function validateOrder($data,$dbTools,$customer_id){
  if($customer_id>0)
  {
    // check what product does the customer already have
    $query="SELECT `product_category` FROM `orders` WHERE `customer_id`=?";
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$customer_id;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $orders_result = $stmt1->get_result();
    if($stmt1->affected_rows>1){
      return "You already purchased all our products before. if you want to change your internet speed or anything else please make a request";

    }
    $order_row = $dbTools->fetch_assoc($orders_result);
    if($order_row["product_category"]===$data["product_type"])
    {
      return "You already purchased this product before. please request other products to purchase";

    }
  }
  return true;
}


///////////////////////////
if(!isset($_POST["product"]))
{
  echo "{\"error\":true,\"message\":\"No product provided\"}";
  exit();
}
$query="SELECT `category` FROM `products` WHERE `product_id`=?";
$stmt1 = $dbTools->getConnection()->prepare($query);

$param_value=$_POST["product"];
$stmt1->bind_param('s',
                  $param_value
                  ); // 's' specifies the variable type => 'string'


$stmt1->execute();

$product_result = $stmt1->get_result();
$product_row = $dbTools->fetch_assoc($product_result);

if(!$product_row)
{
  echo "{\"error\":true,\"message\":\"No such product found\"}";
  exit();
}
$_POST["product_type"]=$product_row["category"];

$validate_message1= validateChooseProduct($_POST);
$validate_message2= validateCustomerInformation($_POST,$customer_id);
$validate_message3= validateCardInfo($_POST);
$validate_message4= validateOrder($_POST,$dbTools,$customer_id);
if($validate_message1!==true )
{
  echo "{\"error\":true,\"message\":\"".$validate_message1."\"}";
  exit();
}
if($validate_message2!==true )
{
  echo "{\"error\":true,\"message\":\"".$validate_message2."\"}";
  exit();
}
if($validate_message3!==true )
{
  echo "{\"error\":true,\"message\":\"".$validate_message3."\"}";
  exit();
}
if($validate_message4!==true )
{
  echo "{\"error\":true,\"message\":\"".$validate_message4."\"}";
  exit();
}

$mGlobalOnePaymentXMLTools = new GlobalOnePaymentXMLTools();
//Get unique random merchant reference
$merchantref = uniqid();
$_POST["merchant_reference"]=$merchantref;
$_POST["merchantref"]=$merchantref;


$reseller_id=190;// id for AmProTelecom reseller
$product_id = intval($_POST["product"]);
// $has_discount = $_POST['has_discount']==='yes';
// $free_modem = $_POST['free_modem']==='yes';
// $free_router = $_POST['free_router']==='yes';
// $free_adapter = $_POST['free_adapter']==='yes';
// $free_installation = $_POST['free_installation']==='yes';
// $free_transfer = $_POST['free_transfer']==='yes';

if(!isset($_POST["options"]["inventory_modem_price"]))
{
  $_POST["options"]["inventory_modem_price"]="no";
}
// $_POST["options"]["free_modem"] = $_POST['free_modem'];
// $_POST["options"]["free_router"] = $_POST['free_router'];
// $_POST["options"]["free_adapter"] = $_POST['free_adapter'];
// $_POST["options"]["free_installation"] = $_POST['free_installation'];
// $_POST["options"]["free_transfer"] = $_POST['free_transfer'];
// $_POST["options"]["discount"] = 0;
// $_POST["options"]["discount_duration"] = "three_months";

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
    // $sql="SELECT * FROM `products` INNER JOIN `reseller_discounts`
    //   on `products`.`product_id`=`reseller_discounts`.`product_id`
    //   WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "'
    //   and `products`.`product_id`='".$product_id."'";
    // $result_product = $dbToolsReseller->query($sql);
    //
    // if($result_product->num_rows ==0)
    $query="SELECT * FROM `products` where `products`.`product_id`=?";
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$product_id;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result_product = $stmt1->get_result();

    if ($result_product->num_rows > 0) {
        $row_product = $result_product->fetch_assoc();
        if (strpos($row_product["subscription_type"], 'yearly') !== false) { // Check they type of payment (yearly or monthly)
            $subscription_period_type = "YEARLY";
        }
        $product_price = $row_product["price"];

        // if($has_discount && isset($row_product["discount"]) && (int)$row_product["discount"] > 0)
        // {
        //   $_POST["options"]["discount"] = $row_product["discount"];
        //   $_POST["options"]["discount_duration"]=$row_product["discount_duration"];
        //   $product_price=(float)$row_product['price']-((float)$row_product['price']*(((float)$row_product['discount']/100)));
        //   $product_price=round($product_price,2);
        // }

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

        // if($has_discount && $free_modem)
        // $modem_cost=0;
        //Deposit has no tax
        //$value_has_no_tax = $modem_cost;
    }
    if ($_POST["options"]["modem"] == "buy") {
        $modem_cost = 200;
    }

    if ($_POST["options"]["router"] == "rent") { //If rent router
        $router_cost = 2.90;
        // if($has_discount && $free_router)
        // $router_cost=0;

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
              // if($has_discount && $free_transfer)
              // $installation_transfer_cost=0;
            }
            else
            {
              $installation_transfer_cost = 60.00;
              // if($has_discount && $free_installation)
              // $installation_transfer_cost=0;
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

        // if(!($has_discount && $free_router))
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
    // $sql="SELECT * FROM `products` INNER JOIN `reseller_discounts`
    //   on `products`.`product_id`=`reseller_discounts`.`product_id`
    //   WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "'
    //   and `products`.`product_id`='".$product_id."'";
    // $result_product = $dbToolsReseller->query($sql);
    //
    // if($result_product->num_rows ==0)
    $query="SELECT * FROM `products` where `products`.`product_id`=?";
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$product_id;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result_product = $stmt1->get_result();

    if ($result_product->num_rows > 0) {
        $row_product = $result_product->fetch_assoc();
        if (strpos($row_product["subscription_type"], 'yearly') !== false) { // Check they type of payment (yearly or monthly)
            $subscription_period_type = "YEARLY";
        }
        $product_price = $row_product["price"];
        // if($has_discount && isset($row_product["discount"]) && (int)$row_product["discount"] > 0)
        // {
        //   $_POST["options"]["discount"] = $row_product["discount"];
        //   $_POST["options"]["discount_duration"]=$row_product["discount_duration"];
        //   $product_price=(float)$row_product['price']-((float)$row_product['price']*(((float)$row_product['discount']/100)));
        //   $product_price=round($product_price,2);
        // }

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
        // if($has_discount && $free_adapter)
        // $adapter_cost=0;
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


// **** FOR TESTING PURPOSES ONLY - START

$subscription_initial_amount = "0.01";
$subscription_recurring_amount = "1";
// **** FOR TESTING PURPOSES ONLY - END



$_POST["period_type"]=$subscription_period_type;
$_POST["subscription_start_date"]=$subscription_start_date;
$_POST["amount"]=$subscription_initial_amount;
$_POST["recurring_amount"]=$subscription_recurring_amount;
$_POST["initial_amount"]=$subscription_initial_amount;
//Get unique random merchant reference
$merchantref = uniqid();


//If  already existed customer, dont create customer and use previous card.
$secure_card_merchantref = false;
if ($customer_id > 0) {
    $secure_card_merchantref_sql = "SELECT `merchantref` FROM `merchantrefs` WHERE `customer_id`=? and `is_credit`='yes'";
    $stmt1 = $dbTools->getConnection()->prepare($secure_card_merchantref_sql);

    $param_value=$customer_id;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'
    $stmt1->execute();

    $secure_card_merchantref_result = $stmt1->get_result();

    if ($secure_card_merchantref_result->num_rows > 0) {
        $secure_card_merchantref_row = $dbTools->fetch_assoc($secure_card_merchantref_result);
        $secure_card_merchantref = $secure_card_merchantref_row["merchantref"];
    }
}
if ($secure_card_merchantref == false) {
  $message=false;
  try {
    $message=$mGlobalOnePaymentXMLTools->secureCardRegister("CARD_" . $_POST["merchant_reference"], $_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"]);

  } catch (\Exception $e) {
    echo "{\"error\":true,\"message\":\"".$e."\"}";
    exit();
  }


  if($message!=true)
  {
    echo "{\"error\":true,\"message\":\"".$message."\"}";
    exit();
  }
  $message=false;
  try {
    $message=$mGlobalOnePaymentXMLTools->subscriptionRegister("SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["merchant_reference"], $_POST["subscription_start_date"], $_POST["recurring_amount"], $_POST["initial_amount"], $_POST["period_type"]);

  } catch (\Exception $e) {
    echo "{\"error\":true,\"message\":\"".$e."\"}";
    exit();
  }


  if($message!=true)
  {
    echo "{\"error\":true,\"message\":\"".$message."\"}";
    exit();
  }
  $message=true;
  {

      $creation_date = date("Y-m-d H:i:s");

      $product_id = $_POST["product"];

      if (isset($_POST["full_name"])) {
          if ($customer_id == 0) {

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
                          ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " '0' ,"
                      . " ? ,"
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
                      . " ?);";
              $param_value1=$_POST["full_name"];
              $param_value2=$_POST["address_line_1"];
              $param_value3=$_POST["address_line_2"];
              $param_value4=$_POST["postal_code"];
              $param_value5=$_POST["city"];
              $param_value6=$_POST["email"];
              $param_value7=$_POST["phone"];
              $param_value8=$reseller_id;
              $param_value9=$_POST["note"];

              $stmt2 = $dbTools->getConnection()->prepare($query);

              $stmt2->bind_param('sssssssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4,
                                $param_value5,
                                $param_value6,
                                $param_value7,
                                $param_value8,
                                $param_value9
                                ); // 's' specifies the variable type => 'string'


              $stmt2->execute();

              $customer_id=-1;
                  if($stmt2->insert_id>0){
                    $customer_id=$stmt2->insert_id;
                  }//New customer's ID
          } else {
              // $customer_id = $_POST["customer_id"];
              $is_credit = "no";
              $result_customer = true;
          }

          //if existed customer, set extra_order_recurring_status = pending to modify the recurring amount later by the engine
          $extra_order_recurring_status = "pending";
          if ($secure_card_merchantref==false) { // new Customer
              $extra_order_recurring_status = "";
          }
          $query="SELECT * FROM `products` WHERE `product_id`=?";
          $stmtproduct = $dbTools->getConnection()->prepare($query);

          $param_value=$product_id;
          $stmtproduct->bind_param('s',
                            $param_value
                            ); // 's' specifies the variable type => 'string'


          $stmtproduct->execute();

          $result_product = $stmtproduct->get_result();
          $row_product = $dbTools->fetch_assoc($result_product);

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
                  . "?,"
                  . "?, "
                  . "'sent', "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "N'0', "
                  . "?, "
                  . "N''"
                  . ")";

                  $param_value1=$product_id;
                  $param_value2=$creation_date;
                  $param_value3=$reseller_id;
                  $param_value4=$customer_id;
                  $param_value5=$row_product["title"];
                  $param_value6=$row_product["category"];
                  $param_value7=$row_product["subscription_type"];
                  $param_value8=$extra_order_recurring_status ;

                  $stmt_order = $dbTools->getConnection()->prepare($order_query);

                  $stmt_order->bind_param('ssssssss',
                                    $param_value1,
                                    $param_value2,
                                    $param_value3,
                                    $param_value4,
                                    $param_value5,
                                    $param_value6,
                                    $param_value7,
                                    $param_value8
                                    ); // 's' specifies the variable type => 'string'


          $stmt_order->execute();

          $order_result = $stmt_order->get_result();
          $order_id=-1;
              if($stmt_order->insert_id>0){

                $order_id=$stmt_order->insert_id;

              }

          if (isset($_POST["options"])) {


              //$options = json_decode($_POST['options'], true);
              $options = $_POST['options'];
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

                      . "`completion`, "
                      . "`adapter`, "
                      . "`actual_installation_time_from`, "
                      . "`actual_installation_time_to`, "
                      . "`join_type` "
                      . ") VALUES (?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "" . $cancellation_date . ", "
                      . "" . $installation_date_1 . ", "
                      . "?, "
                      . "" . $installation_date_2 . ", "
                      . "?, "
                      . "" . $installation_date_3 . ", "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'', "
                      . "?, "

                      . "'', "
                      . "'', "
                      . "'', "
                      . "'', "
                      . "''"
                      . ")";
              //3- Add order options

              $param_value1=$order_id;
              $param_value2=$options['plan'];
              $param_value3=$options['modem'];
              $param_value4=$options['router'];
              $param_value5=$options['cable_subscriber'];
              $param_value6=$options['current_cable_provider'];
              $param_value7=$options['installation_time_1'];
              $param_value8=$options['installation_time_2'] ;
              $param_value9=$options['installation_time_3'] ;
              $param_value10=$options['modem_serial_number'] ;

              $param_value11=$options['modem_mac_address'];
              $param_value12=$options['additional_service'];
              $param_value13=$options['static_ip'];
              $param_value14=$options['product_price'];
              $param_value15=$options['additional_service_price'];
              $param_value16=$options['static_ip_price'];
              $param_value17=$options['setup_price'];
              $param_value18=$options['modem_price'] ;
              $param_value19=$options['router_price'] ;
              $param_value20=$options['adapter_price'] ;

              $param_value21=$options['current_phone_number'];
              $param_value22=$options['phone_province'];
              $param_value23=$options['remaining_days_price'];
              $param_value24=$options['total_price'];
              $param_value25=$options['gst_tax'];
              $param_value26=$options['qst_tax'];
              $param_value27=$options['modem_id'];
              $param_value28=$options['modem_modem_type'] ;



              $stmt_order_options = $dbTools->getConnection()->prepare($order_option_query);

              $stmt_order_options->bind_param('ssssssssssssssssssssssssssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4,
                                $param_value5,
                                $param_value6,
                                $param_value7,
                                $param_value8,
                                $param_value9,
                                $param_value10,
                                $param_value11,
                                $param_value12,
                                $param_value13,
                                $param_value14,
                                $param_value15,
                                $param_value16,
                                $param_value17,
                                $param_value18,
                                $param_value19,
                                $param_value20,
                                $param_value21,
                                $param_value22,
                                $param_value23,
                                $param_value24,
                                $param_value25,
                                $param_value26,
                                $param_value27,
                                $param_value28
                                ); // 's' specifies the variable type => 'string'


            $stmt_order_options->execute();

            $result_order_options = $stmt_order_options->affected_rows>0?true:false;

              //Assgin the modem to the new customer if it is from inventory
              if ($options['modem'] == "inventory") {
                  $param_value1=$customer_id;
                  $param_value2=$options['modem_id'];
                  $modem_query="update `modems` set `customer_id`=? where `modem_id`=?";
                  $stmt_modem_options = $dbTools->getConnection()->prepare($modem_query);

                  $stmt_modem_options->bind_param('ss',
                                    $param_value1,
                                    $param_value2
                                    ); // 's' specifies the variable type => 'string'


                $stmt_modem_options->execute();

                $result_modem_options = $stmt_modem_options->get_result();

              }
          }

          //if existed customer, do not add new merchantref
          if ($secure_card_merchantref==false) {

              //4- insert Order Merchant Ref
              $param_value1=$_POST["merchantref"];
              $param_value2=$customer_id;
              $param_value3=$order_id;
              $param_value4=$is_credit;
              $query_merchantrefs="INSERT INTO `merchantrefs` ("
                      . "`merchantref`, "
                      . "`customer_id`, "
                      . "`order_id`, "
                      . "`is_credit`, "
                      . "`type`"
                      . ") VALUES ("
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'internet_order'"
                      . ")";
              $stmt_merchantrefs = $dbTools->getConnection()->prepare($query_merchantrefs);


              $stmt_merchantrefs->bind_param('ssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4
                                ); // 's' specifies the variable type => 'string'


              $stmt_merchantrefs->execute();

              $result_merchantrefs = $stmt_merchantrefs->affected_rows>0?true:false;
          } else {
              $param_value1=$_POST["merchantref"];
              $param_value2=$customer_id;
              $param_value3=$order_id;
              $param_value4=$is_credit;

              $query_merchantrefs="INSERT INTO `merchantrefs` ("
                      . "`merchantref`, "
                      . "`customer_id`, "
                      . "`order_id`, "
                      . "`is_credit`, "
                      . "`type`"
                      . ") VALUES ("
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'payment'"
                      . ")";
              $stmt_merchantrefs = $dbTools->getConnection()->prepare($query_merchantrefs);

              $stmt_merchantrefs->bind_param('ssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4
                                ); // 's' specifies the variable type => 'string'


              $stmt_merchantrefs->execute();
              $result_merchantrefs = $stmt_merchantrefs->affected_rows>0?true:false;

          }

          if ($customer_id>0 && $order_id>0 && $result_order_options && $result_merchantrefs)
          {
              $orid = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
              $message=$order_id . "_" . (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));

              try {
                  $printOrder = new PrintOrder();
                  file_put_contents('last_order.pdf', $printOrder->output($order_id));

                  $to = mysqli_real_escape_string($dbTools->getConnection(),$_POST["email"]);
                  $body = "Dear Customer,\nWe would like to thank you for using our services,\nYour order (".$orid.") has been received and your invoice is attached\nTo finalize your order, please read our Terms and Conditions on (https://www.amprotelecom.com/terms-and-conditions/) and agree by replying to this email with 'I agree'\nBest,\nAmProTelecom INC.";

                  // Create the Transport
                  $transport = (new Swift_SmtpTransport('mail.amprotelecom.com', 25))
                          ->setUsername('alialsaffar')
                          ->setPassword('zOIq6dX$@Pq44M')
                  ;

                  // Create the Mailer using your created Transport
                  $mailer = new Swift_Mailer($transport);

                  // Create a message
                  $email_message = (new Swift_Message('AmProTelecom INC. - Your Order'))
                          ->setFrom(['info@amprotelecom.com' => 'AmProTelecom INC.'])
                          ->setTo([$to, 'info@amprotelecom.com'])
                          ->setBody($body)
                          ->attach(Swift_Attachment::fromPath(__DIR__ . "/last_order.pdf"))
                  ;

                  // Send the message
                  $result = $mailer->send($email_message);
                  echo "{\"error\":false,\"message\":\"".$message."\"}";
                  exit();
              } catch (Exception $e) {
                $message=$e;
                echo "{\"error\":true,\"message\":\"".$message."\"}";
                exit();
              }

          } else {
            $message="Error in saving the order. Please contact support team to fix this. Sorry for that";
            echo "{\"error\":true,\"message\":\"".$message."\"}";
            exit();
          }
      }
      $message="Error: missing data. Please contact support team to fix this. Sorry for that";
      echo "{\"error\":true,\"message\":\"".$message."\"}";
      exit();
  }


  echo "{\"error\":true,\"message\":\"".$message."\"}";
  exit();

}
else{
  $message=false;
  try {
    $message=$mGlobalOnePaymentXMLTools->payment($_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"], "P_" . $_POST["merchant_reference"], $_POST["amount"]);

  } catch (\Exception $e) {
    echo "{\"error\":true,\"message\":\"".$e."\"}";
    exit();
  }


  if($message!=true)
  {
    echo "{\"error\":true,\"message\":\"".$message."\"}";
    exit();
  }
  $message=true;
  {

      $creation_date = date("Y-m-d H:i:s");

      $product_id = $_POST["product"];

      if (isset($_POST["full_name"])) {
          if ($customer_id == 0) {

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
                          ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " ?,"
                      . " '0' ,"
                      . " ? ,"
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
                      . " ?);";
              $param_value1=$_POST["full_name"];
              $param_value2=$_POST["address_line_1"];
              $param_value3=$_POST["address_line_2"];
              $param_value4=$_POST["postal_code"];
              $param_value5=$_POST["city"];
              $param_value6=$_POST["email"];
              $param_value7=$_POST["phone"];
              $param_value8=$reseller_id;
              $param_value9=$_POST["note"];

              $stmt2 = $dbTools->getConnection()->prepare($query);

              $stmt2->bind_param('sssssssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4,
                                $param_value5,
                                $param_value6,
                                $param_value7,
                                $param_value8,
                                $param_value9
                                ); // 's' specifies the variable type => 'string'


              $stmt2->execute();

              $customer_id=-1;
                  if($stmt2->insert_id>0){
                    $customer_id=$stmt2->insert_id;
                  }//New customer's ID
          } else {
              // $customer_id = $_POST["customer_id"];
              $is_credit = "no";
              $result_customer = true;
          }

          //if existed customer, set extra_order_recurring_status = pending to modify the recurring amount later by the engine
          $extra_order_recurring_status = "pending";
          if ($secure_card_merchantref==false) { // new Customer
              $extra_order_recurring_status = "";
          }
          $query="SELECT * FROM `products` WHERE `product_id`=?";
          $stmtproduct = $dbTools->getConnection()->prepare($query);

          $param_value=$product_id;
          $stmtproduct->bind_param('s',
                            $param_value
                            ); // 's' specifies the variable type => 'string'


          $stmtproduct->execute();

          $result_product = $stmtproduct->get_result();
          $row_product = $dbTools->fetch_assoc($result_product);

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
                  . "?,"
                  . "?, "
                  . "'sent', "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "?, "
                  . "N'0', "
                  . "?, "
                  . "N''"
                  . ")";

                  $param_value1=$product_id;
                  $param_value2=$creation_date;
                  $param_value3=$reseller_id;
                  $param_value4=$customer_id;
                  $param_value5=$row_product["title"];
                  $param_value6=$row_product["category"];
                  $param_value7=$row_product["subscription_type"];
                  $param_value8=$extra_order_recurring_status ;

                  $stmt_order = $dbTools->getConnection()->prepare($order_query);

                  $stmt_order->bind_param('ssssssss',
                                    $param_value1,
                                    $param_value2,
                                    $param_value3,
                                    $param_value4,
                                    $param_value5,
                                    $param_value6,
                                    $param_value7,
                                    $param_value8
                                    ); // 's' specifies the variable type => 'string'


          $stmt_order->execute();

          $order_result = $stmt_order->get_result();
          $order_id=-1;
              if($stmt_order->insert_id>0){

                $order_id=$stmt_order->insert_id;

              }

          if (isset($_POST["options"])) {


              //$options = json_decode($_POST['options'], true);
              $options = $_POST['options'];
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

                      . "`completion`, "
                      . "`adapter`, "
                      . "`actual_installation_time_from`, "
                      . "`actual_installation_time_to`, "
                      . "`join_type` "
                      . ") VALUES (?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "" . $cancellation_date . ", "
                      . "" . $installation_date_1 . ", "
                      . "?, "
                      . "" . $installation_date_2 . ", "
                      . "?, "
                      . "" . $installation_date_3 . ", "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'', "
                      . "?, "

                      . "'', "
                      . "'', "
                      . "'', "
                      . "'', "
                      . "''"
                      . ")";
              //3- Add order options

              $param_value1=$order_id;
              $param_value2=$options['plan'];
              $param_value3=$options['modem'];
              $param_value4=$options['router'];
              $param_value5=$options['cable_subscriber'];
              $param_value6=$options['current_cable_provider'];
              $param_value7=$options['installation_time_1'];
              $param_value8=$options['installation_time_2'] ;
              $param_value9=$options['installation_time_3'] ;
              $param_value10=$options['modem_serial_number'] ;

              $param_value11=$options['modem_mac_address'];
              $param_value12=$options['additional_service'];
              $param_value13=$options['static_ip'];
              $param_value14=$options['product_price'];
              $param_value15=$options['additional_service_price'];
              $param_value16=$options['static_ip_price'];
              $param_value17=$options['setup_price'];
              $param_value18=$options['modem_price'] ;
              $param_value19=$options['router_price'] ;
              $param_value20=$options['adapter_price'] ;

              $param_value21=$options['current_phone_number'];
              $param_value22=$options['phone_province'];
              $param_value23=$options['remaining_days_price'];
              $param_value24=$options['total_price'];
              $param_value25=$options['gst_tax'];
              $param_value26=$options['qst_tax'];
              $param_value27=$options['modem_id'];
              $param_value28=$options['modem_modem_type'] ;



              $stmt_order_options = $dbTools->getConnection()->prepare($order_option_query);

              $stmt_order_options->bind_param('ssssssssssssssssssssssssssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4,
                                $param_value5,
                                $param_value6,
                                $param_value7,
                                $param_value8,
                                $param_value9,
                                $param_value10,
                                $param_value11,
                                $param_value12,
                                $param_value13,
                                $param_value14,
                                $param_value15,
                                $param_value16,
                                $param_value17,
                                $param_value18,
                                $param_value19,
                                $param_value20,
                                $param_value21,
                                $param_value22,
                                $param_value23,
                                $param_value24,
                                $param_value25,
                                $param_value26,
                                $param_value27,
                                $param_value28
                                ); // 's' specifies the variable type => 'string'


            $stmt_order_options->execute();

            $result_order_options = $stmt_order_options->affected_rows>0?true:false;

              //Assgin the modem to the new customer if it is from inventory
              if ($options['modem'] == "inventory") {
                  $param_value1=$customer_id;
                  $param_value2=$options['modem_id'];
                  $modem_query="update `modems` set `customer_id`=? where `modem_id`=?";
                  $stmt_modem_options = $dbTools->getConnection()->prepare($modem_query);

                  $stmt_modem_options->bind_param('ss',
                                    $param_value1,
                                    $param_value2
                                    ); // 's' specifies the variable type => 'string'


                $stmt_modem_options->execute();

                $result_modem_options = $stmt_modem_options->get_result();

              }
          }

          //if existed customer, do not add new merchantref
          if ($secure_card_merchantref==false) {

              //4- insert Order Merchant Ref
              $param_value1=$_POST["merchantref"];
              $param_value2=$customer_id;
              $param_value3=$order_id;
              $param_value4=$is_credit;
              $query_merchantrefs="INSERT INTO `merchantrefs` ("
                      . "`merchantref`, "
                      . "`customer_id`, "
                      . "`order_id`, "
                      . "`is_credit`, "
                      . "`type`"
                      . ") VALUES ("
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'internet_order'"
                      . ")";
              $stmt_merchantrefs = $dbTools->getConnection()->prepare($query_merchantrefs);


              $stmt_merchantrefs->bind_param('ssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4
                                ); // 's' specifies the variable type => 'string'


              $stmt_merchantrefs->execute();

              $result_merchantrefs = $stmt_merchantrefs->affected_rows>0?true:false;
          } else {
              $param_value1=$_POST["merchantref"];
              $param_value2=$customer_id;
              $param_value3=$order_id;
              $param_value4=$is_credit;

              $query_merchantrefs="INSERT INTO `merchantrefs` ("
                      . "`merchantref`, "
                      . "`customer_id`, "
                      . "`order_id`, "
                      . "`is_credit`, "
                      . "`type`"
                      . ") VALUES ("
                      . "?, "
                      . "?, "
                      . "?, "
                      . "?, "
                      . "'payment'"
                      . ")";
              $stmt_merchantrefs = $dbTools->getConnection()->prepare($query_merchantrefs);

              $stmt_merchantrefs->bind_param('ssss',
                                $param_value1,
                                $param_value2,
                                $param_value3,
                                $param_value4
                                ); // 's' specifies the variable type => 'string'


              $stmt_merchantrefs->execute();
              $result_merchantrefs = $stmt_merchantrefs->affected_rows>0?true:false;

          }

          if ($customer_id>0 && $order_id>0 && $result_order_options && $result_merchantrefs)
          {
              $orid = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
              $message=$order_id . "_" . (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));

              try {
                  $printOrder = new PrintOrder();
                  file_put_contents('last_order.pdf', $printOrder->output($order_id));

                  $to = mysqli_real_escape_string($dbTools->getConnection(),$_POST["email"]);
                  $body = "Dear Customer,\nWe would like to thank you for using our services,\nYour order (".$orid.") has been received and your invoice is attached\nTo finalize your order, please read our Terms and Conditions on (https://www.amprotelecom.com/terms-and-conditions/) and agree by replying to this email with 'I agree'\nBest,\nAmProTelecom INC.";

                  // Create the Transport
                  $transport = (new Swift_SmtpTransport('mail.amprotelecom.com', 25))
                          ->setUsername('alialsaffar')
                          ->setPassword('zOIq6dX$@Pq44M')
                  ;

                  // Create the Mailer using your created Transport
                  $mailer = new Swift_Mailer($transport);

                  // Create a message
                  $email_message = (new Swift_Message('AmProTelecom INC. - Your Order'))
                          ->setFrom(['info@amprotelecom.com' => 'AmProTelecom INC.'])
                          ->setTo([$to, 'info@amprotelecom.com'])
                          ->setBody($body)
                          ->attach(Swift_Attachment::fromPath(__DIR__ . "/last_order.pdf"))
                  ;

                  // Send the message
                  $result = $mailer->send($email_message);
                  echo "{\"error\":false,\"message\":\"".$message."\"}";
                  exit();
              } catch (Exception $e) {
                $message=$e;
                echo "{\"error\":true,\"message\":\"".$message."\"}";
                exit();
              }

          } else {
            $message="Error in saving the order. Please contact support team to fix this. Sorry for that";
            echo "{\"error\":true,\"message\":\"".$message."\"}";
            exit();
          }
      }
      $message="Error: missing data. Please contact support team to fix this. Sorry for that";
      echo "{\"error\":true,\"message\":\"".$message."\"}";
      exit();
  }


  echo "{\"error\":true,\"message\":\"".$message."\"}";
  exit();
}

?>
