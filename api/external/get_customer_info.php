<?php
include "./init.php";

if(isset($_POST["action"]))
{
  if($_POST["action"]==="login")
  {

    $query="SELECT * FROM `customers` WHERE `customer_id`=?";
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$customer_id;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $customer_result = $stmt1->get_result();
    $customer_row = $dbTools->fetch_assoc($customer_result);
    if($customer_row){
      $json = json_encode($customer_row);
      echo "{\"customer_info\":", $json
        , ",\"message\":\"login success\""
        , ",\"error\":false}";
    }
    else {
      echo "{\"customer_info\":[]"
        , ",\"message\":\"login failed: customer info not found\""
        , ",\"error\":true}";
    }

  }// end login
}
else {
  echo "{\"customer_info\":[]"
    , ",\"message\":\"login failed: you are not authorized\""
    , ",\"error\":true}";
}

?>
