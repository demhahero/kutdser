<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'router_id',
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
          `routers`.*,
          `customers`.`full_name`,
          `resellers`.`full_name` as reseller_full_name
          FROM `routers`
          LEFT JOIN `customers` ON `customers`.`customer_id`= `routers`.`customer_id`
          LEFT JOIN `customers` as `resellers` ON `resellers`.`customer_id`=`routers`.`reseller_id`";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( customers.full_name LIKE ? ";
    $where .= " OR resellers.full_name LIKE ? ";
    $where .= " OR router_id LIKE ? ";
    $where .= " OR serial_number LIKE ? ) ";
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
$stmt->bind_param('ssss',
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
$stmt1->bind_param('ssss',
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


    $data[0] = '<a href="edit_router.php?router_id='.$row['router_id'].'" >'.$row['router_id'].'</a>';
    $data[1] = $row['serial_number'];
    $data[2] = $row['reseller_full_name'];
    $data[3] = $row['full_name'];
    $data[4] = '<button class="btn btn-primary edit" data-id='.$row['router_id'].'><i class="fa fa-pencil-square-o"></i></button>
    <button class="btn btn-danger remove" data-id='.$row['router_id'].'><i class="fa fa-remove"></i></button>';
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
