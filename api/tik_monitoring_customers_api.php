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
    4 => '`modems`.`mac_address`',
    5 => '`modems`.`router_mac_address`',
    6 => '`modems`.`ip_address`',
    7 => '`vl_number`',
    8 => '`customers`.`address`',
    9 => '`merchantrefs`.`merchantref`'
);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `merchantrefs`.`merchantref`, `merchantrefs`.`order_id`, 
    `merchantrefs`.`is_credit`, `customers`.`customer_id` , `customers`.`phone` , customers.address, 
    customers.email, customers.full_name, orders.order_id, resellers.full_name AS 'reseller_name',
    `orders`.`vl_number`,
    customers.reseller_id, `modems`.`mac_address`, `modems`.`modem_id`, `modems`.`ip_address`, `modems`.`router_mac_address`
FROM customers
LEFT JOIN `customers` resellers ON resellers.`customer_id` = customers.`reseller_id`
LEFT JOIN orders ON orders.customer_id = customers.customer_id
LEFT JOIN modems ON orders.customer_id = modems.customer_id
LEFT JOIN `merchantrefs` on `merchantrefs`.`customer_id` = `customers`.`customer_id` and `merchantrefs`.`type`!='payment' ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( `customers`.`full_name` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `resellers`.`full_name` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `merchantref` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`customer_id` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`phone` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `customers`.`address` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `orders`.`vl_number` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `modems`.`ip_address` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `modems`.`router_mac_address` LIKE '%" . $params['search']['value'] . "%' ";
    $where .= " OR `modems`.`mac_address` LIKE '%" . $params['search']['value'] . "%')  ";
    
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


echo $sqlRec;

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");
$queryTot = mysqli_query($dbTools->getConnection(), $sqlTot);

$totalRecords = mysqli_num_rows($queryTot);

$queryRecords = mysqli_query($dbTools->getConnection(), $sqlRec);

//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = '<a href="customer_details.php?customer_id=' . $row['customer_id'] . '" >' . $row['customer_id'] . '</a>';
    $data[1] = $row['full_name'];
    $data[2] = $row['reseller_name'];
    $data[3] = $row['phone'];
    $data[4] = $row['mac_address'];
    $data[5] = $row['router_mac_address'];
    $data[6] = $row['ip_address'];
    $data[7] = $row['vl_number'];
    $data[8] = $row['address'];
    $data[9] = $row['merchantref'];
    
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