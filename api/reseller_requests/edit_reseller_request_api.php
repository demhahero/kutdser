<?php

if(isset($_POST["post_action"]))
{
  if($_POST["post_action"]==="get_reseller_request_by_id" && isset($_POST["edit_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT
          	 *
          	FROM `reseller_requests`
            WHERE `reseller_request_id`= ? AND `reseller_id`=?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$_POST["edit_id"];
    $param_value1=$reseller_id;
    $stmt1->bind_param('ss',
                      $param_value,
                      $param_value1
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if($result)
    {
      $json = json_encode($result);
        echo "{\"reseller_request\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"reseller_request\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["post_action"]==="edit_reseller_request" && isset($_POST["edit_id"]))
    {

      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $mac_address=$_POST["modem_mac_address"];
      $serial_number=$_POST["modem_serial_number"];
      $type=$_POST["modem_type"];
      $action=$_POST["action"];
      $note=$_POST["note"];
      $action_on_date=new DateTime($_POST["action_on_date"]);
      $action_on_date_string=$action_on_date->format("Y-m-d");

      $query = "UPDATE `reseller_requests` SET
                `modem_mac_address`=?,
                `modem_serial_number`=?,
                `modem_type`=?,
                `action`=?,
                `action_on_date`=?,
                `note`=?
                WHERE `reseller_request_id`=? AND `reseller_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('ssssssss',
                        $mac_address,
                        $serial_number,
                        $type,
                        $action,
                        $action_on_date_string,
                        $note,
                        $edit_id,
                        $reseller_id);


      $stmt1->execute();

      $reseller_request = $stmt1->get_result();
      if ($stmt1->errno==0) {
          echo "{\"edited\" :true,\"error\" :\"null\"}";
      } else {

          echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
      }
    }
}else
{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
?>
