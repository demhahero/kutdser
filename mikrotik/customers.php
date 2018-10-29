<?php
include_once "header.php";
?>

<?php
$type = "1";
if (isset($_GET["type"])) {
    if ($_GET["type"] == "2")
        $type = '';
}

if (isset($_GET["customer_id"])) {
    $result = mysqli_query($connection,"DELETE FROM `customers` WHERE `customer_id` = '" . $_GET["customer_id"] . "'");
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title><?php if($type == '') echo "Customers"; else echo "Resellers";?></title>
<div class="page-header">
    <h4><?php if($type == '') echo "Customers"; else echo "Resellers";?></h4>
</div>
<a href="create_customer.php" class="btn btn-primary">+ Create 1</a>

<br><br>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>IP Address</th>
    <th>Join Type</th>
    <th>Functions</th>
</thead>
<tbody>
    <?php
    $query = mysqli_query($connection,"select * from `customers` where `is_reseller`='" . $type . "'");
    while ($row = mysqli_fetch_array($query)) {
        ?>
        <tr>
            <td style="width: 5%;"><?php echo $row["customer_id"]; ?></td>
            <td>
                <?php
                if ($row["is_reseller"] == "1") {
                    echo "<a href='reseller_details.php?customer_id=" . $row["customer_id"] . "'>" . $row["full_name"] . "</a>";
                } else {
                    echo "<a href=\"customer_details.php?customer_id=" . $row["customer_id"] . "\">" . $row["full_name"] . "</a>";
                }
                ?>
            </td>
            <td style="width: 10%;"><?php echo $row["phone"]; ?></td>
            <td style="width: 12%;"><?php echo $row["ip_address"]; ?></td>
            <td style="width: 6%;">
                <?php echo $row["join_type"]; ?>
            </td>
            <td class="functions" style="width: 12%;">
                <span class="functions">
                    <?php
                    if ($row["is_reseller"] == "1") {
                        echo "<a href='reseller_details.php?customer_id=" . $row["customer_id"] . "'><img title='Reseller' width='30px' src='img/reseller-icon.png' /></a>";
                    } else {
                        ?>
                        <a href="customer_invoices.php?customer_id=<?php echo $row["customer_id"]; ?>"><img title="Invoices" width="30px" src="img/invoice-icon.png" /></a></a>
                        <?php
                    }
                    ?>

                    <a href="edit_customer.php?customer_id=<?php echo $row["customer_id"]; ?>"><img title="Edit" width="30px" src="img/edit-icon.png" /></a>
                    <a class="check-alert" href="customers.php?do=delete&customer_id=<?php echo $row["customer_id"]; ?>"><img title="Remove" width="30px" src="img/delete-icon.png" /></a>
                </span>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>

<?php
include_once "footer.php";
?>
