<?php

if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_customer_monintor_by_id" && isset($_POST["edit_id"]))
  {
      include_once "../dbconfig.php";


      $query="SELECT
                  `customers`.`customer_id` ,
                  `customers`.`phone` ,
                  `customers`.`address`,
                  `customers`.`email`,
                  `customers`.`full_name`,
                  `orders`.`order_id`,
                  `resellers`.`full_name` AS 'reseller_name',
                  `customers`.`reseller_id`,
                  `modems`.`mac_address`,
                  `modems`.`router_mac_address`,
                  `modems`.`ip_address`,
                  `modems`.`modem_id`
              FROM `customers`
                  LEFT JOIN `customers` resellers ON resellers.`customer_id` = `customers`.`reseller_id`
                  LEFT JOIN `orders` ON `orders`.`customer_id` = `customers`.`customer_id`
                  LEFT JOIN `modems` ON `orders`.`customer_id` = `modems`.`customer_id`
              where `customers`.`customer_id`= ? ";


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
          echo "{\"customer\" :", $json
            , ",\"error\":false}";
      }
      else {
        echo "{\"customer\" :", "{}"
          , ",\"error\":true}";
      }

    }
  }
?>
