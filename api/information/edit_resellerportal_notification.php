<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="edit_resellerportal_notification")
    {
      include_once "../dbconfig.php";
      
      $resellerportal_notification=$_POST["resellerportal_notification"];

      $query = "UPDATE `information` SET
                `resellerportal_notification`=?
                WHERE `information_id`=?";


      $information_id=1;

      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('ss',
                        $resellerportal_notification,
                        $information_id);

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
