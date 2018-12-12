<?php

if (isset($_POST["action_on_date"]) && isset($_POST["action"])) {

    include_once "../dbconfig.php";

    $action=$_POST["action"];
    $action_on_date = new DateTime($_POST["action_on_date"]);
    $action_on_date_string=$action_on_date->format('Y-m-d');
    $creation_date=new DateTime();
    $creation_date_string=$creation_date->format('Y-m-d');
    $query = "INSERT INTO `reseller_requests`
    ( `reseller_id`, `creation_date`, `action`, `action_on_date`)
     VALUES (?,?,?,?)";



    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ssss',
                      $reseller_id,
                      $creation_date_string,
                      $action,
                      $action_on_date_string);


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
      $reseller_request_id=$stmt1->insert_id;
      foreach ($_POST["reseller_request_items"] as $key => $value) {
        // code...

        $query = "INSERT INTO `reseller_request_items`
                    ( `reseller_request_id`,`note`, `modem_mac_address`, `modem_serial_number`, `modem_type`)
                    VALUES
                    (?,?,?,?,?)";



        $stmt2 = $dbTools->getConnection()->prepare($query);

        $stmt2->bind_param('sssss',
                          $reseller_request_id,
                          $value["note"],
                          $value["modem_mac_address"],
                          $value["modem_serial_number"],
                          $value["modem_type"]);


        $stmt2->execute();

        $modem = $stmt2->get_result();
      }
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
