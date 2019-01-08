<?php
if(isset($_POST["post_action"]))
{
  if($_POST["post_action"]==="get_request_details" && isset($_POST["request_id"]))
  {
    include_once "../dbconfig.php";

    $request_id = intval($_POST["request_id"]);

    // get request info and reseller info
    $query = "SELECT `request_id`, reseller.`customer_id`, `admins`.`username`
    ,reseller.`full_name` as 'reseller_full_name', `requests`.`order_id`, `creation_date`, `action`,
     `action_value`,`admins`.`admin_id`, `verdict`, `verdict_date`, `action_on_date`,
     `product_price`, `requests`.`note`, `requests`.`full_name`, `requests`.`email`, `requests`.`phone`, `product_title`, `product_category`,
     `product_subscription_type`, `modem_mac_address`, `requests`.`modem_id`,`requests`.`city`,`requests`.`address_line_1`,`requests`.`address_line_2`,`requests`.`postal_code`,
     `modems`.`mac_address`
    FROM `requests`
    INNER JOIN `customers` as reseller on `reseller`.`customer_id`= `requests`.`reseller_id`
    LEFT JOIN `modems` on `requests`.`modem_id`=`modems`.`modem_id`
    LEFT JOIN `admins` on `admins`.`admin_id`=`requests`.`admin_id`
    WHERE `request_id`=?";


        $stmt1 = $dbTools->getConnection()->prepare($query);

        $param_value=$request_id;
        $stmt1->bind_param('s',
                          $param_value
                          ); // 's' specifies the variable type => 'string'


        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $request_row = $dbTools->fetch_assoc($result1);



    $request_modem_mac_address = (strlen($request_row["modem_mac_address"]) > 0 ? $request_row["modem_mac_address"] : $request_row["mac_address"]);
    $request_row["modem_mac_address"]=$request_modem_mac_address;
    /// get request's order info
    $request_order_query = "SELECT `orders`.*,`order_options`.*,`customers`.`full_name`,`orders`.`order_id` as `this_order_id` FROM `orders`
    INNER JOIN `order_options` on `order_options`.`order_id`=`orders`.`order_id`
    INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
    where `orders`.`order_id`=?";


            $stmt2 = $dbTools->getConnection()->prepare($request_order_query);

            $param_value=$request_row['order_id'];
            $stmt2->bind_param('s',
                              $param_value
                              ); // 's' specifies the variable type => 'string'


            $stmt2->execute();

            $result2 = $stmt2->get_result();
            $request_order_row = $dbTools->fetch_assoc($result2);

            $request_order_row["order_id"]=$request_order_row["this_order_id"];
            if ((int) $request_order_row['order_id'] <= 10380) {
                $request_order_row["displayed_order_id"]= $request_order_row['order_id'];
            } else {
                $request_order_row["displayed_order_id"]= (((0x0000FFFF & (int) $request_order_row['order_id']) << 16) + ((0xFFFF0000 & (int) $request_order_row['order_id']) >> 16));
            }
    /// indentify start active date
    $start_active_date = "";
    if ($request_order_row["product_category"] === "phone") {
        $start_active_date = $request_order_row["creation_date"];
    } else if ($request_order_row["product_category"] === "internet") {
        if ($request_order_row["cable_subscriber"] === "yes") {
            $start_active_date = $request_order_row["cancellation_date"];
        } else {
            $start_active_date = $request_order_row["installation_date_1"];
        }
    }
    $request_order_row["start_active_date"]=$start_active_date;

    /// get last approved request for this order if exist;
    $last_request_query = "SELECT `request_id`, reseller.`customer_id`, `admins`.`username`
    ,reseller.`full_name`, `requests`.`order_id`, `creation_date`, `action`,
     `action_value`,`admins`.`admin_id`, `verdict`, `verdict_date`, `action_on_date`,
     `product_price`, `requests`.`note`, `product_title`, `product_category`,
     `product_subscription_type`, `modem_mac_address`, `requests`.`modem_id`,`requests`.`city`,`requests`.`address_line_1`,`requests`.`address_line_2`,`requests`.`postal_code`,
     `modems`.`mac_address`
    FROM `requests`
    INNER JOIN `customers` as reseller on `reseller`.`customer_id`= `requests`.`reseller_id`
    LEFT JOIN `modems` on `requests`.`modem_id`=`modems`.`modem_id`
    LEFT JOIN `admins` on `admins`.`admin_id`=`requests`.`admin_id`

    WHERE `requests`.`order_id`=? and `requests`.`action_on_date` < ? and verdict='approve' ORDER BY action_on_date DESC LIMIT 1";


        $stmt3 = $dbTools->getConnection()->prepare($last_request_query);


        $stmt3->bind_param('ss',
                          $request_row['order_id'],
                          $request_row['action_on_date']
                          ); // 's' specifies the variable type => 'string'


        $stmt3->execute();

        $result3 = $stmt3->get_result();
        $last_request_row = $dbTools->fetch_assoc($result3);



    $product_price = $request_order_row['product_price'];
    $product_title = $request_order_row['product_title'];
    $product_category = $request_order_row['product_category'];
    $product_subscription_type = $request_order_row['product_subscription_type'];
    if (sizeof($last_request_row) > 0) {
      $last_request_modem_mac_address = (strlen($last_request_row["modem_mac_address"]) > 0 ? $last_request_row["modem_mac_address"] : $last_request_row["mac_address"]);
      $last_request_row["modem_mac_address"]=$last_request_modem_mac_address;


        $product_price = $last_request_row['product_price'];
        $product_title = $last_request_row['product_title'];
        $product_category = $last_request_row['product_category'];
        $product_subscription_type = $last_request_row['product_subscription_type'];
    }
if($request_row["action"]!="change_speed")// if not change speed request take these value from the last updated request or order
{
  $request_row["product_price"]=$product_price;
  $request_row["product_title"]=$product_title;
  $request_row["product_category"]=$product_category;
  $request_row["product_subscription_type"]=$product_subscription_type;
}

    if($stmt1->errno==0 && $stmt2->errno==0 && $stmt3->errno==0)
    {
      $request_row_json = json_encode($request_row);
      $request_order_row_json = json_encode($request_order_row);
      $last_request_row_json = json_encode($last_request_row);
        echo "{\"request_row\" :", $request_row_json
          ,",\"request_order_row\" :", $request_order_row_json
          ,",\"last_request_row\" :", $last_request_row_json
          , ",\"error\":false}";
    }
    else {
      echo "{\"request_row\" :", "{}"
        ,"\"request_order_row\" :", "{}"
        ,"\"last_request_row\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["post_action"]==="edit_request" && isset($_POST["request_id"]) && isset($_POST["verdict"]))
    {
      include_once "../dbconfig.php";


      $excute_failed=0;


          if ($_POST["action"] === "moving") {
              $verdict_date = new DateTime();

              $param_value1=$admin_id;
              $param_value2=$_POST["verdict"];
              $param_value3=$verdict_date->format('Y-m-d');
              $param_value4=$_POST["product_price"];
              $param_value5=$_POST["product_title"];
              $param_value6=$_POST["product_category"];
              $param_value7=$_POST["product_subscription_type"];
              $param_value8=$_POST["fees_charged"];
              $param_value9=$_POST["request_id"];


              $query_update_request = "UPDATE `requests` SET
                                      `admin_id`=?,
                                      `verdict`=?,
                                      `verdict_date`=?,
                                      `product_price`=?,
                                      `product_title`=?,
                                      `product_category`=?,
                                      `product_subscription_type`=?,
                                      `fees_charged`=?
                                      WHERE `requests`.`request_id`=?";

                $stmt1 = $dbTools->getConnection()->prepare($query_update_request);

                $stmt1->bind_param('sssssssss',
                                  $param_value1,
                                  $param_value2,
                                  $param_value3,
                                  $param_value4,
                                  $param_value5,
                                  $param_value6,
                                  $param_value7,
                                  $param_value8,
                                  $param_value9);


                $stmt1->execute();

                if ($stmt1->errno!=0) {
                  /// $excute_failed
                  $excute_failed=1;
                }
          } else {

              if (($_POST["action"] === "swap_modem" && $_POST["verdict"] === "approve" ) || ($_POST["verdict"] === "approve" && $_POST["action"] === "change_speed" && is_numeric($_POST["modem_id"]) && (int) $_POST["modem_id"] > 0)) {
                $param_value1=$_POST["customer_id"];
                  $query_update_request = "update `modems` set `customer_id`='0' "
                          . "where `customer_id`=?";
                  $stmt1 = $dbTools->getConnection()->prepare($query_update_request);

                  $stmt1->bind_param('s',
                                    $param_value1);


                  $stmt1->execute();

                  if ($stmt1->errno!=0) {
                    /// $excute_failed
                    $excute_failed=1;
                  }

                  $param_value2=$_POST["customer_id"];
                  $param_value3=$_POST["modem_id"];
                  $query_update_request = "update `modems` set `customer_id`=? "
                          . "where `modem_id`=?";
                  $stmt2 = $dbTools->getConnection()->prepare($query_update_request);

                  $stmt2->bind_param('ss',
                                    $param_value2,
                                    $param_value3);


                  $stmt2->execute();

                  if ($stmt2->errno!=0) {
                    /// $excute_failed
                    $excute_failed=1;
                  }
              }
              else if ($_POST["action"] === "customer_information_modification" && $_POST["verdict"] === "approve"){
                $param_value1=$_POST['full_name'];
                $param_value2=$_POST['phone'];
                $param_value3=$_POST['email'];
                $param_value4=$_POST["customer_id"];

                  $query_update_request = "UPDATE `customers` set `full_name`=?,"
                          . " `phone`=?, `email`=? "
                          . "WHERE `customer_id`=?";

                  $stmt2 = $dbTools->getConnection()->prepare($query_update_request);

                  $stmt2->bind_param('ssss',
                                    $param_value1,
                                    $param_value2,
                                    $param_value3,
                                    $param_value4);


                  $stmt2->execute();

                  if ($stmt2->errno!=0) {
                    /// $excute_failed
                    $excute_failed=1;
                  }
              }

              $verdict_date = new DateTime();

              $param1=$admin_id;
              $param2=$_POST["verdict"];
              $param3=$verdict_date->format('Y-m-d');
              $param4=$_POST["fees_charged"];
              $param5=$_POST["request_id"];


              $query_update_request = "UPDATE `requests` SET
                                          `admin_id`=?,
                                          `verdict`=?,
                                          `verdict_date`=?,
                                          `fees_charged`=?
                                          WHERE `requests`.`request_id`=?";
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
              if ($_POST["verdict"] === "approve" && $_POST["action"] === "moving") {
                  $query_update_order = "UPDATE `orders` SET
                                          `status`=N'sent'
                                          WHERE `orders`.`order_id`=?";

                  $stmt2 = $dbTools->getConnection()->prepare($query_update_order);

                  $stmt2->bind_param('s',
                                    $_POST["order_id"]);


                  $stmt2->execute();

                  if ($stmt2->errno==0) {
                      echo "{\"edited\" :true,\"error\" :\"null\"}";
                    } else {
                      echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
                    }
                } else {
                  echo "{\"edited\" :true,\"error\" :\"null\"}";
                }
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
