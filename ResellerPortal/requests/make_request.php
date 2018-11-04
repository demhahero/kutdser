<?php
include_once "../header.php";
?>

<?php
$order_id = intval($_GET["order_id"]);

$creation_date = new DateTime();

if (isset($_POST["action"])) {

    $action_on_date = new DateTime(($_POST["action_on_date"]));

    $product = $dbToolsReseller->objProductTools($_POST["action_value"]);

    $requestTools = $dbToolsReseller->objRequestTools(null);
    $requestTools->setReseller($dbToolsReseller->objCustomerTools($reseller_id));
    $requestTools->setAction(($_POST["action"]));
    $requestTools->setActionValue(($_POST["action_value"]));
    $requestTools->setNote(($_POST["note"]));
    $requestTools->setOrder($dbToolsReseller->objOrderTools($order_id));
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
        var fistDayInstallation = '1';
        $('#datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '+' + fistDayInstallation + 'd'
        });

        $(".moving-field").hide();
        $(".cusotmer-info-field").hide();

        $(".swap-change").hide();
        $("select[name=\"action\"]").change(function () {

          var data_value=$(this).find(':selected').data('value');

            if (data_value == "change_speed") {
                $(".action-value").show();
            } else {
                $(".action-value").hide();
            }
            if (data_value == "moving") {
                $(".moving-field").show();
            } else {
                $(".moving-field").hide();
            }

            if (data_value == "customer_information_modification") {
                $(".cusotmer-info-field").show();
            } else {
                $(".cusotmer-info-field").hide();
            }
            if (data_value == "swap_modem") {
                $(".swap-modem").show();
            } else {
                $(".swap-modem").hide();
            }
            if (data_value == "swap_change_speed") {
                $(".swap-change").show();
            } else {
                $(".swap-change").hide();
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

            var full_name = $("input[name=\"full_name\"]").val();
            var email = $("input[name=\"email\"]").val();
            var phone = $("input[name=\"phone\"]").val();

            //Swap modem
            var modem_id = $("select[name=\"modem_id\"]").val();

            var product_id = $("select[name=\"product_id\"]").val();
            $.post("<?= $api_url ?>insert_requests_api.php",
                    {
                        order_id: order_id,
                        action: action,
                        product_id: product_id,
                        action_on_date: action_on_date,
                        modem_mac_address: modem_mac_address,
                        modem_id: modem_id,
                        city: city,
                        address_line_1: address_line_1,
                        address_line_2: address_line_2,
                        postal_code: postal_code,
                        full_name: full_name,
                        email: email,
                        phone: phone,
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
        $order_result = $dbToolsReseller->query("select * from `orders` where `order_id`='" . $order_id . "'");
        if ($order_row = $order_result->fetch_assoc()) {

            $customer_sql = "select * from `customers` where `customer_id`='" . $order_row["customer_id"] . "'";
            $customer_result = $dbToolsReseller->query($customer_sql);

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
            <option data-value="change_speed" value="change_speed">Change speed</option>
            <option data-value="swap_modem" value="swap_modem">Swap Modem</option>
            <option data-value="swap_change_speed" value="change_speed">Swap Modem and Change speed</option>
            <option data-value="moving" value="moving">Moving</option>
            <option data-value="terminate" value="terminate">Terminate</option>
            <option data-value="customer_information_modification" value="customer_information_modification">Customer Information Modification</option>
        </select>
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" id="datepicker" class="form-control" />
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


    <div class="form-group cusotmer-info-field">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?=$customer_row["full_name"]?>" class="form-control"/>
    </div>
    <div class="form-group cusotmer-info-field">
        <label>Email:</label>
        <input type="text" name="email" value="<?=$customer_row["email"]?>" class="form-control"/>
    </div>
    <div class="form-group cusotmer-info-field">
        <label>Phone:</label>
        <input type="text" name="phone" value="<?=$customer_row["phone"]?>" class="form-control"/>
    </div>

    <div class="form-group action-value">
        <label>Speed:</label>
        <select name="product_id" class="product-list form-control">
        </select>
    </div>
    <div class="form-group swap-change">
        <label>Speed:</label>
        <select name="product_id" class="product-list form-control">
        </select>
    </div>
    <div class="form-group swap-change">
        <label>New Modem Mac Address (optional):</label>

        <select name="modem_id" class="form-control">
            <option value="0" selected>Select Modem</option>
            <?php
            $result_modems = $dbToolsReseller->query("select * from `modems` where `reseller_id`='" . $reseller_id . "' and `customer_id`='0'");
            while ($row_modem = $result_modems->fetch_assoc()) {
                echo "<option value=\"" . $row_modem["modem_id"] . "\">" . $row_modem["mac_address"] . "[" . $row_modem["type"] . " | " . $row_modem["serial_number"] . "]" . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group action-value swap-modem" style="display: none;">
        <label>New Modem:</label>
        <select name="modem_id" class="form-control">
            <?php
            $result_modems = $dbToolsReseller->query("select * from `modems` where `reseller_id`='" . $reseller_id . "' and `customer_id`='0'");
            while ($row_modem = $result_modems->fetch_assoc()) {
                echo "<option value=\"" . $row_modem["modem_id"] . "\">" . $row_modem["mac_address"] . "[" . $row_modem["type"] . " | " . $row_modem["serial_number"] . "]" . "</option>";
            }
            ?>
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
