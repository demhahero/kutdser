<?php

if(isset($_POST["is_ours"]) && isset($_POST["is_sold"])) {

    include_once "../dbconfig.php";
    $serial_number=$_POST["serial_number"];
    $type=$_POST["type"];
    $reseller_id=$_POST["reseller_id"];
    $customer_id=$_POST["customer_id"];
    $is_ours=$_POST["is_ours"];
    $is_sold=$_POST["is_sold"];

    $query = "INSERT INTO `routers`(
            `serial_number`,
            `type`,
            `reseller_id`,
            `customer_id`,
            `is_ours`,
            `is_sold`) VALUES (?,?,?,?,?,?)";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ssssss',
                      $serial_number,
                      $type,
                      $reseller_id,
                      $customer_id,
                      $is_ours,
                      $is_sold);


    $stmt1->execute();

    $router = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {

        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
else{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
