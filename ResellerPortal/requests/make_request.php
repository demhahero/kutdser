<?php
include_once "../header.php";
?>

<?php
$order_id = intval($_GET["order_id"]);

$creation_date = new DateTime();

if (isset($_POST["action"])) {
    
    $action_on_date = new DateTime(mysql_real_escape_string($_POST["action_on_date"]));
    
    $requestTools = $dbTools->objRequestTools(null);
    $requestTools->setReseller($dbTools->objCustomerTools($reseller_id));
    $requestTools->setAction(mysql_real_escape_string($_POST["action"]));
    $requestTools->setActionValue(mysql_real_escape_string($_POST["action_value"]));
    $requestTools->setNote(mysql_real_escape_string($_POST["note"]));
    $requestTools->setOrder($dbTools->objOrderTools($order_id));
    $requestTools->setCreationDate($creation_date);
    $requestTools->setActionOnDate($action_on_date);
    
    $request_result = $requestTools->doInsert();

    if ($request_result) {
        echo "<div class='alert alert-success'>Request sent!</div>";
        header('Location: '.$site_url.'/requests/requests.php');
    }
}
?>

<title>Make a request</title>

<script>
    $(document).ready(function () {
        $("select[name=\"action\"]").change(function () {
            if (this.value == "upgrade" || this.value == "downgrade") {
                $(".action-value").show();
            } else if (this.value == "cancel") {
                $(".action-value").hide();
            }
        });
    });
</script>
<form class="register-form" method="post">
    <div class="form-group">
        <label>Customer:</label>
        <?php
        $order_result = $conn_routers->query("select * from `orders` where `order_id`='" . $order_id . "'");
        if ($order_row = $order_result->fetch_assoc()) {

            $customer_sql = "select * from `customers` where `customer_id`='" . $order_row["customer_id"] . "'";
            $customer_result = $conn_routers->query($customer_sql);

            if ($customer_result->num_rows > 0) {
                $customer_row = $customer_result->fetch_assoc();
                echo $full_name = $customer_row["full_name"];
            }
        }
        ?>
    </div>
    <div class="form-group">
        <label>Order ID:</label>
        <?= $order_id ?>
    </div>
    <div class="form-group">
        <label>Action:</label>
        <select name="action" class="form-control">
            <option value="upgrade">Upgrade</option>
            <option value="downgrade">Downgrade</option>
            <option value="cancel">Cancel</option>
        </select> 
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" class="form-control datepicker" /> 
    </div>
    <div class="form-group action-value">
        <label>Speed:</label>
        <select name="action_value" class="form-control">
            <option price='29.9' value='383'>Internet 5 Mbps ($29.9)</option>
            <option price='34.9' value='335'>Internet 10 Mbps ($34.9)</option>	
            <option price='39.9' value='380'>Internet 15 Mbps ($39.9)</option>	
            <option price='44.9' value='381'>Internet 20 Mbps ($44.9)</option>	
            <option price='49.9' value='414'>Internet 30 Mbps ($49.9)</option>	
            <option price='59.9' value='416'>Internet 60 Mbps ($59.9)</option>	
            <option price='79.9' value='418'>Internet 120 Mbps ($79.9)</option>	
            <option price='99.9' value='419'>Internet 200 Mbps ($99.9)</option>	
            <option price='159.9' value='420'>Internet 940 Mbps ($159.9)</option>
        </select> 
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea name="note" class="form-control"></textarea> 
    </div>
    <input type="submit" class="btn btn-default"  value="Send">
</form>

<?php
include_once "../footer.php";
?>