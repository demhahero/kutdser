<?php
include_once "../header.php";
?>

<title>Customers</title>
<div class="page-header">
    <h4>Customers</h4>    
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Invoices</th>
    <th>Orders</th>
</thead>
<tbody>
    <?php
    $customers = $dbTools->customer_query("select * from `customers` where `reseller_id` = '".$reseller_id."'");
    foreach ($customers as $customer) {
        ?>
        <tr>
            <td style="width: 5%;"><?=$customer->getCustomerID()?></td>
            <td style="width: 45%;"><?=$customer->getFullName()?></td>
            <td style="width: 15%;"><?=$customer->getPhone()?></td>
            <td style="width: 30%;"><?=$customer->getEmail()?></td>
            <td style="width: 5%;">
                <a href="customer_invoices.php?customer_id=<?=$customer->getCustomerID()?>">Invoices</a>
            </td>
            <td style="width: 5%;">
                <a href="customer_orders.php?customer_id=<?=$customer->getCustomerID()?>">Orders</a>
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