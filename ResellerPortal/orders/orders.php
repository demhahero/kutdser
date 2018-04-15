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
    <th>Date</th>
    <th>Status</th>
    <th>Make request</th>
    <th>Print</th>
</thead>
<tbody>
    <?php
    $orders = $dbTools->order_query("select * from `orders` where `reseller_id`='" . $reseller_id . "'", 3);
    foreach ($orders as $order) {
        ?>
        <tr>
            <td style="width: 5%;"><a href="order_details.php?order_id=<?= $order->getOrderID() ?>" ><?= $order->getDisplayedID() ?></a></td>
            <td style="width: 25%;">
                <?php
                echo $order->getCustomer()->getFullName();
                ?>
            </td>
            <td style="width: 30%;"><?= $order->getCreationDate()->format("Y-m-d") ?></td>
            <td style="width: 30%;"><?= $order->getStatus() ?></td>
            <td style="width: 30%;">
                <a href="<?= $site_url ?>/requests/make_request.php?order_id=<?= $order->getOrderID() ?>">Make request</a>
            </td>
            <td style="width: 30%;">
                <a href="<?= $site_url ?>/shop/print_order.php?order_id=<?= $order->getOrderID() ?>">Print</a>
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