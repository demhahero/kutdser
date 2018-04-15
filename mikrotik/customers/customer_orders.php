<?php
include_once "../header.php";
?>

<?php
$customer_id = intval($_GET["customer_id"]);
$orders = $dbTools->order_query("select * from `orders` where `customer_id`='" . $customer_id . "'", 3);
?>

<title>Customer's Orders</title>
<div class="page-header">
    <a href="customers.php">Customers</a> 
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href=""><?= $orders[0]->getCustomer()->getFullName(); ?>'s orders</a> 
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Product</th>
    <th>Creation Date</th>
    <th>Make request</th>
</thead>

<tbody>
    <?php
    
    foreach ($orders as $order) {
        ?>
        <tr>
            <td style="width: 7%;">
                <a href="../orders/order_details.php?order_id=<?=$order->getOrderID()?>">
                    <?=$order->getDisplayedID()?>
                </a>
            </td>
            <td style="width: 25%;"><?=$order->getProduct()->getTitle()?></td>
            <td style="width: 20%;"><?=$order->getCreationDate()->format("Y-m-d")?></td>
            <td style="width: 15%;">
                <a href="../requests/make_request.php?order_id=<?=$order->getOrderID()?>">
                    Make Request
                </a>
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