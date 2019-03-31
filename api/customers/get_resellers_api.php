<?php

if(isset($_POST["post_action"]) && $_POST["post_action"]="get_reseller")
{
  //include connection file
  include_once "../dbconfig.php";

  if(isset($_POST["reseller_condition"]) && $_POST["reseller_condition"]=="equal" && isset($_POST["reseller_id"]) && intval($_POST["reseller_id"])>0)
  {
    $where=" AND `customer_id`=?";
  }
  elseif (isset($_POST["reseller_condition"]) && $_POST["reseller_condition"]=="not_equal" && isset($_POST["reseller_id"]) && intval($_POST["reseller_id"])>0) {
    $where=" AND `customer_id` != ?";
  }
  $sqlTot = "SELECT `customer_id`,`full_name`
              FROM `customers`
              WHERE `is_reseller` = '1'";







  //concatenate search sql if value exist
  if (isset($where) && $where != '') {

      $sqlTot .= $where;
  }

$sqlTot .= " ORDER BY `full_name`";

  $stmt = $dbTools->getConnection()->prepare($sqlTot);
  if (isset($where) && $where != '') {
    $search_value=$_POST["reseller_id"];


  $stmt->bind_param('s',
                    $search_value );

  }

  $stmt->execute();

  $result = $stmt->get_result();

  $all_data=[];
  while ($row = mysqli_fetch_array($result)) {

      $all_data[] = $row;
  }

  $json_data = array(
      "data" => $all_data,  // total data array
      "error"=> false
  );

  echo $json = json_encode($json_data);
}
else{
  $json_data = array(
      "data" => "not authorized",   // total data array
      "error" => true
  );

  echo $json = json_encode($json_data);
}
?>
