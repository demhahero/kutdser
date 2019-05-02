<?php
include_once "../dbconfig.php";

$reseller_id=(isset($_GET["reseller_id"])?$_GET["reseller_id"]:0);

$year=(isset($_GET["year"])?$_GET["year"]:1990);

$month=(isset($_GET["month"])?$_GET["month"]:1);

$file_name="reseller_id_".$reseller_id."_year_".$year."_month_".$month;


/////////////// get total statistics


$sqlTot="SELECT IFNULL(sum(`total_with_tax`),0) AS `total_with_tax`,
IFNULL(sum(`subtotal`),0) AS `subtotal`,
IFNULL(sum(`commission_base_amount`),0) AS `commission_base_amount`,
IFNULL(sum(`monthly_commission`),0) AS `monthly_commission`
FROM
(SELECT DISTINCT `subtotal`.*,
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
`cable_subscriber`,
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
  WHERE (`invoice_items`.`item_name` LIKE '%Product%' OR `invoice_items`.`item_name` LIKE '%Refund%') AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
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
LEFT JOIN `customer_active_status` ON `customer_active_status`.`customer_id`= `subtotal`.`customer_id` AND `customer_active_status`.`order_id`=`subtotal`.`order_id`
) AS `statistics`";

$sqlRec = $sqlTot;




mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);

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


$stmt->execute();

$result = $stmt->get_result();

$row = mysqli_fetch_array($result);
////////// get all terminated order in month
$sqlquery="SELECT count(*) AS `total_terminated_per_month`  FROM `invoices` WHERE `invoice_type_id` = 6 AND `reseller_id` = ?
AND year(`valid_date_from`)=? AND month(`valid_date_from`)=?";
$stmt1 = $dbTools->getConnection()->prepare($sqlquery);
$stmt1->bind_param('sss',
                    $reseller_id,
                    $year,
                    $month);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = mysqli_fetch_array($result1);
