<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="update_order_reseller_commission_percentage" && isset($_POST["edit_id"]))
    {
      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $reseller_commission_percentage=$_POST["reseller_commission_percentage"];


      $query = "UPDATE `order_options` SET
                `reseller_commission_percentage`=?
                WHERE `order_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('ss',
                        $reseller_commission_percentage,
                        $edit_id);


      $stmt1->execute();

      $modem = $stmt1->get_result();
      if ($stmt1->errno==0) {
          echo "{\"updated\" :true,\"error\" :\"null\"}";
      } else {

          echo "{\"updated\" :\"false\",\"error\" :\"failed to insert value\"}";
      }
    }
}else
{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
?>
