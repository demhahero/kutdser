<?php
include_once "header.php";
?>


<?php
if (isset($_GET["customer_log_id"])) {
    if ($_GET["do"] == "delete") {
        $result = mysql_query("DELETE FROM `customer_log` WHERE `customer_log_id` = '" . $_GET["customer_log_id"] . "'");
        if ($result)
            echo "<div class='alert alert-success'>done</div>";
    } else if ($_GET["customer_log_id"] == "edit") {
        
    }
}

$customer_id = "";
if (isset($_GET["customer_id"])) {
    $customer_id = (int) $_GET["customer_id"];
}

if (isset($_POST["completion"])) {
    $result = mysql_query("update `customer_log` SET `completion` = '" . $_POST["completion"] . "' WHERE `customer_log_id` = '" . $_POST["customer_log_id"] . "'");
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}


//Add log
if (isset($_POST["type"])) {
    $result = mysql_query("INSERT INTO `customer_log` (
                                `customer_id` ,
        			`type` ,
				`note` ,
                                `completion` ,
				`log_date`
				)
				VALUES (
                                '" . $customer_id . "',"
            . "'" . $_POST["type"] . "',"
            . " '" . mysql_real_escape_string($_POST["note"]) . "',"
            . " '" . mysql_real_escape_string($_POST["completion"]) . "',"
            . " '" . $_POST["log_date"] . "'
				);");
}

$customer_query = mysql_query("select * from `customers` where `customer_id`='" . $customer_id . "'");
$customer_row = mysql_fetch_array($customer_query);
?>
<title>Customer: <?= $customer_row["full_name"] ?></title>

<div class="page-header">
    <h4>Customer: <?= $customer_row["full_name"] ?></h4>    
</div>

<form class="register-form form-inline" method="post">
    <div class="form-group">
        <label>Type:</label>
        <select  style="width: 130px;"  name="type" class="form-control">
            <option value="upgrade">upgrade</option>
            <option value="downgrade">downgrade</option>
            <option value="suspend">suspend</option>
            <option value="pending">pending</option>
            <option value="technical problem">technical problem</option>

        </select>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea  style="width: 300px;" name="note" value="" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label >Due Date:</label>
        <input style="width: 130px;" name="log_date" value="<?= date("Y-m-d")?>" class="form-control datepicker"/>
    </div>
    <div class="form-group">
        <label >Completion:</label>
        <select  style="width: 130px;"  name="completion" class="form-control">
            <option value="0">Incomplete</option>
            <option value="1">Complete</option>
        </select>
    </div>
    <input type="submit" class="btn btn-primary" value="Add">
</form>
<br>
<br>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Type</th>
    <th>Note</th>
    <th>Due Date</th>
    <th>Completion</th>
    <th>Functions</th>
</thead>
<tbody>
    <?php
    $query = mysql_query("select * from `customer_log` where `customer_id`='" . $customer_id . "'");
    while ($row = mysql_fetch_array($query)) {
        ?>
        <tr>
            <td style="width: 5%;">
                <?php echo $row["customer_log_id"]; ?>
            </td>
            <td style="width: 15%;">
                <?php echo $row["type"]; ?>
            </td>
            <td style="width: 30%;">
                <?php echo $row["note"]; ?>
            </td>
            <td style="width: 9%;">
                <?php echo $row["log_date"]; ?>
            </td>
            <td style="width: 9%;">
                <form action="" class="status-form" method="post">
                    <select name="completion" class="completion">
                        <option <?php if ($row["completion"] == "0") echo "selected"; ?> value="0">Incomplete</option>
                        <option <?php if ($row["completion"] == "1") echo "selected"; ?> value="1">Complete</option>
                    </select>
                    <input name="customer_log_id" hidden="" value="<?= $row["customer_log_id"]; ?>">
                </form>
            </td>
            <td class="functions" style="width: 5%;">
                <span class="functions">
                    <a href="edit_customer_log.php?customer_log_id=<?php echo $row["customer_log_id"]; ?>&customer_id=<?php echo $row["customer_id"]; ?>">
                        <img title="Edit" width="30px" src="img/edit-icon.png" />
                    </a>
                    <a href="customer_details.php?do=delete&customer_log_id=<?php echo $row["customer_log_id"]; ?>&customer_id=<?php echo $row["customer_id"]; ?>">
                        <img title="Remove" width="30px" src="img/delete-icon.png" />
                    </a>
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
        $("select.completion").change(function () {
            $("form.status-form").submit();
        });
    })
</script>
<?php
include_once "footer.php";
?>

