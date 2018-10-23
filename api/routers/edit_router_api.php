<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_router_by_id" && isset($_POST["edit_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT
    	`routers`.*,
      `customers`.`customer_id`,
      `customers`.`full_name`,
      `resellers`.`customer_id` as `reseller_id`,
      `resellers`.`full_name` as `reseller_full_name`
    	FROM `routers`
    	LEFT JOIN `customers` as `resellers` ON `routers`.`reseller_id`=`resellers`.`customer_id`
    	LEFT JOIN `customers` on `routers`.`customer_id`=`customers`.`customer_id`
      WHERE `routers`.`router_id`= ? ";


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
        echo "{\"router\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"router\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["action"]==="edit_router" && isset($_POST["edit_id"]))
    {
      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $serial_number=$_POST["serial_number"];
      $type=$_POST["type"];
      $reseller_id=$_POST["reseller_id"];
      $customer_id=$_POST["customer_id"];
      $is_ours=$_POST["is_ours"];
      $is_sold=$_POST["is_sold"];

      $query = "UPDATE `routers` SET
                `serial_number`=?,
                `type`=?,
                `reseller_id`=?,
                `customer_id`=?,
                `is_ours`=?,
                `is_sold`=?
                WHERE `router_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('sssssss',
                        $serial_number,
                        $type,
                        $reseller_id,
                        $customer_id,
                        $is_ours,
                        $is_sold,
                        $edit_id);


      $stmt1->execute();

      $router = $stmt1->get_result();
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
