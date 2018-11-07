<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_order_by_id" && isset($_POST["order_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT
    	`orders`.`order_id`,
      `orders`.`admin_id`,
      `orders`.`update_date`,
      `orders`.`status`,
      `orders`.`vl_number`,
      `order_options`.`modem`,
      `order_options`.`modem_mac_address`,
      `order_options`.`modem_modem_type`,
      `order_options`.`cable_subscriber`,
      `order_options`.`completion`,
      `order_options`.`actual_installation_date`,
      `order_options`.`actual_installation_time_from`,
      `order_options`.`actual_installation_time_to`,
      `modems`.`mac_address`,
      `modems`.`serial_number`,
      `customers`.`ip_address`,
      `customers`.`customer_id`,
      `admins`.`username`
    	FROM `orders`
    	inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
      LEFT JOIN `modems` ON `order_options`.`modem_id`=`modems`.`modem_id`
    	inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
      LEFT JOIN `admins` ON `admins`.`admin_id`=`orders`.`admin_id`
      WHERE `orders`.`order_id`= ? ";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$_POST["order_id"];
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if($result)
    {
      $json = json_encode($result);
        echo "{\"order_details\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"order_details\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["action"]==="edit_order" && isset($_POST["edit_id"]))
  {
      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $dateNow=new DateTime();
      $dateNowString=$dateNow->format("Y-m-d");
      $status=$_POST["status"];
      $completion=$_POST["completion"];
      $actual_installation_date=$_POST["actual_installation_date"];
      $update_date=$dateNowString;
      $admin_id_copy=$admin_id;
      $actual_installation_time_from=$_POST["actual_installation_time_from"];
      $actual_installation_time_to=$_POST["actual_installation_time_to"];
      $vl_number=$_POST["vl_number"];


        $query = "UPDATE `orders` INNER JOIN `order_options` ON `orders`.`order_id` = `order_options`.`order_id`
                  SET
                  `status`=?,
                  `completion`=?,
                  `actual_installation_date`=?,
                  `update_date`=?,
                  `admin_id`=?,
                  `actual_installation_time_from`=?,
                  `actual_installation_time_to`=?,
                  `vl_number`=?
                  WHERE `orders`.`order_id`=?";


        $stmt1 = $dbTools->getConnection()->prepare($query);

        $stmt1->bind_param('sssssssss',
                          $status,
                          $completion,
                          $actual_installation_date,
                          $update_date,
                          $admin_id_copy,
                          $actual_installation_time_from,
                          $actual_installation_time_to,
                          $vl_number,
                          $edit_id);


        $stmt1->execute();

        $order = $stmt1->get_result();
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
