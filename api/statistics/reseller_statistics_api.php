<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'order_id',
    1 => 'customer_id',
    2 => 'commission_base_amount',
    3 => 'subtotal',
    4 => 'total_with_tax',

);

$where = $sqlTot = $sqlRec = "";

$reseller_id=(isset($_POST["reseller_id"])?$_POST["reseller_id"]:0);

$year=(isset($_POST["year"])?$_POST["year"]:1990);

$month=(isset($_POST["month"])?$_POST["month"]:1);


$sqlTot = "SELECT `subtotal`.*,`twt`.`total_with_tax`,`cba`.`commission_base_amount`
FROM (SELECT `order_id`,`customer_id`, sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'subtotal'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_name` NOT LIKE '%Tax%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `subtotal`
INNER JOIN (SELECT `order_id`, `customer_id`,sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'total_with_tax'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `twt` ON `twt`.`order_id` = `subtotal`.`order_id`

INNER JOIN (SELECT sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'commission_base_amount', `order_id`,`customer_id`
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_type` = 'duration' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`, `customer_id`
) AS `cba` ON `cba`.`order_id` = `subtotal`.`order_id`";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( order_id LIKE ? ";
    $where .= " OR customer_id LIKE ? ";
    $where .= " OR total_with_tax LIKE ? ) ";
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
$stmt->bind_param('ssssssssssss',
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt->bind_param('sssssssss',
                    $reseller_id,
                    $year,
                    $month,
                    $reseller_id,
                    $year,
                    $month,
                    $reseller_id,
                    $year,
                    $month);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt1->bind_param('ssssssssssss',
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt1->bind_param('sssssssss',
  $reseller_id,
  $year,
  $month,
  $reseller_id,
  $year,
  $month,
  $reseller_id,
  $year,
  $month);
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = '<a href="customer_invoices.php?order_id='.$row['order_id'].'&year='.$year.'&month='.$month.'" >'.$row['order_id'].'</a>';
    $data[1] = $row['customer_id'];
    $data[2] = round((double)$row['commission_base_amount'], 2);
    $data[3] = round((double)$row['subtotal'], 2);
    $data[4] = round((double)$row['total_with_tax'], 2);
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
