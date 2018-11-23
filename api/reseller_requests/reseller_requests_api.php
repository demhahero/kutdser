<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'reseller_request_id',
    1 => 'full_name',
    2 => 'modem_type',
    3 => 'modem_mac_address',
    4 => 'modem_serial_number',
    5 => 'action',
    6 => 'creation_date',
    7 => 'action_on_date',
    8 => 'verdict',
    9 => 'verdict_date',
    10 => 'note',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
            `reseller_request_id`,
            `verdict`,
            `verdict_date`,
            `reseller_requests`.`note`,
            `action`,
            `full_name`,
            `action_on_date`,
            `creation_date`,
            `modem_mac_address`,
            `modem_serial_number`,
            `modem_type`
          FROM `reseller_requests`
          INNER JOIN `customers` ON `reseller_requests`.`reseller_id`=`customers`.`customer_id`";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( modem_mac_address LIKE ? ";
    $where .= " OR modem_serial_number LIKE ? ";
    $where .= " OR full_name LIKE ? ";
    $where .= " OR modem_type LIKE ? ";
    $where .= " OR reseller_request_id LIKE ? ";
    $where .= " OR verdict LIKE ? ";
    $where .= " OR note LIKE ? ) ";
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


    $data[0] = '<a href="request_details.php?request_id='.$row['reseller_request_id'].'" >'.$row['reseller_request_id'].'</a>';
    $data[1] = $row['full_name'];
    $data[2] = $row['action'];
    $data[3] = $row['modem_mac_address'];
    $data[4] = $row['modem_serial_number'];
    $data[5] = $row['modem_type'];
    $data[6] = $row['creation_date'];
    $data[7] = $row['action_on_date'];
    $data[8] = $row['verdict'];
    $data[9] = $row['verdict_date'];
    $data[10] = $row['note'];
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
