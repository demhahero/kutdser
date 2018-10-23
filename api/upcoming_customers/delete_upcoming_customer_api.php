<?php

if (isset($_POST["delete_id"])) {
    include_once "../dbconfig.php";

    $query = "DELETE FROM `upcoming_customers` WHERE `upcoming_customer_id`=?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('s',
                      $_POST["delete_id"]
                      );


    $stmt1->execute();

    $upcoming_customer = $stmt1->get_result();
    if ($stmt1->errno==0) {
      echo "{\"deleted\" :true}";
    }
    else{
      echo "{\"deleted\" :false}";
    }
}
else{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
