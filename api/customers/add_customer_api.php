<?php

if (isset($_GET["action"])) {
  if($_GET["action"]==="get_all_customers"){
    include_once "../dbconfig.php";

    $customer_query = $dbTools->query("SELECT `customer_id`,`full_name` FROM `customers` WHERE `is_reseller` = '0'");


      $customers=array();
      while ($customer = $dbTools->fetch_assoc($customer_query)) {
        array_push($customers,$customer);
      }
    $json_customers = json_encode($customers);
    echo "{\"customers\" :", $json_customers, "}";
  }
  if($_GET["action"]==="get_all_resellers")
  {
    include_once "../dbconfig.php";

    $reseller_query = $dbTools->query("SELECT `customer_id`,`full_name` FROM `customers` WHERE `is_reseller` = '1'");


      $resellers=array();
      while ($reseller = $dbTools->fetch_assoc($reseller_query)) {
        array_push($resellers,$reseller);
      }
    $json_resellers = json_encode($resellers);
    echo "{\"resellers\" :", $json_resellers, "}";
  }
} else if (isset($_POST["customer_id"])) {

    include_once "../dbconfig.php";


        $query="SELECT `customer_id`,
                        `customer_center_id`,
                        `customer_note`,
                        `receipt_arrived_date`,
                        `receipt_received_date`,
                        `receipt_received_name`,
                        `receipt_note`,
                        `receipt_note_public`,
                        `receipt_center_id`,
                receipt_center.center_number as receipt_center_number,customer_center.center_number as customer_center_number
                FROM `customers`
                       LEFT JOIN `receipts` ON `customers`.`customer_receipt_id` = `receipts`.`receipt_id`
                       LEFT JOIN `centers` as receipt_center ON `receipts`.`receipt_center_id` = `receipt_center`.`center_id`
                       LEFT JOIN `centers` as customer_center ON `customers`.`customer_center_id` = `customer_center`.`center_id`
                        where
                        `customers`.`customer_number`= N'" . $_POST["customer_number"] . "'
                        and 	`customers`.`customer_province` = N'" . $_POST["customer_province"] . "'
                        and `customers`.`customer_class`= N'" . $_POST["customer_class"] . "'";
        if($_POST["customer_class"]!=="فحص"){
          $query.=" and `customers`.`customer_type`= N'" . $_POST["customer_type"] . "' ";
        }

        $customer_query = $dbTools->query($query);
        $message="";
        while ($customer = $dbTools->fetch_assoc($customer_query)) {
            $message="{\"inserted\" :\"false\",\"status\":4}";
        }

        if($message!=="")
        {
          echo $message;
          exit();
        }


    $customer_last_id=$_POST["customer_customer_id"];
    if($_POST["customer_customer_full_name"] !==$_POST["customer_customer_full_name_new"]){
      $query_customer = "INSERT INTO `customers` ( `customer_full_name`) VALUES ( N'".$_POST["customer_customer_full_name_new"]."' )";

      if($dbTools->query($query_customer)===TRUE){
        $customer_last_id=$dbTools->getConnection()->insert_id;
      }
    }
    $query = "update `customers` set "
            . "`customer_number`=N'".$_POST["customer_number"]."',"
            . "`customer_class`=N'".$_POST["customer_class"]."',"
            . "`customer_province`=N'".$_POST["customer_province"]."',"
            . "`customer_type`=N'".$_POST["customer_type"]."',"
            . "`customer_manufacture_number`='".$_POST["customer_manufacture_number"]."',"
            . "`customer_customer_id`='".$customer_last_id."',"
            . "`customer_image_url`='".$_POST["customer_image_url"]."',"
            . "`customer_note`='".$_POST["customer_note"]."' where `customer_id`='".$_POST["customer_id"]."'";

    $customer_log = $dbTools->query($query);

    if ($customer_log) {
        $json = json_encode($customer_log);
        echo "{\"inserted\" :", $json, ",\"error\" :\"null\"}";
    } else {
        echo "{\"inserted\" :\"false\",\"error\" :\"فشل تحديث المعلومات\"}";
    }
}
else{
  echo "لا تمتلك صلاحية الدخول لهذه الصفحة";
}
