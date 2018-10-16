<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_modem_by_id" && isset($_POST["edit_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT
    	`modems`.*,
      `customers`.`customer_id`,
      `customers`.`full_name`,
      `resellers`.`customer_id` as `reseller_id`,
      `resellers`.`full_name` as `reseller_full_name`
    	FROM `modems`
    	LEFT JOIN `customers` as `resellers` ON `modems`.`reseller_id`=`resellers`.`customer_id`
    	LEFT JOIN `customers` on `modems`.`customer_id`=`customers`.`customer_id`
      WHERE `modems`.`modem_id`= ? ";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$_POST["edit_id"];
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if($result)
    {
      $json = json_encode($result);
        echo "{\"modem\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"modem\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["action"]==="edit_modem" && isset($_POST["edit_id"]))
    {
      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $mac_address=$_POST["mac_address"];
      $serial_number=$_POST["serial_number"];
      $type=$_POST["type"];
      $reseller_id=$_POST["reseller_id"];
      $customer_id=$_POST["customer_id"];
      $is_ours=$_POST["is_ours"];

      $query = "UPDATE `modems` SET
                `mac_address`=?,
                `serial_number`=?,
                `type`=?,
                `reseller_id`=?,
                `customer_id`=?,
                `is_ours`=?
                WHERE `modem_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('sssssss',
                        $mac_address,
                        $serial_number,
                        $type,
                        $reseller_id,
                        $customer_id,
                        $is_ours,
                        $edit_id);


      $stmt1->execute();

      $modem = $stmt1->get_result();
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
