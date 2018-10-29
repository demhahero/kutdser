<?php

if (isset($_GET["customer_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "../dbconfig.php";

    $stmt = $dbTools->getConnection()->prepare('SELECT *
     FROM `customers`
      WHERE `customer_id`= ?');
    $stmt->bind_param('s', $_GET["customer_id"]); // 's' specifies the variable type => 'string'

    $stmt->execute();

    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();


    $json = json_encode($customer);

    $query="SELECT `customer_id`,`full_name`
            FROM `customers`
            WHERE `customer_id` != ? ";

      $resellers=array();
      $stmt1 = $dbTools->getConnection()->prepare($query);
      $stmt1->bind_param('s', $_GET["customer_id"]); // 's' specifies the variable type => 'string'

      $stmt1->execute();

      $result1 = $stmt1->get_result();
      while($reseller = $result1->fetch_assoc())
      {
        array_push($resellers,$reseller);
      }

    $json_resellers = json_encode($resellers);
    echo "{\"customer\" :", $json, ",\"resellers\" :", $json_resellers, "}";
} else if (isset($_POST["customer_id"]) && isset($_POST["full_name"])) {

    include_once "../dbconfig.php";

    $full_name=$_POST["full_name"];
    $parent_reseller=$_POST["parent_reseller"];
    $email=$_POST["email"];
    $phone=$_POST["phone"];
    $address_line_1=$_POST["address_line_1"];
    $address_line_2=$_POST["address_line_2"];
    $postal_code=$_POST["postal_code"];
    $reseller_commission_percentage=$_POST["reseller_commission_percentage"];
    $city=$_POST["city"];
    $customer_id=$_POST["customer_id"];

    $query = "UPDATE `customers` SET "
            . "`full_name`= ? ,"
            . "`parent_reseller`= ? ,"
            . "`email`= ? ,"
            . "`phone`= ? ,"
            . "`address_line_1`= ? ,"
            . "`address_line_2`= ? ,"
            . "`postal_code`= ? ,"
            . "`reseller_commission_percentage`= ? ,"
            . "`city`= ?  WHERE `customer_id`= ?";

            $stmt1 = $dbTools->getConnection()->prepare($query);
            $stmt1->bind_param('ssssssssss',
                                $full_name,
                                $parent_reseller,
                                $email,
                                $phone,
                                $address_line_1,
                                $address_line_2,
                                $postal_code,
                                $reseller_commission_percentage,
                                $city,
                                $customer_id
                              ); // 's' specifies the variable type => 'string'
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
} else if (isset($_POST["customer_id"]) && isset($_POST["username"])) {
  include_once "../dbconfig.php";

  $username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
  if ($username != FALSE) {

      $query="SELECT * FROM `customers` WHERE `username`= ?";

      $stmt1 = $dbTools->getConnection()->prepare($query);
      $stmt1->bind_param('s',
                          $username
                        ); // 's' specifies the variable type => 'string'
      $stmt1->execute();
      $result1 = $stmt1->get_result();

      $username_exist=FALSE;
      while ($row  = $result1->fetch_assoc()) {
        $username_exist=TRUE;

            if(isset($_POST["new_password"]) && strlen($_POST["new_password"])>4){
              $password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
              $query = "UPDATE `customers` SET "
                      . "`password`=? WHERE `customer_id`= ?";

              $customer_password = $dbTools->query($query);

              $stmt2 = $dbTools->getConnection()->prepare($query);
              $stmt2->bind_param('ss',
                                  $password,
                                  $_POST["customer_id"]
                                ); // 's' specifies the variable type => 'string'
              $stmt2->execute();
              $result2 = $stmt2->get_result();

              if ($stmt2->errno==0) {

                  echo "{\"inserted\" :true,\"error\" :\"null\"}";
              } else {
                  echo "{\"inserted\" :\"false\",\"error\" :\"failed to update password\"}";
              }
            }
            else {
                echo "{\"inserted\" :\"false\",\"error\" :\" please enter new password\"}";
            }


      }
      if(!$username_exist)
      {
        echo "{\"inserted\" :\"false\",\"error\" :\" username is wrong\"}";
      }
  }
  else {
      echo "{\"inserted\" :\"false\",\"error\" :\" please enter username \"}";
  }


}