////////// get all terminated order
$sqlquery2="SELECT count(*) AS `total_terminated`  FROM `invoices` WHERE `invoice_type_id` = 6 AND `reseller_id` = ?";
$stmt2 = $dbTools->getConnection()->prepare($sqlquery2);
$stmt2->bind_param('s',
                    $reseller_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = mysqli_fetch_array($result2);
////////// get all new and transfer order per month
$sqlquery3="SELECT
IFNULL(sum(IF(`order_options`.`cable_subscriber`='yes',1,0)),0) AS `total_transfer_per_month`
, IFNULL(sum(IF(`order_options`.`cable_subscriber`='no',1,0)),0) AS `total_new_per_month`
FROM `invoices`
LEFT JOIN `order_options`
ON `invoices`.`order_id`=`order_options`.`order_id`
WHERE `invoice_type_id` = 1
AND `reseller_id` = ?
AND Year(`valid_date_from`)=?
AND MONTH(`valid_date_from`)=?
";
$stmt3 = $dbTools->getConnection()->prepare($sqlquery3);
$stmt3->bind_param('sss',
                    $reseller_id,
                    $year,
                    $month);
$stmt3->execute();
$result3 = $stmt3->get_result();
$row3 = mysqli_fetch_array($result3);
////////// get all new and transfer order
$sqlquery4="SELECT
IFNULL(sum(IF(`order_options`.`cable_subscriber`='yes',1,0)),0) AS `total_transfer`
, IFNULL(sum(IF(`order_options`.`cable_subscriber`='no',1,0)),0) AS `total_new`
FROM `invoices`
LEFT JOIN `order_options`
ON `invoices`.`order_id`=`order_options`.`order_id`
WHERE `invoice_type_id` = 1
AND `reseller_id` = ?
";
$stmt4 = $dbTools->getConnection()->prepare($sqlquery4);
$stmt4->bind_param('s',
                    $reseller_id);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = mysqli_fetch_array($result4);
//////////////////////////////////
$total_data = array(
    "total_with_tax" => round((double)$row['total_with_tax'], 2),
    "subtotal" => round((double)$row['subtotal'], 2),
    "monthly_commission" => round((double)$row['monthly_commission'], 2),
    "commission_base_amount" => round((double)$row['commission_base_amount'], 2),
    "total_new" => $row4["total_new"],
    "total_transfer" => $row4["total_transfer"],
    "total_terminated" => $row2["total_terminated"],
    "total_terminated_per_month" => $row1["total_terminated_per_month"],
    "total_new_per_month" => $row3["total_new_per_month"],
    "total_transfer_per_month" => $row3["total_transfer_per_month"]
);
$totalData = '';
foreach ($total_data as $key => $value) {
  $totalValue='"' . $key . '"' . "\t".'"' . $value . '"' . "\t";
  $totalData .= $totalValue;
}


/////////////// end get total statistics
$sqlTot="SELECT DISTINCT `subtotal`.`customer_id`,
`customers`.`full_name`,
`products_details`.`item_name`,
`products_details`.`item_price`,
`cba`.`commission_base_amount`,
(`cba`.`commission_base_amount`*(IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`)/100)) AS `monthly_commission`,
`types_details`.`type_name`,
`subtotal`.`subtotal`,
`twt`.`total_with_tax`,
`payment_method`,
`start_active_date`,
IF(`order_options`.`cable_subscriber` LIKE 'yes','transfer','new') AS `join_type`,
IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`) AS `reseller_commission_percentage`

FROM (SELECT `order_id`,`customer_id`, sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'subtotal'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_name` NOT LIKE '%Tax%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `subtotal`
INNER JOIN (SELECT `order_id`, `customer_id`,sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'total_with_tax'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `twt` ON `twt`.`order_id` = `subtotal`.`order_id`

INNER JOIN (SELECT sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'commission_base_amount', `order_id`,`customer_id`
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE  (`invoice_items`.`item_name` LIKE '%Product%' OR `invoice_items`.`item_name` LIKE '%Refund%') AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`, `customer_id`
) AS `cba` ON `cba`.`order_id` = `subtotal`.`order_id`
INNER JOIN `order_options` ON `order_options`.`order_id` = `subtotal`.`order_id`
INNER JOIN `customers` AS `resellers` ON `resellers`.`customer_id` = ?
INNER JOIN (
SELECT `order_id`,`customer_id`,
GROUP_CONCAT(`invoice_items`.`item_name`) AS `item_name`,
GROUP_CONCAT(`invoice_items`.`item_price`) AS `item_price`,
GROUP_CONCAT(date(`invoices`.`valid_date_from`)) AS `valid_date_from`

  FROM `invoices`
  INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id`
  WHERE (`invoice_items`.`item_name` LIKE '%Product%' OR `invoice_items`.`item_name` LIKE '%Refund%') AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
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
LEFT JOIN `customer_active_status` ON `customer_active_status`.`customer_id`= `subtotal`.`customer_id` AND `customer_active_status`.`order_id`=`subtotal`.`order_id`
RIGHT JOIN `customers` ON `customers`.`customer_id` = `subtotal`.`customer_id`
WHERE `customers`.`reseller_id`=? ORDER BY `customers`.`full_name`";





mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);


$stmt->bind_param('sssssssssssssssss',
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
                $reseller_id);

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;


//mysqli_query($dbTools->getConnection(), "set names 'utf8'");
$columnHeader =
"ID" . "\t" .
"Full Name" . "\t" .
"Product" . "\t" .
"Product Price" . "\t" .
"Valid From" . "\t" .
"Commission base amount" . "\t" .
"Monthly commission" . "\t" .
"Type" . "\t" .
"Subtotal" . "\t" .
"total with Tax" . "\t" .
"Payment Method" . "\t" .
"Join Type" . "\t" .
"Start Active Date" . "\t" .
"Reseller Commission percentage";

$setData = $columnHeader."\n";
$count=0;
while ($rec = mysqli_fetch_row($queryTot)) {
    $rowData = '';
    foreach ($rec as $value) {
        $value = '"' . $value . '"' . "\t";
        $rowData .= $value;
    }
    $setData .= trim($rowData) . "\n";
    $count++;
    if($count%1000==0)
    {
      $setData.=trim($totalData)."\n";
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename='.$file_name.'.xls');
      print chr(255) . chr(254).mb_convert_encoding($setData, 'UTF-16LE', 'UTF-8');
      $setData="";
    }
}
// convert to UTF-16 and add BOM
$setData.=trim($totalData)."\n";
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='.$file_name.'.xls');
print chr(255) . chr(254).mb_convert_encoding($setData, 'UTF-16LE', 'UTF-8');
?>
