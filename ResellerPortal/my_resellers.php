<?php
include_once "header.php";
?>

<title>My Resellers</title>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>MAC Address</th>
    <th>Invoices</th>
    <th>Details</th>
</thead>
<tbody>
    <?php
    $query = $dbToolsReseller->query("select * from `customers` where `reseller_id` in (select `customer_id` from `customers` where `parent_reseller` = '".$reseller_id."')");
    while ($row = mysqli_fetch_array($query)) {
        ?>
        <tr>
            <td style="width: 7%;"><?php echo $row["customer_id"]; ?></td>
            <td style="width: 20%;"><?php echo $row["full_name"]; ?></td>
            <td style="width: 12%;"><?php echo $row["phone"]; ?></td>
            <td style="width: 20%;"><?php echo $row["email"]; ?></td>
            <td style="width: 12%;"><?php echo $row["mac_address"]; ?></td>
            <td style="width: 8%;">
                <?php
                if ($row["is_reseller"] == "1") {
                    echo "Reseller";
                } else {
                    ?>
                    <a href="customer_invoices.php?customer_id=<?php echo $row["customer_id"]; ?>">Invoices</a>
                    <?php
                }
                ?>
            </td>
            <td style="width: 8%;"><a href="customer_details.php?customer_id=<?php echo $row["customer_id"]; ?>">Details</a></td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>

<?php
include_once "footer.php";
?>
