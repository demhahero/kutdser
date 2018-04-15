<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "header.php";
?>

<?php

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[rand(0, $max)];
    }
    return $str;
}

$date = new DateTime();

if (isset($_POST["full_name"])) {
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    if ($_POST["is_reseller"] != "1")
        $_POST["is_reseller"] = "0";

    $result = mysql_query("INSERT INTO `customers` (
        			`username` ,
				`password` ,
				`full_name` ,
				`address` ,
				`email`,
				`phone`,
				`stripe_id`,
                                `product_id`,
                                `start_date`,
                                `is_reseller`,
                                `reseller_id`,
                                `actual_installation_date`,
                                `actual_installation_time_from`,
                                `actual_installation_time_to`,
                                `completion`,
                                `note`,
                                `join_type`,
                                `ip_address`,
                                `is_new_system`,
                                `session_id`,
				`order_id`
				)
				VALUES (
            '" . mysql_real_escape_string($_POST["username"]) . "',"
            . " '" . mysql_real_escape_string($password) . "',"
            . " '" . mysql_real_escape_string($_POST["full_name"]) . "',"
            . " '" . mysql_real_escape_string($_POST["address"]) . "',"
            . " '" . mysql_real_escape_string($_POST["email"]) . "',"
            . " '" . mysql_real_escape_string($_POST["phone"]) . "',"
            . " '" . mysql_real_escape_string($_POST["stripe_id"]) . "',"
            . " '" . mysql_real_escape_string($_POST["product_id"]) . "',"
            . " '" . mysql_real_escape_string($_POST["start_date"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["is_reseller"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["reseller_id"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["actual_installation_date"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["actual_installation_time_from"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["actual_installation_time_to"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["completion"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["note"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["join_type"]) . "' ,"
            . " '" . mysql_real_escape_string($_POST["ip_address"]) . "' ,"
            . " '1' ,"
            . " '" . random_str(32) . "' ,"
            . " '" . mysql_real_escape_string($_POST["order_id"]) . "'
				);");
    $customer_id = mysql_insert_id();
    if ($result) {
        //header("Location: customers.php");
        //die();
        echo "<div class='alert alert-success'>done</div>";
    }
}




$sql = "select * from `customers` where `is_reseller`='1'";
$result = $connection->query($sql);
?>

<title>Create Customer</title>
<div class="page-header">
    <h4>Create Customer</h4>    
</div>
<form class="register-form" method="post">
    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" value="" class="form-control" placeholder="Username"/>
    </div>
    <div class="form-group">
        <label >Password:</label>
        <input type="text" name="password" value="" class="form-control" placeholder="Password"/>
    </div>
    <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="" class="form-control" placeholder="Full Name"/>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="text" name="email" value="" class="form-control" placeholder="Email"/>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" class="form-control" placeholder="Phone"/>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <textarea type="text" name="address" class="form-control" /></textarea>
    </div>
    <div class="form-group">
        <label>Stripe ID:</label>
        <input type="text" name="stripe_id" class="form-control" placeholder="Stripe ID"/>
    </div>
    <div class="form-group">
        <label>Order ID:</label>
        <input type="text" name="order_id" class="form-control" placeholder="order ID"/>
    </div>
    <div class="form-group">
        <label>Product ID:</label>
        <input type="text" name="product_id" class="form-control" placeholder="product ID"/>
    </div>
    <div class="form-group">
        <label>Joining Date:</label>
        <input type="text" name="start_date" value="<?= date("Y-m-d") ?>" class="form-control datepicker" placeholder="Joining Date"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Date:</label>
        <input type="text" name="actual_installation_date" value="<?= date("Y-m-d") ?>" class="form-control datepicker" placeholder="actual installation date"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Time From:</label>
        <input type="text" name="actual_installation_time_from" value="09:00am" class="form-control" placeholder="actual installation time from"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Time To:</label>
        <input type="text" name="actual_installation_time_to" value="03:00pm" class="form-control" placeholder="actual installation time to"/>
    </div>
    <div class="form-group">
        <label for="email">Completion:</label>
        <input type="text" name="completion" value="" class="form-control" placeholder="Completion"/>
    </div>
    <div class="form-group">
        <label for="email">IP Address:</label>
        <input type="text" name="ip_address" value="" class="form-control" placeholder="IP Address"/>
    </div>
    <div class="form-group">
        <label for="email">Note:</label>
        <textarea type="text" name="note" class="form-control" /></textarea>
    </div>
    <div class="form-group">
        <label>Join Type:</label>
        <select  name="join_type" class="form-control">
            <option value="new">New</option>
            <option value="transfer">Transfer</option>
        </select>
    </div>
    <div class="form-group">
        <label>Is Reseller:</label>
        <input type="checkbox" name="is_reseller" value="1" class="form-control" />
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["customer_id"] . "'>" . $row["full_name"] . "</option>";
                }
            }
            ?>   
        </select>
    </div>
    <input type="submit" class="btn btn-default" value="create">
</form>

<?php
include_once "footer.php";
