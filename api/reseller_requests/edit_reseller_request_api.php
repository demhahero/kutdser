<?php

if(isset($_POST["post_action"]))
{
  if($_POST["post_action"]==="get_reseller_request_by_id" && isset($_POST["edit_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT
          	 *
          	FROM `reseller_request_items`
            WHERE `reseller_request_id`= ?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$_POST["edit_id"];
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $request_items=[];
    while($request_item = $dbTools->fetch_assoc($result1))
    {
      if (is_null($request_item["note"]))
      {
        $request_item["note"]="";
      }
      if (is_null($request_item["verdict_reason"]))
      {
        $request_item["verdict_reason"]="";
      }
      $request_items[]=$request_item;
    }
    if(sizeof($request_items)>0)
    {

      $json = json_encode($request_items);
        echo "{\"reseller_request_items\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"reseller_request\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["post_action"]==="edit_reseller_request_item" && isset($_POST["edit_id"]))
    {

      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $mac_address=$_POST["modem_mac_address"];
      $serial_number=$_POST["modem_serial_number"];
      $type=$_POST["modem_type"];
      $note=$_POST["note"];

      $query = "UPDATE `reseller_request_items`
                  SET `note`=?,
                      `modem_mac_address`=?,
                      `modem_serial_number`=?,
                      `modem_type`=?
                WHERE `reseller_request_item_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('sssss',
                        $note,
                        $mac_address,
                        $serial_number,
                        $type,
                        $edit_id);


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
