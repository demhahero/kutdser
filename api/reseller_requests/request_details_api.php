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
  else if($_POST["post_action"]==="edit_all_request_items" && isset($_POST["reseller_request_items"]))
    {
      include_once "../dbconfig.php";
      $excute_failed=0;
      $verdict_date = new DateTime();
        foreach ($_POST["reseller_request_items"] as $key => $value) {
          $param1=$admin_id;
          $param2=(isset($value["verdict"])?"approve":"disapprove");
          $param3=$verdict_date->format('Y-m-d');
          $param4=$value["verdict_reason"];
          $param5=$key;
          $query_update_request = "UPDATE `reseller_request_items` SET
                                      `admin_id`=?,
                                      `verdict`=?,
                                      `verdict_date`=?,
                                      `verdict_reason`=?
                                      WHERE `reseller_request_items`.`reseller_request_item_id`=?";

          $stmt2 = $dbTools->getConnection()->prepare($query_update_request);
          $stmt2->bind_param('sssss',
                            $param1,
                            $param2,
                            $param3,
                            $param4,
                            $param5);
          $stmt2->execute();
          if ($stmt2->errno!=0) {
            /// $excute_failed
            $excute_failed=1;
          }
        }
        if ($excute_failed===0) {
            echo "{\"edited\" :true,\"error\" :\"null\"}";
          } else {
            echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
          }
}
else if($_POST["post_action"]==="edit_reseller_request_item" && isset($_POST["edit_id"]))
  {

    include_once "../dbconfig.php";
    $edit_id=$_POST["edit_id"];
    $verdict=($_POST['verdict']==="true"?"approve":"disapprove");
    $verdict_reason=$_POST["verdict_reason"];
    $verdict_date=new DateTime();
    $verdict_date_string=$verdict_date->format('Y-m-d');


    $query = "UPDATE `reseller_request_items`
                SET `admin_id`=?,
                    `verdict`=?,
                    `verdict_reason`=?,
                    `verdict_date`=?
              WHERE `reseller_request_item_id`=?";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('sssss',
                      $admin_id,
                      $verdict,
                      $verdict_reason,
                      $verdict_date_string,
                      $edit_id);


    $stmt1->execute();

    $reseller_request = $stmt1->get_result();
    if ($stmt1->errno==0) {
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
