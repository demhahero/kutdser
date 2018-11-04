<?php
include_once "header.php";
?>

<?php
$date = date("Y-m-d h:i:sa");

$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));
if (isset($_POST["text"])) {

    $result = $dbToolsReseller->query("INSERT INTO `notes` (
        			`customer_id` ,
				`text` ,
				`datetime`,
                                `status`
				)
				VALUES (
            '" . $customer_id . "',"
            . " '" . mysql_real_escape_string($_POST["text"]) . "',"
            . " '" . $date . "',"
            . " 'waiting'
				);");

    if ($result) {
        $query_cutomer = $dbToolsReseller->query("select * from `customers` where `customer_id` = '" . $customer_id . "'");
        $row_customer = mysql_fetch_array($query_cutomer);

        $to = 'info@amprotelecom.com';
        $subject = 'New Note from ' . $row_customer["full_name"];
        $message = "There is a new note has been sent from " . $row_customer["full_name"]
                . "\r\n \r\n \r\n Note Body: \r\n \r\n " . mysql_real_escape_string($_POST["text"]);
        $headers = 'From: ' . $row_customer["email"] . "\r\n" .
                'Reply-To: ' . $row_customer["email"] . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
        echo "<div class='alert alert-success'>Note sent</div>";
    }
}
?>

<title>Send a note</title>

<form class="register-form" method="post">
    <div class="form-group">
        <label>Customer:</label>
        <?php
        $query_cutomer = $dbToolsReseller->query("select * from `customers` where `customer_id` = '" . $customer_id . "'");
        $row_customer = mysql_fetch_array($query_cutomer);
        echo $row_customer["full_name"];
        ?>
    </div>

    <div class="form-group">
        <label>Body:</label>
        <textarea name="text" class="form-control"></textarea>
    </div>
    <input type="submit" class="btn btn-default"  value="Send">
</form>

<?php
include_once "footer.php";
