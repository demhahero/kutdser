<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'modem_id',
    1 => 'mac_address',
    2 => 'type',
    3 => 'serial_number',
    4 => '`resellers`.`full_name`',
    5 => '`customers`.`full_name`',
    6 => 'functions'

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `modems`.modem_id, `modems`.serial_number, `modems`.type,`modems`.customer_id,`modems`.mac_address,
    resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name'
FROM `modems`

LEFT JOIN `customers` on `modems`.`customer_id`=`customers`.`customer_id`
LEFT JOIN `customers` resellers on resellers.`customer_id` = `modems`.`reseller_id` ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( customers.full_name LIKE ? ";
    $where .= " OR resellers.full_name LIKE ? ";
    $where .= " OR `modems`.mac_address LIKE ? ";
    $where .= " OR `modems`.type LIKE ? ";
    $where .= " OR `modems`.serial_number LIKE ? ";
    $where .= " OR `modems`.modem_id LIKE ? ) ";
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
$stmt->bind_param('ssssss',
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
$stmt1->bind_param('ssssss',
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


    $data[0] = $row['modem_id'];
    $data[1] = $row['mac_address'];
    $data[2] = $row['type'];
    $data[3] = $row['serial_number'];
    $data[4] = $row['reseller_name'];
    $data[5] = '<a href="'.$site_url.'/edit_customer.php?customer_id='.$row['customer_id'].'">'.$row['customer_name'].'</a>';
    $data[6] = '<button class="btn btn-primary edit" data-id='.$row['modem_id'].'><i class="fa fa-pencil-square-o"></i></button>
    <button class="btn btn-danger remove" data-id='.$row['modem_id'].'><i class="fa fa-remove"></i></button>';
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
