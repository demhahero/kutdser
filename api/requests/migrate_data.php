<?php
include_once "./insert_invoice_function.php";
  include_once "../dbconfig.php";


  $query="SELECT
                `customers`.`customer_id` AS `c_id`,
                `customers`.`reseller_id`,
                `orders`.*,
                `order_options`.*
          FROM `customers`
          INNER JOIN `orders` ON `customers`.`customer_id`=`orders`.`customer_id`
          INNER JOIN `order_options` ON `order_options`.`order_id`=`orders`.`order_id`

          ";
  $stmt1 = $dbTools->getConnection()->prepare($query);
  // $customer_id="1455";
  // $stmt1->bind_param('s',$customer_id);


  $stmt1->execute();

  $getCustomers = $stmt1->get_result();


  $count = 0;
  while ($customer_row = $dbTools->fetch_assoc($getCustomers))
  {
    $count++;
    insertNewOrder($dbTools,$customer_row);
  }

  $json = json_encode($count);

  echo "{\"customers\" :", $json, ",\"error\" :null}";

function insertNewOrder($dbTools,$customer_row)
{
  // $json = json_encode($customer_row);
  //
  // echo "{\"customer\" :", $json, ",\"error\" :null}";
  // exit();
  $product_price = $customer_row["product_price"];

  $free_modem = $customer_row['free_modem']==='yes';
  $free_router = $customer_row['free_router']==='yes';
  $free_adapter = $customer_row['free_adapter']==='yes';
  $free_installation = $customer_row['free_installation']==='yes';
  $free_transfer = $customer_row['free_transfer']==='yes';

  if(!isset($customer_row["inventory_modem_price"]))
  {
    $customer_row["inventory_modem_price"]="no";
  }
  $customer_row["free_modem"] = $customer_row['free_modem'];
  $customer_row["free_router"] = $customer_row['free_router'];
  $customer_row["free_adapter"] = $customer_row['free_adapter'];
  $customer_row["free_installation"] = $customer_row['free_installation'];
  $customer_row["free_transfer"] = $customer_row['free_transfer'];
  $customer_row["discount"] = 0;
  $customer_row["discount_duration"] = "three_months";

  //if user selects phone change product type to phone
  $product_type = $customer_row["product_category"];

  $subscription_recurring_amount=0;

  //If it is internet product
  if ($product_type == "internet") {

      //Get start date
      if ($customer_row["cable_subscriber"] == "yes") {
          $cancellation_date = $customer_row["cancellation_date"];
          $start_date = new DateTime($cancellation_date);
      } else {
          $installation_date_1 = $customer_row["installation_date_1"];
          $start_date = new DateTime($installation_date_1);
      }
      $start_active_date_string=$start_date->format('Y-m-d');
      //Get product info
      $subscription_period_type = $customer_row["product_subscription_type"];

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

      /////////////// invoice related variables

      $router_item_type="once";
      $router_duration_price=0;
      $modem_duration_price=0;
      $additional_service_duration_price=0;
      $static_ip_duration_price=0;
      $product_duration_price=0;



      //If rent modem
      //If rent modem
      if ($customer_row["inventory_modem_price"] == "yes") {
          $modem_cost = 59.90;

          //Deposit has no tax
          //$value_has_no_tax = $modem_cost;
      }

      if ($customer_row["modem"] == "rent") {
          $modem_cost = 59.90;

          if( $free_modem)
          $modem_cost=0;
          //Deposit has no tax
          //$value_has_no_tax = $modem_cost;
      }
      if ($customer_row["modem"] == "buy") {
          $modem_cost = 200;
      }

      if ($customer_row["router"] == "rent") { //If rent router
          $router_cost = 2.90;
          if( $free_router)
            $router_cost=0;
          $router_item_type="duration";
      }
      else if($customer_row["router"] == "rent_hap_lite") { //If rent hap lite router
        $router_cost = 4.90;
        $router_item_type="duration";
      } else if ($customer_row["router"] == "buy_hap_ac_lite") { //if buy hap ac lite
          $router_cost = 74.00;
      } else if ($customer_row["router"] == "buy_hap_mini") { //if buy hap mini
          $router_cost = 39.90;
      }


  //Check additional service
      if (isset($customer_row["additional_service"]) && $customer_row["additional_service"] == "yes") {
          $additional_service = 5;
      }
  //Check static ip
      if (isset($customer_row["static_ip"]) && $customer_row["static_ip"] == "yes") {
          $static_ip = 20;
      }

  //if NOT yearly payment, check monthly (no contract) for transfer or installation fees.
      //If user selects 60 or 120, then charge him the setup fees anyways.
      if ($subscription_period_type == "MONTHLY") {
          if ($customer_row["plan"] == "monthly"
              || $product_id == 416
              || $product_id == 418) {
              if ($customer_row["cable_subscriber"] == "yes")
              {
                $installation_transfer_cost = 19.90;
                if($free_transfer)
                $installation_transfer_cost=0;
              }
              else
              {
                $installation_transfer_cost = 60.00;
                if($free_installation)
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

              //// invoice related remaining_days_price
              $product_duration_price=((($product_price / 12) / $days_in_month) * $remainingDays);
              $additional_service_duration_price=($additional_service / $days_in_month * $remainingDays);
              $static_ip_duration_price= ($static_ip / $days_in_month * $remainingDays);
          } else { // if monthly payment
              $price_of_remaining_days = ($product_price / $days_in_month) * $remainingDays;
              $price_of_remaining_days += $additional_service / $days_in_month * $remainingDays; // add additional service fees
              $price_of_remaining_days += $static_ip / $days_in_month * $remainingDays; // add static_ip fees
              //// invoice related remaining_days_price
              $product_duration_price=(($product_price / $days_in_month) * $remainingDays);
              $additional_service_duration_price=($additional_service / $days_in_month * $remainingDays);
              $static_ip_duration_price= ($static_ip / $days_in_month * $remainingDays);

              if ($customer_row["router"] == "rent") { //if rent router, add rent cost of the remaining day
                  $price_of_remaining_days += ($router_cost / $days_in_month) * $remainingDays;
                  $router_duration_price=(($router_cost / $days_in_month) * $remainingDays);// invoice
              }
              else if ($customer_row["router"] == "rent_hap_lite") { //if rent hap lite router, add rent cost of the remaining day
                  $price_of_remaining_days += ($router_cost / $days_in_month) * $remainingDays;
                  $router_duration_price=(($router_cost / $days_in_month) * $remainingDays);// invoice

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
      $customer_row["product_price"] = $product_price;
      $customer_row["remaining_days_price"] = $price_of_remaining_days;
      $customer_row["setup_price"] = $installation_transfer_cost;
      $customer_row["modem_price"] = $modem_cost;
      $customer_row["router_price"] = $router_cost;
      $customer_row["additional_service_price"] = $additional_service;
      $customer_row["static_ip_price"] = $static_ip;
      $customer_row["total_price"] = $total_price;
      $customer_row["qst_tax"] = $qst_tax;
      $customer_row["gst_tax"] = $gst_tax;

      //////////////////////////////// invoice_items values

      $customer_row["invoice_items"][]=["item_name"=>"Product ".$customer_row["product_title"]." With remaining ".$remainingDays." days","item_price"=> $product_price,"item_duration_price"=>($product_price+$product_duration_price),"item_type"=>"duration"];
      $customer_row["invoice_items"][]=["item_name"=>"Setup price","item_price"=> $installation_transfer_cost,"item_duration_price"=>$installation_transfer_cost,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"Modem price","item_price"=> $modem_cost,"item_duration_price"=>$modem_cost,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"Router price","item_price"=> $router_cost,"item_duration_price"=>($router_cost+$router_duration_price),"item_type"=>$router_item_type];
      $customer_row["invoice_items"][]=["item_name"=>"Additional service price","item_price"=> $additional_service,"item_duration_price"=>($additional_service+$additional_service_duration_price),"item_type"=>"duration"];
      $customer_row["invoice_items"][]=["item_name"=>"Static IP price","item_price"=> $static_ip,"item_duration_price"=>($static_ip+$static_ip_duration_price),"item_type"=>"duration"];
      $customer_row["invoice_items"][]=["item_name"=>"QST tax","item_price"=> $qst_tax,"item_duration_price"=>$qst_tax,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"GST tax","item_price"=> $gst_tax,"item_duration_price"=>$gst_tax,"item_type"=>"once"];

      //Calculate recurring amount
      $subscription_recurring_amount = $product_price + $additional_service + $static_ip;
      if ($customer_row["router"] == "rent") { //If rent router, add $2.90 on the recurring amount

          if(!($free_router))
          $subscription_recurring_amount += 2.90;
      }
      else if($customer_row["router"] == "rent_hap_lite") { //If rent hap lite router, add $4.90 on the recurring amount
        $subscription_recurring_amount +=4.90;
      }
  } else if ($product_type == "phone") {
      //Get start date
      $start_date = new DateTime($customer_row["creation_date"]);
      $start_active_date_string=$start_date->format('Y-m-d');
      //Get product info

      $subscription_period_type = $customer_row["product_subscription_type"];

      $total_price = 0;
      $price_of_remaining_days = 0;
      $transfer_cost = 0;
      $adapter_cost = 0;
      $remainingDays = 0; //Remaining days in the month
      $value_has_no_tax = 0; // Exclude items that have no tax such as deposits
      $gst_tax = 0;
      $qst_tax = 0;
      //////////// invoice related variables
      $product_duration_price=0;

      //If rent modem
      if ($customer_row["adapter"] == "buy_Cisco_SPA112") {
          $adapter_cost = 59.90;
          if( $free_adapter)
          $adapter_cost=0;
      }
      //NOTICE: Have to be changed later
      //$adapter_cost = 0;

      //If transfer
      if (isset($customer_row["you_have_phone_number"]) && $customer_row["you_have_phone_number"] == "yes") {
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
              $product_duration_price=((($product_price / 12) / $days_in_month) * $remainingDays);
          } else { // if monthly payment
              $price_of_remaining_days = ($product_price / $days_in_month) * $remainingDays;
              $product_duration_price=(($product_price / $days_in_month) * $remainingDays);
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
      $customer_row["product_price"] = $product_price;
      $customer_row["remaining_days_price"] = $price_of_remaining_days;
      $customer_row["setup_price"] = $transfer_cost;
      $customer_row["adapter_price"] = $adapter_cost;
      $customer_row["total_price"] = $total_price;
      $customer_row["qst_tax"] = $qst_tax;
      $customer_row["gst_tax"] = $gst_tax;

      //////////////////////////////// invoice_items values


      $customer_row["invoice_items"][]=["item_name"=>"Product ".$customer_row["product_title"]." With remaining ".$remainingDays." days","item_price"=>$product_price,"item_duration_price"=>($product_price+$product_duration_price),"item_type"=>"duration"];
      $customer_row["invoice_items"][]=["item_name"=>"Setup price","item_price"=> $transfer_cost,"item_duration_price"=> $transfer_cost,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"adapter_price","item_price"=> $adapter_cost,"item_duration_price"=> $adapter_cost,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"QST tax","item_price"=> $qst_tax,"item_duration_price"=> $qst_tax,"item_type"=>"once"];
      $customer_row["invoice_items"][]=["item_name"=>"GST tax","item_price"=> $gst_tax,"item_duration_price"=> $gst_tax,"item_type"=>"once"];

      $subscription_recurring_amount = $product_price;
  }
  else{
    return;
  }

  $subscription_recurring_amount += $subscription_recurring_amount * 0.09975 + $subscription_recurring_amount * 0.05; // Add tax for recurring amount
  $subscription_recurring_amount = number_format((float) $subscription_recurring_amount, 2, '.', '');

  //Initial amount is same as total_price
  $subscription_initial_amount = number_format((float) $total_price, 2, '.', '');

  //Find out 1st recurring date
  $subscription_start_date = "";
  if ($subscription_period_type == "YEARLY" || $subscription_period_type == "yearly") { //If yearly payment
      if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
          $start_date->add(new DateInterval('P1Y'));
          $subscription_start_date = $start_date->format('d-m-Y');
      } else { // if not 1st day, add 1 year plus one month
          $start_date->add(new DateInterval('P1Y'));
          $start_date->add(new DateInterval('P1M'));
          $start_date->modify('first day of this month');
          $subscription_start_date = $start_date->format('d-m-Y');
      }

      $interval = DateInterval::createFromDateString('1 year');


  } else { // if payment monthly
      if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
          $start_date->add(new DateInterval('P1M'));
          $subscription_start_date = $start_date->format('d-m-Y');
      } else { // if not 1st day, add 1 year plus one month
          $start_date->add(new DateInterval('P2M'));
          $start_date->modify('first day of this month');
          $subscription_start_date = $start_date->format('d-m-Y');
      }

      $interval = DateInterval::createFromDateString('1 month');

  }

  $recurring_date = DateTime::createFromFormat('d-m-Y', $subscription_start_date);

  $start    = (new DateTime($recurring_date->format("Y-m-d")))->modify('first day of this month');
  $end      = (new DateTime())->modify('first day of next month');
  $period   = new DatePeriod($start, $interval, $end);
  $previous_date=$start_active_date_string;



  $recurring_date->sub(new DateInterval('P1D'));
  $invoice_query="INSERT INTO `invoices`(`customer_id`,`valid_date_from`,`valid_date_to`,`order_id`,`product_price`,`reseller_id`) VALUES (N'".$customer_row["c_id"]."',N'".$start_active_date_string."',N'".$recurring_date->format('Y-m-d')."',N'".$customer_row["order_id"]."',N'".$customer_row["product_price"]."',N'".$customer_row["reseller_id"]."')";
  $invoice_id=-1;
  if($dbTools->query($invoice_query)===TRUE)
  {
    $last_insert_id=mysqli_fetch_assoc( $dbTools->query("SELECT last_insert_id() as 'invoice_id'"));
    $invoice_id=$last_insert_id["invoice_id"];
  }
  else {
    return FALSE;
  }

  $invoice_item_query="INSERT INTO `invoice_items`( `invoice_id`, `item_name`, `item_price`, `item_duration_price`,`item_type`) VALUES ";

  foreach ($customer_row["invoice_items"] as $key => $value)
  {
    $invoice_item_query.="(N'".$invoice_id."',N'".$value["item_name"]."',N'".$value["item_price"]."',N'".$value["item_duration_price"]."',N'".$value["item_type"]."'),";
  }
  $invoice_item_query=rtrim($invoice_item_query, ",");
  if($dbTools->query($invoice_item_query)===TRUE){

    foreach ($period as $dt) {

      $sql="SELECT `customers`.`customer_id`,`requests`.* FROM `requests` INNER JOIN `orders` ON `orders`.`order_id`=`requests`.`order_id` INNER JOIN `customers` ON `customers`.`customer_id`=`orders`.`customer_id`
            WHERE `customers`.`customer_id`={$customer_row["c_id"]}  AND `verdict`='approve' AND `action_on_date`<'{$dt->format("Y-m-d")}' AND `action_on_date` >= '{$previous_date}'
            ORDER BY `requests`.`action_on_date` ASC";

      $requests=$dbTools->query($sql);
      while ($request = $dbTools->fetch_assoc($requests))
      {
        insertInvoice($dbTools,$request);
      }
      $recurring_date=new DateTime($dt->format("Y-m-d"));
      $recurring_date->sub(new DateInterval('P1D'));
      recurring($dbTools,$customer_row,$previous_date,$recurring_date->format("Y-m-d"));
      $previous_date= $dt->format("Y-m-d");
    }
  }
  return FALSE;
}

?>
