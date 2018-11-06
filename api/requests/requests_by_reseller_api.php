<?php

$reseller_id=0;
if(isset($_POST["data_id"]))
  $reseller_id=$_POST["data_id"];
//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'request_id',
    1 => 'order_id',
    2 => 'full_name',
    3 => 'action',
    4 => 'creation_date',
    5 => 'verdict',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
            `requests`.`request_id`,
            `requests`.`verdict`,
            `requests`.`note`,
            `orders`.`order_id`,
            `customers`.`customer_id`,
            `customers`.`full_name`,
            `requests`.`action`,
            `requests`.`action_value`,
            `requests`.`action_on_date`,
            `requests`.`creation_date`,
            `requests`.`modem_id`
          FROM `requests`
            INNER JOIN `orders` on `orders`.`order_id` = `requests`.`order_id`
            INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
          WHERE `requests`.`reseller_id`=?";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( customers.full_name LIKE ? ";
    $where .= " OR request_id LIKE ? ";
    $where .= " OR verdict LIKE ? ";
    $where .= " OR orders.order_id LIKE ? ) ";
}

//concatenate search sql if value exist
if (isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}


//Orders
if($params['order'][0]['column']<4)
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'];

//Pagination
$sqlRec .= " LIMIT " . $params['start'] . " ," . $params['length'];

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt->bind_param('sssss',
                  $reseller_id,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt->bind_param('s',
                    $reseller_id);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
  $stmt1->bind_param('sssss',
                  $reseller_id,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value
                  ); // 's' specifies the variable type => 'string'
}else{
  $stmt1->bind_param('s',
                    $reseller_id);
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

  
  if($row["action"]==="change_speed" && is_numeric($row["modem_id"])  && (int)$row["modem_id"] >0)
  {
    $row["action"]="swap modem and change speed";
  }

    $data[0] = $row['request_id'];
    $data[1] = $row['order_id'];
    $data[2] = $row['full_name'];
    $data[3] = $row['action'];
    $data[4] = $row['creation_date'];
    $data[5] = $row['verdict'];
    $all_data[] = $data;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $all_data   // total data array
);

echo $json = json_encode($json_data);
?>
