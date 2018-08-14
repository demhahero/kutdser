<?php

if (isset($_GET["customer_id"]) /* && isset($_GET["action_on_date"]) */) {
    include_once "dbconfig.php";

    $customer_query = $dbTools->query("SELECT *
     FROM `customers`
      WHERE `customer_id`='" . $_GET["customer_id"] . "'");
    $customer = $dbTools->fetch_assoc($customer_query);
    $json = json_encode($customer);

    $reseller_query = $dbTools->query("SELECT customer_id,full_name
     FROM `customers`
      WHERE `customer_id` !='" . $_GET["customer_id"] . "'");
      $resellers=array();
      while ($reseller = $dbTools->fetch_assoc($reseller_query)) {
        array_push($resellers,$reseller);
      }
    $json_resellers = json_encode($resellers);
    echo "{\"customer\" :", $json, ",\"resellers\" :", $json_resellers, "}";
} else if (isset($_POST["customer_id"]) && isset($_POST["full_name"])) {

    include_once "dbconfig.php";

    $query = "update `customers` set "
            . "`full_name`=N'".$_POST["full_name"]."',"
            . "`parent_reseller`=N'".$_POST["parent_reseller"]."',"
            . "`email`=N'".$_POST["email"]."',"
            . "`phone`=N'".$_POST["phone"]."',"
            . "`address_line_1`='".$_POST["address_line_1"]."',"
            . "`address_line_2`='".$_POST["address_line_2"]."',"
            . "`postal_code`='".$_POST["postal_code"]."',"
            . "`reseller_commission_percentage`=N'".$_POST["reseller_commission_percentage"]."',"
            . "`city`='".$_POST["city"]."' where `customer_id`='".$_POST["customer_id"]."'";

    $customer_log = $dbTools->query($query);

    if ($customer_log) {
        $json = json_encode($customer_log);
        echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
} else if (isset($_POST["customer_id"]) && isset($_POST["username"])) {
  include_once "dbconfig.php";

  $username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
  if ($username != FALSE) {

      $reseller_result = $dbTools->query("select * from `customers` where `username`='" . $username . "'");
      $username_exist=FALSE;
      while ($row  = $dbTools->fetch_assoc($reseller_result)) {
        $username_exist=TRUE;
          if (password_verify($_POST["password"], $row["password"])) {
            if(isset($_POST["new_password"]) && strlen($_POST["new_password"])>4){
              $password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
              $query = "update `customers` set "
                      . "`password`=N'".$password."' where `customer_id`='".$_POST["customer_id"]."'";

              $customer_password = $dbTools->query($query);

              if ($customer_password) {
                  $json = json_encode($customer_password);
                  echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
              } else {
                  echo "{\"inserted\" :\"false\",\"error\" :\"failed to update password\"}";
              }
            }
            else {
                echo "{\"inserted\" :\"false\",\"error\" :\" please enter new password\"}";
            }
          }
          else {
              echo "{\"inserted\" :\"false\",\"error\" :\" current password is wrong\"}";
          }
      }
      if(!$username_exist)
      {
        echo "{\"inserted\" :\"false\",\"error\" :\" username is wrong\"}";
      }
  }
  else {
      echo "{\"inserted\" :\"false\",\"error\" :\" please enter username username\"}";
  }


}
