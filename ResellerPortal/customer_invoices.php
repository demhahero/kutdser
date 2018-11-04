<?php
include_once "header.php";
?>

<?php
$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));

$sql = "select * from `customers` where `customer_id`='" . $customer_id . "'";
$result = $dbToolsReseller->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $full_name = $row["full_name"];
    $address = $row["address"];
    $product_id = $row["product_id"];
    $start_date = strtotime($row["start_date"]);
}
?>
<title><?= $full_name ?> - Invoices</title>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>Year - Month</th>
    <th>Invoice</th>
</thead>
<tbody>
    <?php
    $year = date('Y');

    $extra = 2;
    if (date('m', $start_date) == "1" || date('m', $start_date) == "01")
        $extra = 1;

    for ($year = date('Y', $start_date); $year <= date('Y'); $year++) {
        if ($year == date('Y', $start_date)) {
            for ($month = date('m', $start_date) + $extra; $month <= 12; $month++) {
                ?>
                <tr>
                    <td><?= $year . " - " . $month ?></td>
                    <td><a target="_blank" href="print_order.php?month=<?= $month ?>&year=<?= $year ?>&customer_id=<?= $_GET["customer_id"] ?>">Print</a></td>
                </tr>

                <?php
            }
        } else {
            for ($month = 1; $month <= date("m"); $month++) {
                ?>
                <tr>
                    <td><?= $year . " - " . $month ?></td>
                    <td><a target="_blank" href="print_order.php?month=<?= $month ?>&year=<?= $year ?>&customer_id=<?= $_GET["customer_id"] ?>">Print</a></td>
                </tr>
                <?php
            }
        }
    }
    ?>
</tbody>
</table>

<?php
include_once "footer.php";
?>
