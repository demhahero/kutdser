<?php
if (isset($_POST["post_action"]) && $_POST["post_action"]=="customers_transfer")
{
    include_once "../dbconfig.php";
    $from_reseller=$_POST["from_reseller"];
    $to_reseller=$_POST["to_reseller"];

    $query = "UPDATE `customers` SET `reseller_id`=?"
            . " WHERE `reseller_id`=?";

      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('ss',
                        $to_reseller,
                        $from_reseller);


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
else {
  // code...
  echo "{\"inserted\" :\"false\",\"error\" :\"Not authorized\"}";
}
