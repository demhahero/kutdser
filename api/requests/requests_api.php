<?php

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
    3 => 'reseller_full_name',
    4 => 'action',
    5 => 'product_title',
    6 => 'action_on_date',
    7 => 'creation_date',
    8 => 'verdict',
    9 => 'username',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
            requests.request_id,
            requests.product_price,
            requests.product_title,
            requests.verdict,
            requests.note,
            orders.order_id,
            customers.customer_id,
            customers.full_name,
            requests.action,
            requests.action_value,
            requests.action_on_date,
            requests.creation_date,
            requests.modem_id,
            resellers.full_name as 'reseller_full_name',
            resellers.customer_id as 'reseller_id',
            admins.admin_id,
            admins.username
          FROM requests
            INNER JOIN `customers` resellers on resellers.`customer_id` = requests.reseller_id
            INNER JOIN orders on orders.order_id = requests.order_id
            INNER JOIN customers on customers.customer_id=orders.customer_id
            left JOIN admins on requests.admin_id = admins.admin_id
            ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( customers.full_name LIKE ? ";
    $where .= " OR resellers.full_name LIKE ? ";
    $where .= " OR request_id LIKE ? ";
    $where .= " OR requests.product_title LIKE ? ";
    $where .= " OR verdict LIKE ? ";
    $where .= " OR admins.username LIKE ? ";
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
$stmt->bind_param('sssssss',
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value );

}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt1->bind_param('sssssss',
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value
                  ); // 's' specifies the variable type => 'string'
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

  $action_on_date="";
  if(strLen($row['action_on_date'])>0){
      $action_on_date = explode(' ', $row['action_on_date']);
      $action_on_date = $action_on_date[0];
  }
    $data[0] = '<a href="request_details.php?request_id='.$row['request_id'].'" >'.$row['request_id'].'</a>';
    $data[1] = $row['order_id'];
    $data[2] = $row['full_name'];
    $data[3] = $row['reseller_full_name'];
    $data[4] = $row['action'];
    $data[5] = $row['product_title'];
    $data[6] = $action_on_date;
    $data[7] = $row['creation_date'];
    $data[8] = $row['verdict'];
    $data[9] = $row['username'];
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
