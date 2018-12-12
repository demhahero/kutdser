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
    1 => 'action',
    2 => 'action_on_date',
    3 => 'creation_date',
    4 => 'number_of_items',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
           `reseller_requests`.`reseller_request_id`,
            `action`,
            `action_on_date`,
            `creation_date`,
            `number_of_items`,
            `approved`,
            `disapproved`
          FROM `reseller_requests`
          LEFT JOIN
          (
            SELECT count(*) AS `number_of_items`,`reseller_request_items`.`reseller_request_id`,
              IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'approve', 1, 0)), 0) as `approved`,
            IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'disapprove', 1, 0)), 0) as `disapproved`

            FROM `reseller_request_items` GROUP BY `reseller_request_items`.`reseller_request_id`)
          AS `reseller_requests_items` ON `reseller_requests_items`.`reseller_request_id`= `reseller_requests`.`reseller_request_id`
          WHERE `reseller_requests`.`reseller_id`=?";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    // $where .= " AND ";
    // $where .= " ( modem_mac_address LIKE ? ";
    // $where .= " OR modem_serial_number LIKE ? ";
    // $where .= " OR modem_type LIKE ? ";
    // $where .= " OR reseller_request_id LIKE ? ";
    // $where .= " OR verdict LIKE ? ";
    // $where .= " OR note LIKE ? ) ";
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

    if($row['action']=="add_modem")
    $row['action']="Add Modem(s)";

    $data[0] = $row['reseller_request_id'];
    $data[1] = $row['action'];
    $data[2] = $row['creation_date'];
    $data[3] = $row['action_on_date'];
    $data[4] = $row['number_of_items'];
    $data[5] = $row['approved'];
    $data[6] = $row['disapproved'];

    if((int)$row['approved']==0 && (int)$row['disapproved']==0)
    {
      $data[7] = '<button class="btn btn-primary view" data-id='.$row['reseller_request_id'].'><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
      <button class="btn btn-danger delete" data-id='.$row['reseller_request_id'].'><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';
    }
    else {
      $data[7] = '<button class="btn btn-primary view" data-id='.$row['reseller_request_id'].'><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>';
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
