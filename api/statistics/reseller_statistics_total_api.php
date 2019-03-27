<?php
include_once "../dbconfig.php";


$reseller_id=(isset($_POST["reseller_id"])?$_POST["reseller_id"]:0);

$year=(isset($_POST["year"])?$_POST["year"]:1990);

$month=(isset($_POST["month"])?$_POST["month"]:1);

$sqlTot="SELECT IFNULL(sum(`total_with_tax`),0) AS `total_with_tax`,
IFNULL(sum(`subtotal`),0) AS `subtotal`,
IFNULL(sum(`commission_base_amount`),0) AS `commission_base_amount`,
IFNULL(sum(`monthly_commission`),0) AS `monthly_commission`,
IFNULL(sum(IF(`cable_subscriber` LIKE 'yes',0,1)),0) AS `total_new`,
IFNULL(sum(IF(`cable_subscriber` LIKE 'yes',1,0)),0) AS `total_transfer`
FROM
(SELECT `subtotal`.*,
`twt`.`total_with_tax`,
`cba`.`commission_base_amount`,
(`cba`.`commission_base_amount`*(IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`)/100)) AS `monthly_commission`,
`customers`.`full_name`,
`order_options`.`reseller_commission_percentage` AS `customer_commission_percentage`,
IF(`order_options`.`reseller_commission_percentage`=-1,`resellers`.`reseller_commission_percentage`,`order_options`.`reseller_commission_percentage`) AS `reseller_commission_percentage`,
`products_details`.`item_name`,
`products_details`.`item_price`,
`products_details`.`valid_date_from`,
`products_details`.`type_name`,
 `order_options`.`cable_subscriber`


FROM (SELECT `order_id`,`customer_id`, sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'subtotal'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_name` NOT LIKE '%Tax%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `subtotal`
INNER JOIN (SELECT `order_id`, `customer_id`,sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'total_with_tax'
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `twt` ON `twt`.`order_id` = `subtotal`.`order_id`

INNER JOIN (SELECT sum(IF(`invoices`.`invoice_type_id`=0,`invoice_items`.`item_duration_price`*-1,`invoice_items`.`item_duration_price`)) AS 'commission_base_amount', `order_id`,`customer_id`
  FROM `invoices` INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id` WHERE `invoice_items`.`item_type` = 'duration' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`, `customer_id`
) AS `cba` ON `cba`.`order_id` = `subtotal`.`order_id`
INNER JOIN `customers` ON `customers`.`customer_id` = `subtotal`.`customer_id`
INNER JOIN `order_options` ON `order_options`.`order_id` = `subtotal`.`order_id`
INNER JOIN `customers` AS `resellers` ON `resellers`.`customer_id` = ?
INNER JOIN (
SELECT `order_id`,`customer_id`,
GROUP_CONCAT(`invoice_items`.`item_name`) AS `item_name`,
GROUP_CONCAT(`invoice_items`.`item_price`) AS `item_price`,
GROUP_CONCAT(date(`invoices`.`valid_date_from`)) AS `valid_date_from`,
GROUP_CONCAT(`invoice_types`.`type_name`) AS `type_name`

  FROM `invoices`
  INNER JOIN `invoice_items` ON `invoice_items`.`invoice_id`=`invoices`.`invoice_id`
  INNER JOIN `invoice_types` ON `invoice_types`.`invoic_type_id` = `invoices`.`invoice_type_id`
  WHERE `invoice_items`.`item_name` LIKE '%Product%' AND `reseller_id` = ?  AND Year(`valid_date_from`)=? and Month(`valid_date_from`)=? GROUP BY `order_id`,`customer_id`
) AS `products_details` ON  `products_details`.`order_id` = `subtotal`.`order_id`) AS `statistics`";

$sqlRec = $sqlTot;




mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);

$stmt->bind_param('sssssssssssss',
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
                    $month);


$stmt->execute();

$result = $stmt->get_result();


$row = mysqli_fetch_array($result);
$json_data = array(
    "total_with_tax" => round((double)$row['total_with_tax'], 2),
    "subtotal" => round((double)$row['subtotal'], 2),
    "monthly_commission" => round((double)$row['monthly_commission'], 2),
    "commission_base_amount" => round((double)$row['commission_base_amount'], 2),
    "total_new" => $row["total_new"],
    "total_transfer" => $row["total_transfer"]
);

echo $json = json_encode($json_data);
?>
