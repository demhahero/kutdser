<?php
include_once "../header.php";
?>

<?php
$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));
$customer = $dbToolsReseller->objCustomerTools($customer_id);
?>
<title><?= $customer->getFullName() ?> - Invoices</title>
<div class="page-header">
    <h4><?= $customer->getFullName() ?> - Invoices</h4>
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>Year - Month</th>
    <th>Invoice</th>
</thead>
<tbody>
    <?php
    if ($customer->getSubscriptionType() == "monthly") {
        $start = $customer->getRecurringStartDate()->modify('first day of this month');
        $end = (new DateTime())->modify('first day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            ?>
            <tr>
                <td><?= $dt->format("Y-m") ?></td>
                <td>
                    <a target="_blank" href="print_customer_recurring_invoice.php?month=<?= $dt->format("m") ?>&year=<?= $dt->format("Y") ?>&customer_id=<?= $customer_id ?>">
                        Print
                    </a>
                </td>
            </tr>

            <?php
        }
    }
    ?>
</tbody>
</table>

<?php
include_once "../footer.php";
?>
