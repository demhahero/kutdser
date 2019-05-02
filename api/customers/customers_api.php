<?php

include_once "../dbconfig.php";


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
    $where .= " ( `customers`.`full_name` LIKE ? ";
    $where .= " OR `resellers`.`full_name` LIKE ? ";
    $where .= " OR `customers`.`phone` LIKE ? ";
    $where .= " OR `customers`.`email` LIKE ? ";
    $where .= " OR `customers`.`customer_id` LIKE ? ) ";
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

$stmt = $dbTools->getConnection()->prepare($sqlTot);
if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt->bind_param('sssss',
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
$stmt1->bind_param('sssss',
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
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = '<a href="edit_customer.php?customer_id='.$row['customer_id'].'">'.$row['customer_id'].'</a>';
    $data[1] = '<a href="customer_details.php?customer_id='.$row['customer_id'].'">'.$row['full_name'].'</a>';
    $data[2] = $row['reseller_name'];
    $data[3] = $row['phone'];
    $data[4] = $row['email'];
    $data[5] = '<a href="customer_invoices.php?customer_id='.$row['customer_id'].'" class="btn btn-primary btn-xs"><i class="fa fa-file-archive-o"></i> Invoices </a>'
            .'<a href="customer_invoices_new.php?customer_id='.$row['customer_id'].'" class="btn btn-success btn-xs"><i class="fa fa-file-archive-o"></i> Invoices New </a>'
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
