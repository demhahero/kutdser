<?php
include_once "header.php";
?>

<?php
if (isset($_POST["full_name"])) {
   
    if(strlen($_POST["termination_date"]) > 5)
        $termination_date = "'" . mysql_real_escape_string($_POST["termination_date"]) . "'";
    else 
        $termination_date = "NULL";

    if ($_POST["password"] != "" && $_POST["password"] != " ") {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        
        if ($_POST["is_reseller"] != "1") {
            $_POST["is_reseller"] = "0";
        }
        $result = mysql_query("Update `customers` set
                                    `username` = '" . mysql_real_escape_string($_POST["username"]) . "',
                                    `password` = '" . mysql_real_escape_string($password) . "',
                                    `full_name` = '" . mysql_real_escape_string($_POST["full_name"]) . "',
                                    `email` = '" . mysql_real_escape_string($_POST["email"]) . "',
                                    `phone` = '" . mysql_real_escape_string($_POST["phone"]) . "',
                                    `address` = '" . mysql_real_escape_string($_POST["address"]) . "',
                                    `stripe_id` = '" . mysql_real_escape_string($_POST["stripe_id"]) . "',
                                    `order_id` = '" . mysql_real_escape_string($_POST["order_id"]) . "',
                                    `start_date` = '" . mysql_real_escape_string($_POST["start_date"]) . "',
                                    `reseller_id` = '" . mysql_real_escape_string($_POST["reseller_id"]) . "',
                                    `parent_reseller` = '" . mysql_real_escape_string($_POST["parent_reseller"]) . "',
                                    `is_reseller` = '" . mysql_real_escape_string($_POST["is_reseller"]) . "',
                                    `actual_installation_date` = '" . mysql_real_escape_string($_POST["actual_installation_date"]) . "',
                                    `actual_installation_time_from` = '" . mysql_real_escape_string($_POST["actual_installation_time_from"]) . "',
                                    `actual_installation_time_to` = '" . mysql_real_escape_string($_POST["actual_installation_time_to"]) . "', 
                                    `completion` = '" . mysql_real_escape_string($_POST["completion"]) . "', 
                                    `note` = '" . mysql_real_escape_string($_POST["note"]) . "',
                                    `join_type` = '" . mysql_real_escape_string($_POST["join_type"]) . "', 
                                    `ip_address` = '" . mysql_real_escape_string($_POST["ip_address"]) . "',
                                    `termination_date` = ".$termination_date.",
                                    `product_id` = '" . mysql_real_escape_string($_POST["product_id"]) . "'
                                    where `customer_id` = '" . mysql_real_escape_string($_GET["customer_id"]) . "';
                                    ");
    } else {
        if ($_POST["is_reseller"] != "1") {
            $_POST["is_reseller"] = "0";
        }
        $result = mysql_query("Update `customers` set
                                    `username` = '" . mysql_real_escape_string($_POST["username"]) . "',
                                    `full_name` = '" . mysql_real_escape_string($_POST["full_name"]) . "',
                                    `email` = '" . mysql_real_escape_string($_POST["email"]) . "',
                                    `phone` = '" . mysql_real_escape_string($_POST["phone"]) . "',
                                    `address` = '" . mysql_real_escape_string($_POST["address"]) . "',
                                    `stripe_id` = '" . mysql_real_escape_string($_POST["stripe_id"]) . "',
                                    `order_id` = '" . mysql_real_escape_string($_POST["order_id"]) . "',
                                    `start_date` = '" . mysql_real_escape_string($_POST["start_date"]) . "',
                                    `reseller_id` = '" . mysql_real_escape_string($_POST["reseller_id"]) . "',
                                    `parent_reseller` = '" . mysql_real_escape_string($_POST["parent_reseller"]) . "',    
                                    `is_reseller` = '" . mysql_real_escape_string($_POST["is_reseller"]) . "',
                                    `actual_installation_date` = '" . mysql_real_escape_string($_POST["actual_installation_date"]) . "',
                                    `actual_installation_time_from` = '" . mysql_real_escape_string($_POST["actual_installation_time_from"]) . "',
                                    `actual_installation_time_to` = '" . mysql_real_escape_string($_POST["actual_installation_time_to"]) . "', 
                                    `completion` = '" . mysql_real_escape_string($_POST["completion"]) . "', 
                                    `note` = '" . mysql_real_escape_string($_POST["note"]) . "',
                                    `join_type` = '" . mysql_real_escape_string($_POST["join_type"]) . "', 
                                    `ip_address` = '" . mysql_real_escape_string($_POST["ip_address"]) . "',
                                    `termination_date` = ".$termination_date.",
                                    `product_id` = '" . mysql_real_escape_string($_POST["product_id"]) . "'
                                    where `customer_id` = '" . mysql_real_escape_string($_GET["customer_id"]) . "';
                                    ");
    }

    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}

$query = mysql_query("select * from `customers` where `customer_id`='" . $_GET["customer_id"] . "'");
$row = mysql_fetch_array($query);

$sql = "select * from `customers` where `is_reseller`='1'";
$reseller_result = $connection->query($sql);

$reseller_parent_result = $connection->query($sql);
?>

<title>Edit Customer</title>
<div class="page-header">
    <h4>Edit Customer</h4>    
</div>
<form class="register-form" method="post">

    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" value="<?= $row["username"]; ?>" class="form-control" placeholder="Username"/>
    </div>
    <div class="form-group">
        <label >Password:</label>
        <input type="text" name="password" value="" class="form-control" placeholder="Password"/>
    </div>
    <div class="form-group">
        <label for="email">Full Name:</label>
        <input type="text" name="full_name" value="<?= $row["full_name"]; ?>" class="form-control" placeholder="Full Name"/>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" value="<?= $row["email"]; ?>" class="form-control" placeholder="Email"/>
    </div>
    <div class="form-group">
        <label for="email">Phone:</label>
        <input type="text" name="phone" value="<?= $row["phone"]; ?>" class="form-control" placeholder="Phone"/>
    </div>
    <div class="form-group">
        <label for="email">Address:</label>
        <textarea type="text" name="address" class="form-control" /><?= $row["address"]; ?></textarea>
    </div>
    <div class="form-group">
        <label for="email">Stripe ID:</label>
        <input type="text" name="stripe_id" value="<?= $row["stripe_id"]; ?>" class="form-control" placeholder="Stripe ID"/>
    </div>
    <div class="form-group">
        <label for="email">Order ID:</label>
        <input type="text" name="order_id" value="<?= $row["order_id"]; ?>" class="form-control" placeholder="order ID"/>
    </div>
    <div class="form-group">
        <label for="email">Product ID:</label>
        <input type="text"  name="product_id" value="<?= $row["product_id"]; ?>" class="form-control" placeholder="product ID"/>
<?php
$servername = "localhost";
$username = "i3702914_wp1";
$password = "D@fH(9@QUrGOC7Ki5&*61]&0";
$dbname = "i3702914_wp1";

// Create connection
$conn_wordpress = new mysqli($servername, $username, $password, $dbname);
$sql_customer = "SELECT
  wpp.ID,
  wpp.post_title,
  wppm.meta_key AS FIELD,
  wppm.meta_value AS VALUE,
  wppm.*
FROM wp_posts AS wpp
  LEFT JOIN wp_postmeta AS wppm
    ON wpp.ID = wppm.post_id
WHERE wpp.post_type = 'product'
      AND (wppm.meta_key = '_regular_price')
      AND wpp.id='" . $row["product_id"] . "'
ORDER BY wpp.ID ASC, FIELD ASC, wppm.meta_id DESC;";

            $result_customer = $conn_wordpress->query($sql_customer);

            if ($result_customer->num_rows > 0) {
                $row_customer = $result_customer->fetch_assoc();

                echo "<span style='color:red;'><b>".$row_customer["post_title"]."</b></span>";
                
            }
?>
    </div>
    <div class="form-group">
        <label for="email">Joining Date:</label>
        <input type="text" name="start_date" value="<?= $row["start_date"]; ?>" class="form-control datepicker" placeholder="Joining Date"/>
    </div>
    <div class="form-group">
        <label for="email">IP Address:</label>
        <input type="text" name="ip_address" value="<?= $row["ip_address"]; ?>" class="form-control" placeholder="IP Address"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Date:</label>
        <input type="text" name="actual_installation_date" value="<?= $row["actual_installation_date"]; ?>" class="form-control datepicker" placeholder="actual installation date"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Time From:</label>
        <input type="text" name="actual_installation_time_from" value="<?= $row["actual_installation_time_from"]; ?>" class="form-control" placeholder="actual installation time from"/>
    </div>
    <div class="form-group">
        <label for="email">Actual Installation Time To:</label>
        <input type="text" name="actual_installation_time_to" value="<?= $row["actual_installation_time_to"]; ?>" class="form-control" placeholder="actual installation time to"/>
    </div>
    <div class="form-group">
        <label for="email">Completion:</label>
        <input type="text" name="completion" value="<?= $row["completion"]; ?>" class="form-control" placeholder="Completion"/>
    </div>
    <div class="form-group">
        <label for="email">Note:</label>
        <textarea type="text" name="note" class="form-control" /><?= $row["note"]; ?></textarea>
    </div>
    <div class="form-group">
        <label>Join Type:</label>
        <select  name="join_type" class="form-control">
            <option value="new">New</option>
            <option <?php if ($row["join_type"] == "transfer") echo "selected"; ?> value="transfer">Transfer</option>
        </select>
    </div>
    <div class="form-group">
        <label for="email">Is Reseller:</label>
        <input type="checkbox" name="is_reseller" <?php if ($row["is_reseller"]) echo "checked"; ?> value="1" class="form-control" />
    </div>
    <div class="form-group">
        <label for="email">Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            if ($reseller_result->num_rows > 0) {
                while ($reseller = $reseller_result->fetch_assoc()) {
                    if ($reseller["customer_id"] == $row["reseller_id"])
                        echo "<option selected value='" . $reseller["customer_id"] . "'>" . $reseller["full_name"] . "</option>";
                    else
                        echo "<option value='" . $reseller["customer_id"] . "'>" . $reseller["full_name"] . "</option>";
                }
            }
            ?>   
        </select>
    </div>
    <div class="form-group">
        <label for="email">Parent Reseller:</label>
        <select  name="parent_reseller" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            if ($reseller_parent_result->num_rows > 0) {
                while ($reseller = $reseller_parent_result->fetch_assoc()) {
                    if ($reseller["customer_id"] == $row["parent_reseller"])
                        echo "<option selected value='" . $reseller["customer_id"] . "'>" . $reseller["full_name"] . "</option>";
                    else
                        echo "<option value='" . $reseller["customer_id"] . "'>" . $reseller["full_name"] . "</option>";
                }
            }
            ?>   
        </select>
    </div>
    <div class="form-group">
        <label for="email">Termination Date:</label>
        <input type="text" name="termination_date" value="<?= $row["termination_date"]; ?>" class="form-control datepicker" placeholder="Termination Date"/>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>

<?php
include_once "footer.php";
?>