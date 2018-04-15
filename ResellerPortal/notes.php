<?php
include_once "header.php";
?>

<title>Notes</title>

<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Customer</th>
    <th>Text</th>
    <th>Status</th>
    <th>Date</th>
</thead>
<tbody>
    <?php
    $query = mysql_query("select * from `notes` where `customer_id` in "
            . "( select `customer_id` from `customers` where `reseller_id` ='" . $reseller_id . "') "
            . "ORDER BY `note_id` desc");
    while ($row = mysql_fetch_array($query)) {
        ?>
        <tr>
            <td><?php echo $row["note_id"]; ?></td>
            <td>
                <?php
                $query_cutomer = mysql_query("select * from `customers` where `customer_id` = '" . $row["customer_id"] . "'");
                $row_customer = mysql_fetch_array($query_cutomer);
                echo $row_customer["full_name"];
                ?>
            </td>
            <td><?php echo $row["text"]; ?></td>
            <td><?php echo $row["status"]; ?></td>
            <td><?php echo $row["datetime"]; ?></td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>


<?php
include_once "footer.php";
?>