<?php
include_once "./insert_invoice_function.php";
include "../db_credentials.php";
include "../tools/DBTools.php";
$dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);


  $query="SELECT
                `customers`.`customer_id` AS `c_id`,
                `customers`.`reseller_id` AS `r_id`,
                `orders`.*,
                `order_options`.*,
                `customer_active_status`.`start_active_date`
          FROM `customers`
          INNER JOIN `orders` ON `customers`.`customer_id`=`orders`.`customer_id`
          INNER JOIN `order_options` ON `order_options`.`order_id`=`orders`.`order_id`
          INNER JOIN `customer_active_status` ON `orders`.`order_id`=`customer_active_status`.`order_id`

          ";
  $stmt1 = $dbTools->getConnection()->prepare($query);
  // $customer_id="1455";
  // $stmt1->bind_param('s',$customer_id);

  $stmt1->execute();

  $getCustomers = $stmt1->get_result();


  $count = 0;
  while ($customer_row = $dbTools->fetch_assoc($getCustomers))
  {
    $start_date=new DateTime($customer_row["start_active_date"]);
    //Find out 1st recurring date
    $subscription_start_date = "";
    if ($customer_row["product_subscription_type"] == "YEARLY" || $customer_row["product_subscription_type"] == "yearly") { //If yearly payment
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



    $end      = (new DateTime())->modify('first day of next month');
    $end->add($interval);


    $start    = (new DateTime($recurring_date->format("Y-m-d")))->modify('first day of this month');

    $period   = new DatePeriod($start, $interval, $end);
    $previous_date=$customer_row["start_active_date"];



    $recurring_date->sub(new DateInterval('P1D'));
    $date_from="";
    $date_to="";
      foreach ($period as $dt) {


        $recurring_date=new DateTime($dt->format("Y-m-d"));
        $recurring_date->sub(new DateInterval('P1D'));
        $dateNow=new DateTime();
        if($recurring_date<=$dateNow)
        {
          $date_from=$previous_date;
          $date_to=$recurring_date->format("Y-m-d");

        }

        $previous_date= $dt->format("Y-m-d");
      }

    $count++;
    $dateNow=new DateTime();
    $recurring_date=new DateTime($dateNow->format("Y-m-1"));
    $recurring_date->sub(new DateInterval('P1D'));
    if($recurring_date->format("Y-m-d")==$date_to)
    {
      $customer_row["reseller_id"]=$customer_row["r_id"];
      recurring($dbTools,$customer_row,$recurring_date->format("Y-m-1"),$recurring_date->format("Y-m-t"));
    }
}

  $json = json_encode($count);

  echo "{\"customers\" :", $json, ",\"error\" :null}";

?>
