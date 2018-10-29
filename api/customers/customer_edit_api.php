<?php

if (isset($_GET["customer_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "../dbconfig.php";

    $customer_query = "SELECT *
     FROM `customers`
      WHERE `customer_id`=?";

      $stmt1 = $dbTools->getConnection()->prepare($customer_query);

      $param_value=$_GET["customer_id"];
      $stmt1->bind_param('s',
                        $param_value
                        ); // 's' specifies the variable type => 'string'


      $stmt1->execute();

      $result1 = $stmt1->get_result();
      $result = $dbTools->fetch_assoc($result1);
      if($result)
      {
        $json = json_encode($result);
        echo "{\"customer\" :", $json, "}";
      }


} else if (isset($_POST["customer_id"])) {

    include_once "../dbconfig.php";
    $address_line_1=$_POST["address_line_1"];
    $address_line_2=$_POST["address_line_2"];
    $postal_code=$_POST["postal_code"];
    $city=$_POST["city"];
    $customer_id=$_POST["customer_id"];

    $query = "UPDATE `customers` SET `address_line_1`=?,"
            . "`address_line_2`=?,"
            . "`postal_code`=?,"
            . "`city`=? WHERE `customer_id`=?";

      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('sssss',
                        $address_line_1,
                        $address_line_2,
                        $postal_code,
                        $city,
                        $customer_id);


    $stmt1->execute();

    $modem = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
