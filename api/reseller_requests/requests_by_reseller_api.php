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
    0 => 'reseller_request_id',
    1 => 'modem_type',
    2 => 'modem_mac_address',
    3 => 'modem_serial_number',
    4 => 'action',
    5 => 'creation_date',
    6 => 'action_on_date',
    7 => 'verdict',
    8 => 'verdict_date',
    9 => 'note',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
            `reseller_request_id`,
            `verdict`,
            `verdict_date`,
            `note`,
            `action`,
            `action_on_date`,
            `creation_date`,
            `modem_mac_address`,
            `modem_serial_number`,
            `modem_type`
          FROM `reseller_requests`
          WHERE `reseller_requests`.`reseller_id`=?";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( modem_mac_address LIKE ? ";
    $where .= " OR modem_serial_number LIKE ? ";
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
                  $reseller_id,
                  $search_value,
                  $search_value,
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
  $stmt1->bind_param('sssssss',
                  $reseller_id,
                  $search_value,
                  $search_value,
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


    $data[0] = $row['reseller_request_id'];
    $data[1] = $row['action'];
    $data[2] = $row['modem_mac_address'];
    $data[3] = $row['modem_serial_number'];
    $data[4] = $row['modem_type'];
    $data[5] = $row['creation_date'];
    $data[6] = $row['action_on_date'];
    $data[7] = $row['verdict'];
    $data[8] = $row['verdict_date'];
    $data[9] = $row['note'];
    if(strlen($row['verdict'])<=0)
    {
      $data[10] = '<button class="btn btn-primary edit" data-id='.$row['reseller_request_id'].'>Edit</button>
      <button class="btn btn-danger remove" data-id='.$row['reseller_request_id'].'>Delete</button>';
    }
    else {
      $data[10] = "";
    }

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
