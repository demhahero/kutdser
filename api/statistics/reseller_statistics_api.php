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

$sqlTot="SELECT DISTINCT `subtotal`.*,
`twt`.`total_with_tax`,
`cba`.`commission_base_amount`,
(`cba`.`commission_base_amount`*(IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`)/100)) AS `monthly_commission`,
`customers`.`full_name`,
`order_options`.`reseller_commission_percentage` AS `customer_commission_percentage`,
IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`) AS `reseller_commission_percentage`,
`products_details`.`item_name`,
`products_details`.`item_price`,
`products_details`.`valid_date_from`,
`types_details`.`type_name`,
`payment_method`,
`start_active_date`,
IF(`order_options`.`cable_subscriber` LIKE 'yes','transfer','new') AS `join_type`


FROM (SELECT `order_id`,`customer_id`, sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'subtotal'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_name` NOT LIKE '%Tax%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `subtotal`
INNER JOIN (SELECT `order_id`, `customer_id`,sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'total_with_tax'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `twt` ON `twt`.`order_id` = `subtotal`.`order_id`

INNER JOIN (SELECT sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'commission_base_amount', `order_id`,`customer_id`
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE  (`invoice_items`.`item_name` LIKE '%Product%' OR `invoice_items`.`item_name` LIKE '%Refund%') AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`, `customer_id`
) AS `cba` ON `cba`.`order_id` = `subtotal`.`order_id`
INNER JOIN `customers` ON `customers`.`customer_id` = `subtotal`.`customer_id`
INNER JOIN `order_options` ON `order_options`.`order_id` = `subtotal`.`order_id`
INNER JOIN `customers` AS `resellers` ON `resellers`.`customer_id` = ?
INNER JOIN (
SELECT `order_id`,`customer_id`,
GROUP_CONCAT(`invoice_items`.`item_name`) AS `item_name`,
GROUP_CONCAT(`invoice_items`.`item_price`) AS `item_price`,
GROUP_CONCAT(date(`invoices`.`valid_date_from`)) AS `valid_date_from`

  FROM `invoices`
  INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id`
  WHERE `invoice_items`.`item_name` LIKE '%Product%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `products_details` ON  `products_details`.`order_id` = `subtotal`.`order_id`
LEFT JOIN (
  SELECT
`order_id`,`customer_id`,
GROUP_CONCAT(`types`.`type_name`) AS `type_name`
FROM (
SELECT `invoices`.`invoice_id`, `order_id`,`customer_id`,`invoice_types`.`type_name`

  FROM `invoices`
  INNER JOIN `invoice_types` ON `invoice_types`.`invoic_type_id` = `invoices`.`invoice_type_id`
  WHERE `reseller_id` = ?
  AND Year(`valid_date_from`)=?
  AND Month(`valid_date_from`)=?
  ORDER BY `invoices`.`customer_id`,`invoices`.`order_id`,`invoices`.`invoice_id`
) AS `types`
  GROUP BY `order_id`,`customer_id`
) AS `types_details` ON  `types_details`.`order_id` = `subtotal`.`order_id`
INNER JOIN (SELECT `customer_id`,
IF(`merchantref` LIKE '%cache%','Cash on delivery','VISA') AS `payment_method` FROM `merchantrefs` ORDER BY `merchantref` ASC ) AS `payments_method` ON `payments_method`.`customer_id`=`subtotal`.`customer_id`
LEFT JOIN `customer_active_status` ON `customer_active_status`.`customer_id`= `subtotal`.`customer_id` AND `customer_active_status`.`order_id`=`subtotal`.`order_id`";


// $sqlTot = "SELECT `subtotal`.*,`twt`.`total_with_tax`,`cba`.`commission_base_amount`,`customers`.`full_name`
// FROM (SELECT `order_id`,`customer_id`, sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'subtotal'
//   FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_name` NOT LIKE '%Tax%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
// ) AS `subtotal`
// INNER JOIN (SELECT `order_id`, `customer_id`,sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'total_with_tax'
//   FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
// ) AS `twt` ON `twt`.`order_id` = `subtotal`.`order_id`
//
// INNER JOIN (SELECT sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'commission_base_amount', `order_id`,`customer_id`
//   FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_type` = 'duration' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`, `customer_id`
// ) AS `cba` ON `cba`.`order_id` = `subtotal`.`order_id`
// INNER JOIN `customers` ON `customers`.`customer_id` = `subtotal`.`customer_id`";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( `subtotal`.`order_id` LIKE ? ";
    $where .= " OR `subtotal`.`customer_id` LIKE ? ";
    $where .= " OR `customers`.`full_name` LIKE ? ";
    $where .= " OR `payment_method` LIKE ? ";
    $where .= " OR `type_name` LIKE ? ) ";
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

$stmt->bind_param('sssssssssssssssssssss',
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{

  $stmt->bind_param('ssssssssssssssss',
                    $reseller_id,
                    $year,
                    $month,
                    $reseller_id,
                    $year,
                    $month,
                    $reseller_id,
                    $year,
                    $month,
                  $reseller_id,
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
$stmt1->bind_param('sssssssssssssssssssss',
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $reseller_id,
                  $year,
                  $month,
                  $reseller_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt1->bind_param('ssssssssssssssss',
  $reseller_id,
  $year,
  $month,
  $reseller_id,
  $year,
  $month,
  $reseller_id,
  $year,
  $month,
  $reseller_id,
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

    $data[0] = '<a href="customer_invoices.php?order_id='.$row['order_id'].'&year='.$year.'&month='.$month.'" >'.$row['customer_id'].'</a>';
    $data[1] = $row['full_name'];
    $data[2] = $row['item_name'];
    $data[3] = $row['item_price'];
    $data[4] = $row['valid_date_from'];
    $data[5] = round((double)$row['commission_base_amount'], 2);
    $data[6] = round((double)$row['monthly_commission'], 2);
    $data[7] = $row['type_name'];
    $data[8] = round((double)$row['subtotal'], 2);
    $data[9] = round((double)$row['total_with_tax'], 2);
    $data[10] = $row['payment_method'];
    $data[11] = $row['start_active_date'];
    $data[12] = $row['join_type'];
    $data[13] = "%".$row['reseller_commission_percentage']
    .'<a data-id="'.$row['order_id'].'" data-id-2="'.$row['customer_commission_percentage'].'" type="button" class="btn btn-danger change_commission" >Change</a>';
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
