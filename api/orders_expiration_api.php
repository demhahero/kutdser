<?php

include_once "dbconfig.php";

function write_log($log_msg)
{
    $log_filename = "log";
    if (!file_exists($log_filename))
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}


$orders = $dbTools->order_expiration_date();
$count=0;
foreach ($orders as $key => $orderChild) {

  $query = "INSERT INTO `order_expiration_notify`
  (`order_id`, `year_no`, `expiration_date`, `seen`)
   VALUES
   (".$orderChild['order_id'].",".$orderChild['year_no'].",N'".$orderChild['end_date']."','no')";
  $order_expiration = $dbTools->query($query);

  if ($order_expiration) {
      $count=$count+1;
      //echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
  } else {
    //print_r($dbTools->getConnection());
      echo "{\"inserted\" :\"false\",\"error\" :\"".$dbTools->getConnection()->error."\"}";
  }
}
write_log("from ".sizeof($orders)." orders expire soon, we record ".$count." of them successfully");
$json = json_encode($orders);
echo "{\"insertedCount\" :" ,$count , ",\"orders\" :" ,$json , "}";


?>
