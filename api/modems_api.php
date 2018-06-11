<?php

//include connection file
include_once "dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'modem_id',
    1 => 'mac_address',
    2 => '`resellers`.`full_name`',
    3 => '`customers`.`full_name`',
    4 => 'functions'

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `modems`.modem_id,`modems`.customer_id,`modems`.mac_address,
    resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name'
FROM `modems`

inner JOIN `customers` on `modems`.`customer_id`=`customers`.`customer_id`
INNER JOIN `customers` resellers on resellers.`customer_id` = `modems`.`reseller_id` ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( customers.full_name LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR resellers.full_name LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `modems`.mac_address LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `modems`.modem_id LIKE '%" . $params['search']['value'] . "%' ) ";
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
$queryTot = mysqli_query($dbTools->getConnection(), $sqlTot);

$totalRecords = mysqli_num_rows($queryTot);


$queryRecords = mysqli_query($dbTools->getConnection(), $sqlRec);


//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {


    $data[0] = $row['modem_id'];
    $data[1] = $row['mac_address'];
    $data[2] = $row['reseller_name'];
    $data[3] = '<a href="'.$site_url.'/edit_customer.php?customer_id='.$row['customer_id'].'">'.$row['customer_name'].'</a>';
    $data[4] = '<a href="edit_modem.php?modem_id='.$row['modem_id'].'"><img title="Edit" width="30px" src="'.$site_url.'/img/edit-icon.png" /></a><a class="check-alert" href="modems.php?do=delete&modem_id='.$row['modem_id'].'"><img title="Remove" width="30px" src="'.$site_url.'/img/delete-icon.png" /></a>';
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
