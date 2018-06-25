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
        $(".moving-field").hide();
        $("select[name=\"action\"]").change(function () {
            if (this.value == "change_speed") {
                $(".action-value").show();
            } else {
                $(".action-value").hide();
            }
            if (this.value == "moving") {
                $(".moving-field").show();
            } else {
                $(".moving-field").hide();
            }
        });

        $(".submit").click(function () {

            var order_id = "<?= $order_id ?>";
            var action_on_date = $("input[name=\"action_on_date\"]").val();
            var note = $("textarea[name=\"note\"]").val();
            var reseller_id = "<?= $reseller_id ?>";
            var action_value = $("select[name=\"product_id\"]").val();
            var action = $("select[name=\"action\"]").val();
            var modem_mac_address = $("input[name=\"modem_mac_address\"]").val();
            //// moving fields
            var city = $("input[name=\"city\"]").val();
            var address_line_1 = $("input[name=\"address_line_1\"]").val();
            var address_line_2 = $("input[name=\"address_line_2\"]").val();
            var postal_code = $("input[name=\"postal_code\"]").val();

            var product_id = $("select[name=\"product_id\"]").val();
            $.post("<?= $api_url ?>insert_requests_api.php",
                    {
                        order_id: order_id,
                        action: action,
                        product_id: product_id,
                        action_on_date: action_on_date,
                        modem_mac_address: modem_mac_address,
                        city: city,
                        address_line_1: address_line_1,
                        address_line_2: address_line_2,
                        postal_code: postal_code,
                        note: note,
                        reseller_id: reseller_id}, function (data, status) {
                data = $.parseJSON(data);
                if (data.inserted == true) {
                    alert("Request sent");
                    location.href = "requests.php";
                } else {
                    if (data.error !== "null")
                        alert(data.error);
                    else
                        alert("Error, try again");
                }

            });
            return false;
        });

        $.getJSON("<?= $api_url ?>insert_requests_api.php?order_id=<?= $order_id ?>&do=product_list", function (result) {
            $.each(result['products'], function (i, field) {
                $("select.product-list").append("<option price='" + field['price'] + "' value='"
                        + field['product_id'] + "'>" + field['title'] + " (" + field['price'] + ")</option>");
            });
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
            <option value="moving">Moving</option>
            <option value="terminate">Terminate</option>
        </select>
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" class="form-control datepicker" />
    </div>
    <div class="form-group moving-field">
        <label>City:</label>
        <input type="text" name="city" class="form-control"/>

    </div>
    <div class="form-group moving-field">
        <label>New Address:</label>
        <input type="text" name="address_line_1" class="form-control"/>

    </div>
    <div class="form-group moving-field">
        <label>New Address 2 (optional):</label>
        <input type="text" name="address_line_2" class="form-control"/>

    </div>
    <div class="form-group moving-field">
        <label>Postal Code:</label>
        <input type="text" name="postal_code" class="form-control"/>

    </div>
    <div class="form-group action-value">
        <label>Speed:</label>
        <select name="product_id" class="product-list form-control">
        </select>
    </div>
    <div class="form-group action-value">
        <label>New Modem Mac Address (optional):</label>
        <input type="text" name="modem_mac_address" class="form-control"/>

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
