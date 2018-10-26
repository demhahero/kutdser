<?php

//include connection file
include_once "../dbconfig.php";


$resellersqlTot = "SELECT `full_name`
            FROM `customers`
            WHERE `is_reseller` = '1' AND `customer_id`=?";
$reseller = $dbTools->getConnection()->prepare($resellersqlTot);
$search_id=$_POST["data_id"];
$reseller->bind_param('s',
                $search_id );

$reseller->execute();

$resellerresult1 = $reseller->get_result();

$reseller_row = mysqli_fetch_array($resellerresult1);
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
            WHERE `is_reseller` = '0' AND `reseller_id`=?";
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

$search_id=$_POST["data_id"];



if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt->bind_param('sssss',
                  $search_id ,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value );

}
else {
  $stmt->bind_param('s',
                  $search_id );
}
$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);
$search_id=$_POST["data_id"];


if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt1->bind_param('sssss',
                  $search_id ,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value
                  ); // 's' specifies the variable type => 'string'
}
else{
  $stmt1->bind_param('s',
                  $search_id );
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = $row['customer_id'];
    $data[1] = $row['full_name'];
    $data[2] = $row['phone'];
    $data[3] = $row['email'];
    $data[4] = '<a href="'.$site_url.'/customers/customer_invoices.php?customer_id='.$row['customer_id'].'">Invoices</a>';
    $data[5] = '<a href="'.$site_url.'/customers/customer_orders.php?customer_id='.$row['customer_id'].'">Orders</a>';
    $data[6] = '<a href="'.$site_url.'/statistics/reseller_customers_monthly_new.php?reseller_id='.$row['customer_id'].'">Monthly</a>';

    $all_data[] = $data;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $all_data ,  // total data array
    "reseller_full_name"=>$reseller_row["full_name"]
);

echo $json = json_encode($json_data);
?>
