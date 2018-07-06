<?php

include_once "dbconfig.php";

    
// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'customer_id',
    1 => '`customers`.`full_name`',
    2 => '`resellers`.`full_name`',
    3 => '`customers`.phone',
    4 => '`customers`.email',
    5 => 'invoices',
    6 => 'orders',
    7 => 'status'
);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `customers`.`customer_id`,`customers`.`phone`,customers.address,customers.email,customers.full_name,orders.order_id,resellers.full_name as 'reseller_name',customers.reseller_id 
FROM customers INNER JOIN `customers` resellers on resellers.`customer_id` = customers.`reseller_id`
LEFT JOIN orders on orders.customer_id=customers.customer_id ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( `customers`.`full_name` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `resellers`.`full_name` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`phone` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`email` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`customer_id` LIKE '%" . $params['search']['value'] . "%' ) ";
}

//concatenate search sql if value exist
if (isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}

//Orders
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'];

//Pagination
$sqlRec .= " LIMIT " . $params['start'] . " ," . $params['length'];

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");
$queryTot = mysqli_query($dbTools->getConnection(), $sqlTot);

$totalRecords = mysqli_num_rows($queryTot);

$queryRecords = mysqli_query($dbTools->getConnection(), $sqlRec);

//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {
    
    $data[0] = $row['customer_id'];
    $data[1] = '<a href="customer_details.php?customer_id='.$row['customer_id'].'">'.$row['full_name'].'</a>';
    $data[2] = $row['reseller_name'];
    $data[3] = $row['phone'];
    $data[4] = $row['email'];
    $data[5] = '<a href="customer_invoices.php?customer_id='.$row['customer_id'].'" class="btn btn-primary btn-xs"><i class="fa fa-file-archive-o"></i> Invoices </a>'
            . '<a href="customer_orders.php?customer_id='.$row['customer_id'].'" class="btn btn-info btn-xs"><i class="fa fa-credit-card"></i> Orders </a>'
            . '<a href="customer_by_month.php?customer_id='.$row['customer_id'].'" class="btn btn-danger btn-xs"><i class="fa fa-bar-chart"></i> Status </a>';
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