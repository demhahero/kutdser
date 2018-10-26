<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'customer_id',
    1 => 'full_name',
    2 => 'phone',
    3 => 'email',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `customer_id`,`full_name`,`phone`,`email`
            FROM `customers`
            WHERE `is_reseller` = '1'";
$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( full_name LIKE ? ";
    $where .= " OR `phone` LIKE ? ";
    $where .= " OR `email` LIKE ? ";
    $where .= " OR `customer_id` LIKE ? ) ";
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


    $data[0] = $row['customer_id'];
    $data[1] = '<a href="'.$site_url.'/customers/edit_reseller.php?customer_id='.$row['customer_id'].'">'.$row['full_name'].'</a></td>';
    $data[2] = $row['phone'];
    $data[3] = $row['email'];
    $data[4] = '<a href="'.$site_url.'/customers/reseller_customers.php?reseller_id='.$row['customer_id'].'">Customers</a>';
    $data[5] = '<a href="'.$site_url.'/statistics/reseller_child_customers_monthly.php?reseller_id='.$row['customer_id'].'">Monthly</a>';
    $data[6] = '<a href="'.$site_url.'/statistics/reseller_customers_monthly_new.php?reseller_id='.$row['customer_id'].'">Monthly</a>';
    $data[7] = '<a href="'.$site_url.'/customers/edit_discount.php?reseller_id='.$row['customer_id'].'"><i class="fa fa-pencil-square-o"></i></a>';

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
