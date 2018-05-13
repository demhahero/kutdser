<?php
include_once "../../header.php";
?>

<title>Resellers</title>
<div class="page-header">
    <a class="last" href="">Resellers</a>
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Customers</th>
    <th>Statistics</th>
</thead>
<tbody>
    <?php
    $customers = $dbTools->customer_query("select * from `customers` where `is_reseller` = '1'");
    foreach ($customers as $customer) {
        ?>
        <tr>
            <td style="width: 5%;"><?=$customer->getCustomerID()?></td>
            <td style="width: 45%;"><?=$customer->getFullName()?></td>
            <td style="width: 15%;"><?=$customer->getPhone()?></td>
            <td style="width: 30%;"><?=$customer->getEmail()?></td>
            <td style="width: 5%;">
                <a href="reseller_customers.php?reseller_id=<?=$customer->getCustomerID()?>">Customers</a>
            </td>
            <td style="width: 5%;">
                <a href="<?=$site_url?>/statistics/statistics_v2/reseller_customers_monthly.php?reseller_id=<?=$customer->getCustomerID()?>">Monthly</a>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>

<?php
include_once "../../footer.php";
?>
