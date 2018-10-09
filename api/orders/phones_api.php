<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'order_id',
    1 => '`customers`.`full_name`',
    2 => '`resellers`.`full_name`',
    3 => '`orders`.product_title',
    4 => '`orders`.creation_date',
    5 => '`orders`.status',
    6 => 'print'
);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,
    orders.product_title,orders.product_category,orders.product_subscription_type,resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber`
FROM `orders`
inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` where `product_title` like '%phone%' ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ( customers.full_name LIKE ? ";
    $where .= " OR resellers.full_name LIKE ? ";
    $where .= " OR product_title LIKE ? ";
    $where .= " OR status LIKE ? ";
    $where .= " OR `orders`.order_id LIKE ?  ";
    $where .= " OR (((0x0000FFFF & `orders`.order_id) << 16) + ((0xFFFF0000 & `orders`.order_id) >> 16)) LIKE ? ) ";
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
    if ((int) $row["order_id"] > 10380)
        $displayed_order_id = (((0x0000FFFF & (int) $row["order_id"]) << 16) + ((0xFFFF0000 & (int) $row["order_id"]) >> 16));
    else
        $displayed_order_id = $row["order_id"];

    $data[0] = '<a href="order_details.php?order_id=' . $row['order_id'] . '" >' . $displayed_order_id . '</a>';
    $data[1] = '<a href="' . $site_url . '/customers/customer_details.php?customer_id='.$row['customer_id'].'">'.$row['customer_name'].'</a>';
    $data[2] = $row['reseller_name'];
    $data[3] = $row['product_title'];
    $data[4] = $row['creation_date'];
    $data[5] = $row['status'];
    $data[6] = '<a target="_blank" href="' . $site_url . '/orders/print_order.php?order_id='
            . $row['order_id'] . '" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print </a>';
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
