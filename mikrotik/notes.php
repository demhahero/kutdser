<?php
include_once "header.php";
?>

<?php
mysql_query("Update `notes` set `is_read`='1'");

if (isset($_GET["note_id"])) {
    $result = mysql_query("DELETE FROM `notes` WHERE `note_id` = '" . $_GET["note_id"] . "'");
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}

if (isset($_POST["status"])) {
    $result = mysql_query("update `notes` SET `status` = '" . $_POST["status"] . "' WHERE `note_id` = '" . $_POST["note_id"] . "'");
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Notes</title>
<div class="page-header">
    <h4>Notes</h4>    
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Customer</th>
    <th>Text</th>
    <th>Status</th>
    <th>Date</th>
    <th>Remove</th>
</thead>
<tbody>
    <?php
    $query = mysql_query("select * from `notes` ORDER BY `note_id` desc");
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
            <td>
                <form action="" class="status-form" method="post">
                    <select name="status" class="status">
                        <option <?php if ($row["status"] == "waiting") echo "selected"; ?> value="waiting">waiting</option>
                        <option <?php if ($row["status"] == "in processing") echo "selected"; ?> value="in processing">in processing</option>
                        <option <?php if ($row["status"] == "done") echo "selected"; ?> value="done">done</option>
                    </select>
                    <input name="note_id" hidden="" value="<?= $row["note_id"]; ?>">
                </form>
            </td>
            <td><?php echo $row["datetime"]; ?></td>
            <td>
                <span class="functions">
                    <a href="notes.php?do=delete&note_id=<?php echo $row["note_id"]; ?>"><img title="Remove" width="30px" src="img/delete-icon.png" /></a>
                </span>
            </td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>
<script>
    $(document).ready(function () {
        $("body").on("change", "select.status" ,function () {
            $(this).parent().parent().parent().find("form.status-form").submit();
        });
    })
</script>

<?php
include_once "footer.php";
?>