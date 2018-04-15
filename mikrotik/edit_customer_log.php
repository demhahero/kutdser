<?php
include_once "header.php";
?>

<?php
if (isset($_POST["type"])) {

        $result = mysql_query("Update `customer_log` set
                                    `type` = '" . $_POST["type"] . "',
                                    `note` = '" . mysql_real_escape_string($_POST["note"]) . "',
                                    `log_date` = '" . $_POST["log_date"] . "',
                                    `completion` = '" . $_POST["completion"] . "'
                                    where `customer_log_id` = '" . $_GET["customer_log_id"] . "';
                                    ");


    if ($result)
        echo "<script>window.location.href = \"customer_details.php?customer_id=".$_GET["customer_id"]."\";</script>";
}

$query = mysql_query("select * from `customer_log` where `customer_log_id`='" . $_GET["customer_log_id"] . "'");
$row = mysql_fetch_array($query);

?>

<title>Edit Log</title>
<div class="page-header">
    <h4>Edit Log</h4>    
</div>
<form class="register-form" method="post">

    <div class="form-group">
        <label>Type:</label>
        <select  style="width: 130px;"  name="type" class="form-control">
            <option value="upgrade">upgrade</option>
            <option <?php if ($row["completion"] == "downgrade") echo "selected"; ?> value="downgrade">downgrade</option>
            <option <?php if ($row["completion"] == "suspend") echo "selected"; ?> value="suspend">suspend</option>
            <option <?php if ($row["completion"] == "pending") echo "selected"; ?> value="pending">pending</option>
            <option <?php if ($row["completion"] == "technical problem") echo "selected"; ?> value="technical problem">technical problem</option>
        </select>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea  style="width: 300px;" name="note" class="form-control"><?=$row["note"]?></textarea>
    </div>
    <div class="form-group">
        <label >Due Date:</label>
        <input style="width: 130px;" name="log_date" value="<?=$row["log_date"]?>" class="form-control datepicker"/>
    </div>
    <div class="form-group">
        <label >Completion:</label>
        <select  style="width: 130px;"  name="completion" class="form-control">
            <option <?php if ($row["completion"] == "0") echo "selected"; ?> value="0">Incomplete</option>
            <option <?php if ($row["completion"] == "1") echo "selected"; ?> value="1">Complete</option>
        </select>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>

<?php
include_once "footer.php";
?>