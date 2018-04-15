<?php
include_once "../header.php";
?>

<?php
$reseller_id = intval($_GET["reseller_id"]);
$reseller = $dbTools->objCustomerTools($reseller_id, 1, array("customer", "order", "product"));
?>

<title><?= $reseller->getFullName(); ?>'s customers</title>
<div class="page-header">  
    <a href="resellers.php">Resellers</a> 
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href=""><?= $reseller->getFullName(); ?>'s customers</a>  
</div>

<a href="reseller_customers_monthly_generateXLS.php?reseller_id=<?=$reseller_id?>" class="btn btn-primary">XML</a>

<br><br>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Product</th>
    <th>Start Date</th>
    <th>Recurring Start</th>
    <th>total</th>
    <th>total w/ Tax</th>
</thead>
<tbody>
    
    <?php
    $total_with_tax = 0;
    $total_without_tax = 0;
    foreach ($reseller->getResellerCustomers() as $customer) {
        $total_price_after_tax = 0;
        $total_fees_by_month = 0;
        $start_date = null;
        $recurring_start_date = null;
        ?>
        <tr>
            <td style="width: 5%;"><?= $customer->getCustomerID() ?></td>
            <td style="width: 25%;"><?= $customer->getFullName() ?></td>
            <td style="width: 20%;">
                <?php
                $total_fees_by_month = $customer->getTotalFeesByMonth(new DateTime("28-2-2018"));
                
                foreach ($customer->getOrders() as $order) {
                    
                    $total_price_after_tax = $order->getProductPrice() + $qst_tax + $gst_tax;
                    $product_price = $order->getProductPrice();
                    $start_date = $order->getStartDate()->format("Y:m:d");
                    $recurring_start_date = $order->getRecurringStartDate()->format("Y:m:d");
                    echo $order->getProduct()->getTitle(); 
                }
                ?>
            </td>
            <td style="width: 15%;"><?=$start_date?></td>
            <td style="width: 15%;"><?=$recurring_start_date?></td>
            <td style="width: 10%;">
                <?php
                $total_without_tax += $total_fees_by_month;
                echo number_format((float) $total_fees_by_month, 2, '.', '') . "$";
                ?>
            </td>
            <td style="width: 10%;">
                <?php
                $qst_tax = $total_fees_by_month * 0.09975;
                $gst_tax = $total_fees_by_month * 0.05;
                $total_fees_by_month+=$gst_tax+$qst_tax;
                $total_with_tax += $total_fees_by_month;
                echo number_format((float) $total_fees_by_month, 2, '.', '') . "$";
                ?>
            </td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>
<?php
echo "Total without tax:" . number_format((float) $total_without_tax, 2, '.', '') . "$";
echo "<br>";
echo "Total with tax:" . number_format((float) $total_with_tax, 2, '.', '') . "$";
?>
<?php
include_once "../footer.php";
?>