<?php

if (isset($_GET["modems"])) {
    include_once "../dbconfig.php";

    $modem_query = $dbTools->query("SELECT * FROM `customers` WHERE `is_reseller` = '1'");


      $modems=array();
      while ($modem = $dbTools->fetch_assoc($modem_query)) {
        array_push($modems,$modem);
      }
    $json_modems = json_encode($modems);
    echo "{\"modems\" :", $json_modems, "}";
} else if (isset($_POST["is_ours"])) {

    include_once "../dbconfig.php";
    $mac_address=$_POST["mac_address"];
    $serial_number=$_POST["serial_number"];
    $type=$_POST["type"];
    $reseller_id=$_POST["reseller_id"];
    $customer_id=$_POST["customer_id"];
    $is_ours=$_POST["is_ours"];

    $query = "INSERT INTO `modems`(
            `mac_address`,
            `serial_number`,
            `type`,
            `reseller_id`,
            `customer_id`,
            `is_ours`) VALUES (?,?,?,?,?,?)";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ssssss',
                      $mac_address,
                      $serial_number,
                      $type,
                      $reseller_id,
                      $customer_id,
                      $is_ours);


    $stmt1->execute();

    $modem = $stmt1->get_result();
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
