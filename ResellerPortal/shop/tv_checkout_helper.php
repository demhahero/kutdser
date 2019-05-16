<?php

class TVCheckoutHelper {
    var $dbToolsReseller;
    var $reseller_id;
    
    function __construct($dbToolsReseller, $reseller_id){
        $this->dbToolsReseller = $dbToolsReseller; 
        $this->reseller_id = $reseller_id;
    }
    function checkoutSetup($params) {
        $product_id = intval($params["product"]);
        $has_discount = $params['has_discount'] === 'yes';
        $free_modem = $params['free_modem'] === 'yes';
        $free_router = $params['free_router'] === 'yes';
        $free_adapter = $params['free_adapter'] === 'yes';
        $free_installation = $params['free_installation'] === 'yes';
        $free_transfer = $params['free_transfer'] === 'yes';

        if (!isset($params["options"]["inventory_modem_price"])) {
            $params["options"]["inventory_modem_price"] = "no";
        }


        $params["options"]["free_modem"] = $params['free_modem'];
        $params["options"]["free_router"] = $params['free_router'];
        $params["options"]["free_adapter"] = $params['free_adapter'];
        $params["options"]["free_installation"] = $params['free_installation'];
        $params["options"]["free_transfer"] = $params['free_transfer'];
        $params["options"]["discount"] = 0;
        $params["options"]["discount_duration"] = "three_months";


//Get start date

        $start_date = new DateTime();
        $start_active_date_string = $start_date->format('Y-m-d');
//Get product info
        $subscription_period_type = "MONTHLY";
        $sql = "SELECT * FROM `products` INNER JOIN `reseller_discounts`
      on `products`.`product_id`=`reseller_discounts`.`product_id`
      WHERE `reseller_discounts`.`reseller_id`='" . $this->reseller_id . "'
      and `products`.`product_id`='" . $product_id . "'";
        $result_product = $this->dbToolsReseller->query($sql);

        if ($result_product->num_rows == 0)
            $result_product = $this->dbToolsReseller->query("SELECT * FROM `products` where `products`.`product_id`='" . $product_id . "'");

        $row_product = "";

        if ($result_product->num_rows > 0) {
            $row_product = $result_product->fetch_assoc();
            if (strpos($row_product["subscription_type"], 'yearly') !== false) { // Check they type of payment (yearly or monthly)
                $subscription_period_type = "YEARLY";
            }
            $product_price = $row_product["price"];

            if ($has_discount && isset($row_product["discount"]) && (int) $row_product["discount"] > 0) {
                $params["options"]["discount"] = $row_product["discount"];
                $params["options"]["discount_duration"] = $row_product["discount_duration"];
                $product_price = (float) $row_product['price'] - ((float) $row_product['price'] * (((float) $row_product['discount'] / 100)));
                $product_price = round($product_price, 2);
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

/////////////// invoice related variables

        $router_item_type = "once";
        $router_duration_price = 0;
        $modem_duration_price = 0;
        $additional_service_duration_price = 0;
        $static_ip_duration_price = 0;
        $product_duration_price = 0;



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
                $product_duration_price = ((($product_price / 12) / $days_in_month) * $remainingDays);
                $additional_service_duration_price = ($additional_service / $days_in_month * $remainingDays);
                $static_ip_duration_price = ($static_ip / $days_in_month * $remainingDays);
            } else { // if monthly payment
                $price_of_remaining_days = ($product_price / $days_in_month) * $remainingDays;
                $price_of_remaining_days += $additional_service / $days_in_month * $remainingDays; // add additional service fees
                $price_of_remaining_days += $static_ip / $days_in_month * $remainingDays; // add static_ip fees
                //// invoice related remaining_days_price
                $product_duration_price = (($product_price / $days_in_month) * $remainingDays);
                $additional_service_duration_price = ($additional_service / $days_in_month * $remainingDays);
                $static_ip_duration_price = ($static_ip / $days_in_month * $remainingDays);

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
        $params["options"]["product_price"] = $product_price;
        $params["options"]["remaining_days_price"] = $price_of_remaining_days;
        $params["options"]["total_price"] = $total_price;
        $params["options"]["qst_tax"] = $qst_tax;
        $params["options"]["gst_tax"] = $gst_tax;

//////////////////////////////// invoice_items values

        $params["invoice_items"][] = ["item_name" => "Product " . $row_product["title"] . " With remaining " . $remainingDays . " days", "item_price" => $product_price, "item_duration_price" => ($product_price + $product_duration_price), "item_type" => "duration"];
        $params["invoice_items"][] = ["item_name" => "Setup price", "item_price" => $installation_transfer_cost, "item_duration_price" => $installation_transfer_cost, "item_type" => "once"];
        $params["invoice_items"][] = ["item_name" => "Modem price", "item_price" => $modem_cost, "item_duration_price" => $modem_cost, "item_type" => "once"];
        $params["invoice_items"][] = ["item_name" => "Router price", "item_price" => $router_cost, "item_duration_price" => ($router_cost + $router_duration_price), "item_type" => $router_item_type];
        $params["invoice_items"][] = ["item_name" => "Additional service price", "item_price" => $additional_service, "item_duration_price" => ($additional_service + $additional_service_duration_price), "item_type" => "duration"];
        $params["invoice_items"][] = ["item_name" => "Static IP price", "item_price" => $static_ip, "item_duration_price" => ($static_ip + $static_ip_duration_price), "item_type" => "duration"];
        $params["invoice_items"][] = ["item_name" => "QST tax", "item_price" => $qst_tax, "item_duration_price" => $qst_tax, "item_type" => "once"];
        $params["invoice_items"][] = ["item_name" => "GST tax", "item_price" => $gst_tax, "item_duration_price" => $gst_tax, "item_type" => "once"];

//Calculate recurring amount
        $subscription_recurring_amount = $product_price;



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
        if (intval($params["customer_id"]) > 0) {
            $secure_card_merchantref_sql = "select * from `merchantrefs` where `customer_id`='" . intval($params["customer_id"]) . "' and `is_credit`='yes'";
            $secure_card_merchantref_result = $this->dbToolsReseller->query($secure_card_merchantref_sql);
            if ($secure_card_merchantref_result->num_rows > 0) {
                $secure_card_merchantref_row = $secure_card_merchantref_result->fetch_assoc();
                $secure_card_merchantref = $secure_card_merchantref_row["merchantref"];
            }
        }
        
        
        
        $result["secure_card_merchantref"] = $secure_card_merchantref;
        $result["start_active_date_string"] = $start_active_date_string;
        $result["subscription_start_date"] = $subscription_start_date;
        $result["subscription_recurring_amount"] = $subscription_recurring_amount;
        $result["merchantref"] = $merchantref;
        $result["subscription_start_date"] = $subscription_start_date;
        $result["subscription_recurring_amount"] = $subscription_recurring_amount;
        $result["subscription_initial_amount"] = $subscription_initial_amount;
        $result["subscription_period_type"] = $subscription_period_type;
        $result["product_id"] = $product_id;
        $result["invoice_items"] = $params["invoice_items"];
        $result["options"] = $params["options"];
        return $result;
    }
}
