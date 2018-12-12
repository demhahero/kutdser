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
    2 => 'action',
    3 => 'action_on_date',
    4 => 'creation_date',
    5 => 'number_of_items',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT
           `reseller_requests`.`reseller_request_id`,
           `customers`.`full_name`,
            `action`,
            `action_on_date`,
            `creation_date`,
            IFNULL(`number_of_items`,0) AS `number_of_items`,
            IFNULL(`approved`,0) AS `approved`,
            IFNULL(`disapproved`,0) AS `disapproved`
          FROM `reseller_requests`
          LEFT JOIN
          (
            SELECT count(*) AS `number_of_items`,`reseller_request_items`.`reseller_request_id`,
              IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'approve', 1, 0)), 0) as `approved`,
            IFNULL(sum(IF(`reseller_request_items`.`verdict` = 'disapprove', 1, 0)), 0) as `disapproved`

            FROM `reseller_request_items` GROUP BY `reseller_request_items`.`reseller_request_id`)
          AS `reseller_requests_items` ON `reseller_requests_items`.`reseller_request_id`= `reseller_requests`.`reseller_request_id`
          INNER JOIN `customers` ON `customers`.`customer_id`=`reseller_requests`.`reseller_id`
          ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( customer_full_name LIKE ? ";
    $where .= " OR `reseller_requests`.`reseller_request_id` LIKE ? ) ";
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
$stmt->bind_param('ss',
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
  $stmt1->bind_param('ss',
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

    if($row['action']=="add_modem")
    $row['action']="Add Modem(s)";

    $data[0] = $row['reseller_request_id'];
    $data[1] = $row['full_name'];
    $data[2] = $row['action'];
    $data[3] = $row['creation_date'];
    $data[4] = $row['action_on_date'];
    $data[5] = $row['number_of_items'];
    $data[6] = $row['approved'];
    $data[7] = $row['disapproved'];
    $data[8] = '<button class="btn btn-primary view" data-id='.$row['reseller_request_id'].'><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>';


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
