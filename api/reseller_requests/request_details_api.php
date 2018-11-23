<?php
if(isset($_POST["post_action"]))
{
  if($_POST["post_action"]==="get_request_details" && isset($_POST["request_id"]))
  {
    include_once "../dbconfig.php";

    $request_id = intval($_POST["request_id"]);

    // get request info and reseller info
    $query = "SELECT
                `reseller_request_id`,
                `admins`.`username`,
                `reseller`.`full_name` as 'reseller_full_name',
                `creation_date`,
                `action`,
                `verdict`,
                `verdict_date`,
                `action_on_date`,
                `reseller_requests`.`note`,
                `modem_mac_address`,
                `modem_serial_number`,
                `modem_type`
            FROM `reseller_requests`
            INNER JOIN `customers` as `reseller` on `reseller`.`customer_id`= `reseller_requests`.`reseller_id`
            LEFT JOIN `admins` on `admins`.`admin_id`=`reseller_requests`.`admin_id`
            WHERE `reseller_request_id`=?";


        $stmt1 = $dbTools->getConnection()->prepare($query);

        $param_value=$request_id;
        $stmt1->bind_param('s',
                          $param_value
                          ); // 's' specifies the variable type => 'string'


        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $request_row = $dbTools->fetch_assoc($result1);


    if($stmt1->errno==0 )
    {
      $request_row_json = json_encode($request_row);
        echo "{\"request_row\" :", $request_row_json
          , ",\"error\":false}";
    }
    else {
      echo "{\"request_row\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["post_action"]==="edit_request" && isset($_POST["reseller_request_id"]) && isset($_POST["verdict"]))
    {
      include_once "../dbconfig.php";


      $excute_failed=0;




              $verdict_date = new DateTime();

              $param1=$admin_id;
              $param2=$_POST["verdict"];
              $param3=$verdict_date->format('Y-m-d');
              $param4=$_POST["reseller_request_id"];


              $query_update_request = "UPDATE `reseller_requests` SET
                                          `admin_id`=?,
                                          `verdict`=?,
                                          `verdict_date`=?
                                          WHERE `reseller_requests`.`reseller_request_id`=?";
              $stmt2 = $dbTools->getConnection()->prepare($query_update_request);

              $stmt2->bind_param('ssss',
                                $param1,
                                $param2,
                                $param3,
                                $param4);


              $stmt2->execute();

              if ($stmt2->errno!=0) {
                /// $excute_failed
                $excute_failed=1;
              }





          if ($excute_failed===0) {

                  echo "{\"edited\" :true,\"error\" :\"null\"}";

            } else {
              echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
            }
          }

}
else
{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
?>
