<?php
include_once "../header.php";
?>

<?php
$order_id = intval($_GET["order_id"]);

$creation_date = new DateTime();

if (isset($_POST["action"])) {

    $action_on_date = new DateTime(($_POST["action_on_date"]));

    $product = $dbTools->objProductTools($_POST["action_value"]);

    $requestTools = $dbTools->objRequestTools(null);
    $requestTools->setReseller($dbTools->objCustomerTools($reseller_id));
    $requestTools->setAction(($_POST["action"]));
    $requestTools->setActionValue(($_POST["action_value"]));
    $requestTools->setNote(($_POST["note"]));
    $requestTools->setOrder($dbTools->objOrderTools($order_id));
    $requestTools->setCreationDate($creation_date);
    $requestTools->setActionOnDate($action_on_date);

    $requestTools->setProductPrice($product->getPrice());
    $requestTools->setProductTitle($product->getTitle());
    $requestTools->setProductCategory($product->getCategory());
    $requestTools->setProductSubscriptionType($product->getSubscriptionType());

    $request_result = $requestTools->doInsert();

    if ($request_result) {
        echo "<div class='alert alert-success'>Request sent!</div>";
        //header('Location: '.$site_url.'/requests/requests.php');
    }
}
?>

<title>Make a request</title>

<script>
    $(document).ready(function () {
        $("select[name=\"action\"]").change(function () {
            if (this.value == "change_speed") {
                $(".action-value").show();
            } else {
                $(".action-value").hide();
            }
        });

        $(".submit").click(function () {

            var order_id = "<?= $order_id ?>";
            var action_on_date = $("input[name=\"action_on_date\"]").val();
            var note = $("textarea[name=\"note\"]").val();
            var reseller_id = "<?= $reseller_id ?>";
            var action_value = $("select[name=\"product_id\"]").val();
            var action = $("select[name=\"action\"]").val();
            var product_id = $("select[name=\"product_id\"]").val();
            $.post("<?= $api_url ?>insert_requests_api.php", {order_id: order_id, action: action, product_id: product_id, action_on_date: action_on_date, note: note, reseller_id: reseller_id}, function (data, status) {
                data = $.parseJSON(data);
                if (data.inserted == true){
                    alert("Request sent");
                    location.href = "requests.php";
                }
                else
                    alert("Error, try again");
            });
            return false;
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
            <option value="change_speed">Change speed</option>
            <option value="terminate">Terminate</option>
        </select> 
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" class="form-control datepicker" /> 
    </div>
    <div class="form-group action-value">
        <label>Speed:</label>
        <select name="product_id" class="form-control">
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
    <input type="submit" class="btn btn-default submit"  value="Send">
</form>

<?php
include_once "../footer.php";
?>