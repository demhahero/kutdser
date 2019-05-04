<?php
include_once "../dbconfig.php";

if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_reseller_info")
  {
    $customer_id=$_POST["reseller_id"];
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
      echo "{\"resller_info\":", $json
        , ",\"message\":\"\""
        , ",\"error\":false}";
    }
    else {
      echo "{\"resller_info\":[]"
        , ",\"message\":\"failed: reseller info not found\""
        , ",\"error\":true}";
    }

  }// end login
}
else {
  echo "{\"resller_info\":[]"
    , ",\"message\":\"failed: you are not authorized\""
    , ",\"error\":true}";
}

?>
