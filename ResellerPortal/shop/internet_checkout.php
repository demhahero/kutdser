<?php
include_once "../header.php";
?>

<?php
include "internet_checkout_helper.php";
$helper = new InternetCheckoutHelper($dbToolsReseller, $reseller_id);
$result = $helper->checkoutSetup($_POST);
$secure_card_merchantref = $result["secure_card_merchantref"] ;
$start_active_date_string = $result["start_active_date_string"] ;
$subscription_start_date = $result["subscription_start_date"] ;
$subscription_recurring_amount = $result["subscription_recurring_amount"] ;
$merchantref = $result["merchantref"] ;
$subscription_start_date = $result["subscription_start_date"] ;
$subscription_recurring_amount = $result["subscription_recurring_amount"] ;
$subscription_initial_amount = $result["subscription_initial_amount"] ;
$subscription_period_type = $result["subscription_period_type"] ;
$product_id = $result["product_id"];
$_POST["options"] = $result["options"];
$_POST["invoice_items"] = $result["invoice_items"];

?>
<title>Checkout</title>

<script>
    $(document).ready(function () {
        var is_complete = false;
        $(window).bind('beforeunload', function () {
            if (is_complete == false)
                return 'Please do not leave before checkout processing completes';
        });

        $("div.processing-content").show();
        $("div.succeeded-content").hide();
        $("div.failed-content").hide();
        function orderSubmittedSuccessfully(order_id) {
            var myarr = order_id.split("_");
            $('.print-button').attr("href", "print_order_test.php?invoice_id=" + myarr[0]);
            $("h3.order-id").html("Order id: " + myarr[1]);
            $("div.processing-content").hide();
            $("div.succeeded-content").show();
            is_complete = true;
        }
        function orderFailed(reason) {
            $("div.processing-content").hide();
            $("div.failed-content").show();
            $("span.failed-reason").html("Error: " + reason);
            is_complete = true;
        }

<?php
if ($_POST["card_type"] != "cache_on_delivery") {
    if ($secure_card_merchantref == false) {
        ?>
                //1- register
                $("div.process-caption").html("Registering Card...");
                $.post("gateway_processes.php?do=register", {
                    card_number: '<?= $_POST["card_number"] ?>',
                    card_type: '<?= $_POST["card_type"] ?>',
                    card_expiry: '<?= $_POST["card_expiry"] ?>',
                    card_holders_name: '<?= $_POST["card_holders_name"] ?>',
                    card_cvv: '<?= $_POST["card_cvv"] ?>',
                    merchant_reference: '<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data == 1) {
                                $("div.process-caption").html("1-Registering Card Done<br/>");

                                //2- Subscription
                                $("div.process-caption").html("Making subscription...");
                                $.post("gateway_processes.php?do=subscription", {
                                    subscription_start_date: '<?= $subscription_start_date ?>',
                                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                                    initial_amount: '<?= $subscription_initial_amount ?>',
                                    period_type: '<?= $subscription_period_type ?>',
                                    merchant_reference: '<?= $merchantref ?>'})
                                        .done(function (data) {
                                            if (data == 1) {
                                                $("div.process-caption").html("Subscription Done...");

                                                //3- Add Order to customer
                                                $("div.process-caption").html("Adding order...");
                                                $.post("register_customer.php", {
                                                    product: '<?= $product_id ?>',
                                                    full_name: '<?= $_POST["full_name"] ?>',
                                                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                                                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                                                    postal_code: `<?= $_POST["postal_code"] ?>`,
                                                    city: `<?= $_POST["city"] ?>`,
                                                    email: '<?= $_POST["email"] ?>',
                                                    phone: '<?= $_POST["phone"] ?>',
                                                    note: `<?= $_POST["note"] ?>`,
                                                    customer_id: '<?= $_POST["customer_id"] ?>',
                                                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                                                    invoice_items: JSON.stringify(<?= json_encode($_POST["invoice_items"]) ?>),
                                                    start_active_date: '<?= $start_active_date_string ?>',
                                                    recurring_date: '<?= $subscription_start_date ?>',
                                                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                                                    merchantref: '<?= $merchantref ?>'})
                                                        .done(function (data) {
                                                            if (data != 0) {
                                                                $("div.process-caption").html("Order Sent Successfully");
                                                                orderSubmittedSuccessfully(data);
                                                            } else {
                                                                $("div.process-caption").html("Order failed. " + data);
                                                                orderFailed("Order failed. " + data);
                                                            }
                                                        });
                                            } else {
                                                $("div.process-caption").html("Subscription failed. " + data);
                                                orderFailed("Subscription failed. " + data);
                                            }
                                        });

                            } else {
                                $("div.process-caption").html("Subscription failed. " + data);
                                orderFailed("Regisatrtion failed. " + data);
                            }
                        });
        <?php
    } 
    else { //if existed customer
        ?>
                //1- Do payment
                $("div.process-caption").html("Adding order...");
                $.post("gateway_processes.php?do=payment", {
                    card_number: '<?= $_POST["card_number"] ?>',
                    card_type: '<?= $_POST["card_type"] ?>',
                    card_expiry: '<?= $_POST["card_expiry"] ?>',
                    card_holders_name: '<?= $_POST["card_holders_name"] ?>',
                    card_cvv: '<?= $_POST["card_cvv"] ?>',
                    merchant_reference: '<?= $merchantref ?>',
                    amount: '<?= $subscription_initial_amount ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                $("div.process-caption").html("Payment done Successfully");

                                //2- Add Order to customer
                                $("div.process-caption").html("Adding order...");
                                $.post("register_customer.php", {
                                    product: '<?= $product_id ?>',
                                    full_name: '<?= $_POST["full_name"] ?>',
                                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                                    postal_code: `<?= $_POST["postal_code"] ?>`,
                                    city: `<?= $_POST["city"] ?>`,
                                    email: '<?= $_POST["email"] ?>',
                                    phone: '<?= $_POST["phone"] ?>',
                                    note: `<?= $_POST["note"] ?>`,
                                    customer_id: '<?= $_POST["customer_id"] ?>',
                                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                                    invoice_items: JSON.stringify(<?= json_encode($_POST["invoice_items"]) ?>),
                                    start_active_date: '<?= $start_active_date_string ?>',
                                    recurring_date: '<?= $subscription_start_date ?>',
                                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                                    existed_merchant_reference: '<?= $secure_card_merchantref ?>',
                                    merchantref: '<?= $merchantref ?>'})
                                        .done(function (data) {
                                            if (data != 0) {
                                                $("div.process-caption").html("Order Sent Successfully");
                                                orderSubmittedSuccessfully(data);
                                            } else {
                                                $("div.process-caption").html("Order failed. " + data);
                                                orderFailed("Order failed. " + data);
                                            }
                                        });
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });
        <?php
    }
} 
else { //if cache on delivery
    if ($secure_card_merchantref == false) {
        ?>

                //1- register
                $.post("register_customer.php", {
                    product: '<?= $product_id ?>',
                    full_name: '<?= $_POST["full_name"] ?>',
                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                    postal_code: `<?= $_POST["postal_code"] ?>`,
                    city: `<?= $_POST["city"] ?>`,
                    email: '<?= $_POST["email"] ?>',
                    phone: '<?= $_POST["phone"] ?>',
                    note: `<?= $_POST["note"] ?>`,
                    customer_id: '<?= $_POST["customer_id"] ?>',
                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                    invoice_items: JSON.stringify(<?= json_encode($_POST["invoice_items"]) ?>),
                    start_active_date: '<?= $start_active_date_string ?>',
                    recurring_date: '<?= $subscription_start_date ?>',
                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                    merchantref: 'cache_on_delivery_<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                $("div.process-caption").html("Order Sent Successfully");
                                orderSubmittedSuccessfully(data);
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });
        <?php
    } 
    else { //if existed customer
        ?>
                //1- Add order
                $.post("register_customer.php", {
                    product: '<?= $product_id ?>',
                    full_name: '<?= $_POST["full_name"] ?>',
                    address_line_1: `<?= $_POST["address_line_1"] ?>`,
                    address_line_2: `<?= $_POST["address_line_2"] ?>`,
                    postal_code: `<?= $_POST["postal_code"] ?>`,
                    city: `<?= $_POST["city"] ?>`,
                    email: '<?= $_POST["email"] ?>',
                    phone: '<?= $_POST["phone"] ?>',
                    note: `<?= $_POST["note"] ?>`,
                    customer_id: '<?= $_POST["customer_id"] ?>',
                    options: JSON.stringify(<?= json_encode($_POST["options"]) ?>),
                    invoice_items: JSON.stringify(<?= json_encode($_POST["invoice_items"]) ?>),
                    start_active_date: '<?= $start_active_date_string ?>',
                    recurring_date: '<?= $subscription_start_date ?>',
                    recurring_amount: '<?= $subscription_recurring_amount ?>',
                    existed_merchant_reference: 'cache_on_delivery_<?= $merchantref ?>',
                    merchantref: 'cache_on_delivery_<?= $merchantref ?>'})
                        .done(function (data) {
                            if (data != 0) {
                                alert(data);
                                $("div.process-caption").html("Order Sent Successfully");
                                orderSubmittedSuccessfully(data);
                            } else {
                                $("div.process-caption").html("Order failed. " + data);
                                orderFailed("Order failed. " + data);
                            }
                        });
        <?php
    }
}
?>
    });
</script>

<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>


<center>
    <div class="processing-content">
        <h2 style="color:red;">Important: Do not leave or refresh this page until checkout process is done.</h1>
            <h3>Please wait while processing...</h2>
        <div class="loader"></div>
        <h5 class="process-caption" style="color:#00cc00;">Register</h4>
    </div>

    <div class="succeeded-content">
        <div class="alert alert-success order-result">
            <strong>Congratulation!</strong> Order sent successfully!
        </div>
        <h3 class="order-id" style="color: #990099">Order id: 111</h2>
            <a href="" target="_blank" class="print-button"><image class="img-thumbnail" style="width: 50px;" src="<?= $site_url ?>/img/print-icon.png" /></a>
    </div>
    <div class="failed-content">
        <div class="alert alert-danger">
            <strong>Failed!</strong> Error occurred, please call the administrator for more information.<br/>
            <span class="failed-reason"></span>
        </div>
    </div>

</center>

<?php
include_once "../footer.php";
?>
