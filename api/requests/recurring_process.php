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
    $dateNow=new DateTime();
    $recurring_date=new DateTime($dateNow->format("Y-m-1"));
    $recurring_date->sub(new DateInterval('P1D'));
    recurring($dbTools,$customer_row,$recurring_date->format("Y-m-1"),$recurring_date->format("Y-m-t"));
  }

  $json = json_encode($count);

  echo "{\"customers\" :", $json, ",\"error\" :null}";

?>
