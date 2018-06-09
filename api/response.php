<?php

//include connection file 
include_once "dbconfig.php";

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
    6 => '`order_options`.`cable_subscriber`',
    7 => 'print'
);

$where = $sqlTot = $sqlRec = "";


$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
    "product_title" => "product_title",
    "product_category" => "product_category",
    "modem_mac_address" => "modem_mac_address",
    "product_subscription_type" => "product_subscription_type",
    "cable_subscriber" => "cable_subscriber",
    "displayed_order_id" => "order_id"
);
$childFields = array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
);
$child2Fields = array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);


$sqlTot = "SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,
    orders.product_title,orders.product_category,orders.product_subscription_type,resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber` 
FROM `orders` 
inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( customers.full_name LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `orders`.order_id LIKE '%" . $params['search']['value'] . "%' ) ";
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
    $data[0] = '<a href="order_details.php?order_id=' . $row['order_id'] . '" >' . $row['order_id'] . '</a>';
    $data[1] = '<a href="' . $site_url . '/edit_customer.php?customer_id=' . $row['customer_id'] . '">' . $row['customer_name'] . '</a>';
    $data[2] = '<a href="' . $site_url . '/edit_customer.php?customer_id=' . $row['reseller_id'] . '">' . $row['reseller_name'] . '</a>';
    $data[3] = $row['product_title'];
    $data[4] = $row['creation_date'];
    $data[5] = $row['status'];
    $data[6] = $row['cable_subscriber'];
    $data[7] = '<a target="_blank" href="' . $site_url . '/orders/print_order.php?order_id=' 
            . $row['order_id'] . '" ><img src="' . $site_url . '/img/print-icon.png" style="width: 25px;" /></a>';
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
	