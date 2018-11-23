<?php

if (isset($_POST["reseller_id"]) && isset($_POST["action"])) {

    include_once "../dbconfig.php";
    $mac_address=$_POST["modem_mac_address"];
    $serial_number=$_POST["modem_serial_number"];
    $type=$_POST["modem_type"];
    $reseller_id=$_POST["reseller_id"];
    $action=$_POST["action"];

    $action_on_date = new DateTime($_POST["action_on_date"]);
    $action_on_date_string=$action_on_date->format('Y-m-d');
    $note=$_POST["note"];

    $creation_date=new DateTime();
    $creation_date_string=$creation_date->format('Y-m-d');
    $query = "INSERT INTO `reseller_requests`
    ( `reseller_id`, `creation_date`, `action`, `action_on_date`, `note`, `modem_mac_address`, `modem_serial_number`, `modem_type`)
     VALUES (?,?,?,?,?,?,?,?)";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ssssssss',
                      $reseller_id,
                      $creation_date_string,
                      $action,
                      $action_on_date_string,
                      $note,
                      $mac_address,
                      $serial_number,
                      $type);


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
      print_r($stmt1);
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
else{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
