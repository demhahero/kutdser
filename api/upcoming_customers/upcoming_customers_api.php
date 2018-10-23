<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'upcoming_customer_id',
    1 => 'full_name',
    2 => 'phone',
    3 => 'creation_date',
    4 => 'username',
    5 => 'functions'

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT * from `upcoming_customers`
            LEFT JOIN `admins` on `upcoming_customers`.`admin_id`=`admins`.`admin_id` ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( upcoming_customers.full_name LIKE ? ";
    $where .= " OR admins.username LIKE ? ";
    $where .= " OR `upcoming_customers`.phone LIKE ? ";
    $where .= " OR `upcoming_customers`.upcoming_customer_id LIKE ? ) ";
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


    $data[0] = $row['upcoming_customer_id'];
    $data[1] = $row['full_name'];
    $data[2] = $row['phone'];
    $data[3] = $row['creation_date'];
    $data[4] = $row['username'];

    $data[5] = '<button class="btn btn-primary edit" data-id='.$row['upcoming_customer_id'].'><i class="fa fa-pencil-square-o"></i></button>
    <button class="btn btn-danger remove" data-id='.$row['upcoming_customer_id'].'><i class="fa fa-remove"></i></button>';
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
