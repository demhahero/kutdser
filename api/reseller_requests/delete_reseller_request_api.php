<?php

if (isset($_POST["delete_id"]) && isset($_POST["post_action"]) && $_POST["post_action"]=="delete_reseller_request_item") {
    include_once "../dbconfig.php";

    $query = "DELETE FROM `reseller_request_items` WHERE `reseller_request_item_id`=?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('s',
                      $_POST["delete_id"]
                      );


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
      echo "{\"deleted\" :true}";
    }
    else{
      echo "{\"deleted\" :false}";
    }
}
else if (isset($_POST["delete_id"]) && isset($_POST["post_action"]) && $_POST["post_action"]=="delete_reseller_request") {
    include_once "../dbconfig.php";

    $query = "DELETE FROM `reseller_requests` WHERE `reseller_request_id`=? AND `reseller_id`=?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ss',
                      $_POST["delete_id"],
                      $reseller_id
                      );


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
      $query = "DELETE FROM `reseller_request_items` WHERE `reseller_request_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('s',
                        $_POST["delete_id"]
                        );


      $stmt1->execute();

      $modem = $stmt1->get_result();
      if ($stmt1->errno==0) {
        echo "{\"deleted\" :true}";
      }
      else{
        echo "{\"deleted\" :false}";
      }
    }
    else{
      echo "{\"deleted\" :false}";
    }
}
else{
  echo "{\"deleted\" :false,\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
