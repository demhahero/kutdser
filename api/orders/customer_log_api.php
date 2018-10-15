<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (isset($_GET["customer_id"])) {
    include_once "../dbconfig.php";

    $customer_id = $_GET["customer_id"];

    $query = "SELECT
     `customer_log_id`, customer_log.`customer_id`, `customer_log`.`admin_id`, `admins`.`username`, `admins`.`admin_id`, `log_date`, customer_log.`type`, customer_log.`note`, customer_log.`completion`
     ,customers.`full_name`
     FROM `customer_log`
     INNER JOIN customers on customer_log.customer_id=customers.customer_id
     left JOIN admins on customer_log.admin_id = admins.admin_id
      WHERE customer_log.customer_id=? ORDER BY log_date DESC";

      $stmt1 = $dbTools->getConnection()->prepare($query);


      $stmt1->bind_param('s',
                        $customer_id
                        ); // 's' specifies the variable type => 'string'


      $stmt1->execute();

      $result1 = $stmt1->get_result();

      $customer_logs=[];
      while($result = $dbTools->fetch_assoc($result1))
      {
        array_push($customer_logs,$result);
      }

      $json = json_encode($customer_logs);
      echo "{\"customer_logs\" :", $json, "}";
} else if (isset($_POST["customer_id"])) {

    include_once "../dbconfig.php";



    $PostFields = array(
        "customer_id" => "",
        "log_date" => "",
        "type" => "",
        "note" => "",
        "completion" => "",
        "admin_id" => "",
    );



    foreach ($PostFields as $key => $value) {
        if (!isset($_POST[$key]) && $key !== "note")
        {
            echo "{\"inserted\" :false,\"error\" :\"error: not all values sent in POST\"}";
            exit();
        }
    }
    $customer_id=$_POST["customer_id"];
    $log_date=$_POST["log_date"];
    $type=$_POST["type"];
    $note=$_POST["note"];
    $completion=$_POST["completion"];
    $admin_id=$_POST["admin_id"];

    $query = "INSERT INTO `customer_log`(
            `customer_id`,
            `log_date`,
            `type`,
            `note`,
            `completion`,
            `admin_id`) VALUES (?,?,?,?,?,?)";

    $customer_log = $dbTools->query($query);
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ssssss',
                      $customer_id,
                      $log_date,
                      $type,
                      $note,
                      $completion,
                      $admin_id);


    $stmt1->execute();

    $customer_log = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
      print_r($stmt1);
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
