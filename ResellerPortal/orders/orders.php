<?php
include_once "../header.php";
?>

<title>Orders</title>
<div class="page-header">
    <h4>Orders</h4>    
</div>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Customer</th>
    <th>Product</th>
    <th>Date</th>
    <th>Status</th>
    <th>Make request</th>
    <th>Request</th>
    <th>Print</th>
</thead>
<tbody>
    <?php
    $orders = $dbTools->query("SELECT `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,
    orders.product_title,orders.product_category,orders.product_subscription_type,resellers.full_name as 'reseller_name',
    `customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`, `order_options`.`cable_subscriber`,
    requests.`product_title` as 'request_product_title'
FROM `orders` 
inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
left JOIN `requests` on requests.`order_id` = `orders`.`order_id` and requests.`verdict`='approve'
where `orders`.reseller_id='".$reseller_id."'");
    while($row = $dbTools->fetch_assoc($orders)) {
        if ((int) $row["order_id"] > 10380)
            $displayed_order_id = (((0x0000FFFF & (int) $row["order_id"]) << 16) + ((0xFFFF0000 & (int) $row["order_id"]) >> 16));
        else
            $displayed_order_id = $row["order_id"];
        ?>
        <tr>
            <td style="width: 5%;"><a href="order_details.php?order_id=<?= $row["order_id"] ?>" ><?= $displayed_order_id ?></a></td>
            <td style="width: 25%;">
                <?php
                echo $row["customer_name"];
                ?>
            </td>
            <td style="width: 25%;"><?php if($row["request_product_title"] == "") echo $row["product_title"]; else echo $row["request_product_title"]; ?></td>
            <td style="width: 20%;"><?= $row["creation_date"] ?></td>
            <td style="width: 10%;"><?= $row["status"] ?></td>
            <td style="width: 10%;">
                <a href="<?= $site_url ?>/requests/make_request.php?order_id=<?= $row["order_id"] ?>">Make request</a>
            </td>
            <td><?= $row["request_product_title"] ?></td>
            <td style="width: 10%;">
                <a href="<?= $site_url ?>/shop/print_order.php?order_id=<?= $row["order_id"] ?>">Print</a>
            </td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>

<?php
include_once "../footer.php";
?>