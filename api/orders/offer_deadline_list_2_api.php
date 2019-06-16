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
    `customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber`,
    `order_options`.`cancellation_date`, `order_options`.`installation_date_1`, `order_options`.`product_price`, `order_options`.`discount`
FROM `orders`
left JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
left JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
inner JOIN `invoices` on `invoices`.`order_id`=`orders`.`order_id` and `invoices`.`invoice_type_id` in (1,2,3)
and MONTH(`invoices`.`valid_date_from`)='" . date("m") . "' and YEAR(`invoices`.`valid_date_from`)='" . date("Y") . "'
left JOIN `customers` resellers on resellers.`customer_id` = `customers`.`reseller_id`
where `order_options`.discount >0 and `orders`.`order_id` not in (select `order_id` from `requests`
where `requests`.`order_id`=`orders`.`order_id` and `requests`.`action` in ('terminate', 'change_speed')) ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= "  and  ( customers.full_name LIKE ? ";
    $where .= " OR resellers.full_name LIKE ? ";
    $where .= " OR status LIKE ? ";
    $where .= " OR status LIKE ? ";
    $where .= " OR `orders`.order_id LIKE ?  ";
    $where .= " OR (((0x0000FFFF & `orders`.order_id) << 16) + ((0xFFFF0000 & `orders`.order_id) >> 16)) LIKE ?) ";
}

//concatenate search sql if value exist
if (isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}

//Orders
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'];

//Pagination
$sqlRec .= " LIMIT " . $params['start'] . " , " . $params['length'];

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);
if (isset($where) && $where != '') {
    $search_value = "%" . $params['search']['value'] . "%";
    $stmt->bind_param('ssssss', $search_value, $search_value, $search_value, $search_value, $search_value, $search_value);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);
if (isset($where) && $where != '') {
    $search_value = "%" . $params['search']['value'] . "%";
    $stmt1->bind_param('ssssss', $search_value, $search_value, $search_value, $search_value, $search_value, $search_value
    ); // 's' specifies the variable type => 'string'
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data = [];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {
    if ((int) $row["order_id"] > 10380)
        $displayed_order_id = (((0x0000FFFF & (int) $row["order_id"]) << 16) + ((0xFFFF0000 & (int) $row["order_id"]) >> 16));
    else
        $displayed_order_id = $row["order_id"];

    $data[0] = '<a href="order_details.php?order_id=' . $row['order_id'] . '" >' . $displayed_order_id . '</a>';
    $data[1] = '<a href="' . $site_url . '/customers/customer_details.php?customer_id=' . $row['customer_id'] . '">' . $row['customer_name'] . '</a>';
    $data[2] = $row['reseller_name'];
    $data[3] = $row['product_title'];


    $start_active_date = "";
    if ($row["product_category"] === "phone") {
        $start_active_date = $row["creation_date"];
    } else if ($row["product_category"] === "internet") {
        if ($row["cable_subscriber"] === "yes") {
            $start_active_date = $row["cancellation_date"];
        } else {
            $start_active_date = $row["installation_date_1"];
        }
    }
    $row["start_active_date"] = $start_active_date;

    $converted = DateTime::createFromFormat("Y-m-d H:i:s", $row["start_active_date"]);
    $start_active_date = clone $converted;
    $converted1Year = $converted->add(new DateInterval("P1Y"));

    if ($converted1Year->format("d") != "1")
        $converted1Year->modify('first day of next month');

    $original_price = ((float)$row['product_price'])/(1-((float)$row['discount'])/100);


    $data[4] = $start_active_date->format("Y-m-d");
    $data[5] = $converted1Year->format("Y-m-d");
    $data[6] = round($original_price, 2);
    $data[7] = $row['discount']."%";
    $data[8] = $row['product_price'];
    $data[9] = '<a target="_blank" href="' . $api_url . 'print/print_order_test.php?order_id='
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
