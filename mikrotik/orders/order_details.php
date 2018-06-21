<?php
include_once "../header.php";
?>

<?php
$order = $dbTools->objOrderTools($_GET["order_id"], 2);

if (isset($_POST["status"])) {
    $order->setStatus($_POST["status"]);
    $order->setCompletion($_POST["completion"]);

    if ($_POST["actual_installation_date"] != "")
        $order->setActualInstallationDate(new DateTime($_POST["actual_installation_date"]));

    $order->setUpdateDate(new DateTime());
    $order->setAdminID($admin_id);
    $order->setActualInstallationTimeFrom($_POST["actual_installation_time_from"]);
    $order->setActualInstallationTimeTo($_POST["actual_installation_time_to"]);
    $result = $order->doUpdate();
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}

$order = $dbTools->objOrderTools($_GET["order_id"], 2);
?>
<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?= $api_url ?>order_api.php?order_id=<?= $_GET["order_id"] ?>", function (result) {
                    $.each(result, function (i, field) {
                        $(".displyed-order-id").html(field['displayed_order_id']);
                        $(".customer-full-name").html(field['customer'][0]['full_name']);
                        $(".customer-address").html(field['customer'][0]['address'] + field['customer'][0]['city'] + " " +
                                field['customer'][0]['address_line_1'] + " " + field['customer'][0]['address_line_2'] + " " +
                                field['customer'][0]['postal_code']);
                        $(".customer-email").html(field['customer'][0]['email']);
                        $(".customer-phone").html(field['customer'][0]['phone']);
                        $(".customer-note").html(field['customer'][0]['note']);
                        $(".product-title").html(field['product_title']);
                        $(".plan").html(field['plan']);
                        $(".creation-date").html(field['creation_date']);
                        $(".status").html(field['status']);
                        $(".router").html(field['router']);
                        $(".modem").html(field['modem']);
                        $(".cancellation-date").html(field['cancellation_date']);
                        $(".cable-subscriber").html(field['cable_subscriber']);
                        $(".current-cable-provider").html(field['current_cable_provider']);
                        $(".additional-service").html(field['additional_service']);
                        $(".completion").html(field['completion']);
                        $(".reseller-full-name").html(field['reseller'][0]['full_name']);
                        $(".installation-date-1").html(field['installation_date_1']);
                        $(".installation-time-1").html(field['installation_time_1']);
                        $(".installation-date-2").html(field['installation_date_2']);
                        $(".installation-time-2").html(field['installation_time_2']);
                        $(".installation-date-3").html(field['installation_date_3']);
                        $(".installation-time-3").html(field['installation_time_3']);
                        $(".subscription-ref").html("SS_" + field['merchantref'][0]["merchantref"]);
                        $(".subscription-card-ref").html("CARD_" + field['merchantref'][0]["merchantref"]);
                        $(".subscription-payment-ref").html("P_" + field['merchantref'][0]["merchantref"]);
                    });
                });




                $.getJSON("<?= $api_url ?>customer_log_api.php?customer_id=<?= $order->getCustomer()->getCustomerID(); ?>", function (result) {

                            $.each(result['customer_logs'], function (i, field) {
                                table.row.add([
                                    field['customer_log_id'],
                                    field['note'],
                                    field['log_date'],
                                    field['admin'][0]['username']
                                ]).draw(false);
                            });
                        });

                        $(".submit").click(function () {
<?php
$dt = new DateTime();
?>
                            var customer_id = "<?= $order->getCustomer()->getCustomerID(); ?>";
                            var log_date = "<?= $dt->format("Y-m-d H:i:s") ?>";
                            var note = $("textarea[name=\"note\"]").val();
                            $.post("<?= $api_url ?>customer_log_api.php", {customer_id: customer_id, log_date: log_date, note: note, type: "general", completion: "1", admin_id: '<?= $admin_id ?>'}, function (data, status) {
                                data = $.parseJSON(data);
                                if (data.inserted == true) {
                                    alert("Log inserted");
                                    location.reload();
                                } else
                                    alert("Error, try again");
                            });
                            return false;
                        });
                    });
</script>
<title>Order <?= $order->getOrderID(); ?>'s details</title>
<div class="page-header">
    <a href="orders.php">Orders</a> 
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href="">Order <?= $order->getDisplayedID(); ?>'s details</a>

</div>

<a target="_blank" href="<?= $site_url ?>/orders/print_order.php?order_id=<?php echo $_GET["order_id"]; ?>" class="btn btn-success print-button">Print</a>
<a class="btn btn-danger print-button check-alert" href="send_invoice.php?order_id=<?= $order->getOrderID(); ?>">Send by Email</a>
<a class="btn btn-primary print-button" onclick="window.open('http://38.104.226.51/ahmed/netflow_graph2.php?ip=<?= $order->getCustomer()->getIPAddress(); ?>', 'myWindow', 'width=1200,height=500');" href="#">Usage</a>

<br>
<br>
<div>
    <table class="display table table-striped table-bordered">
        <tr>
            <td style="width:20%;">order ID</td>
            <td class="displyed-order-id"></td>
        </tr>
        <tr>
            <td>Completion</td>
            <td class="completion">

            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td class="customer-full-name">

            </td>
        </tr>
        <tr>
            <td>Customer's Address</td>
            <td class="customer-address">

            </td>
        </tr>
        <tr>
            <td>Customer's Email</td>
            <td class="customer-email">

            </td>
        </tr>
        <tr>
            <td>Customer's Phone</td>
            <td class="customer-phone">

            </td>
        </tr>
        <tr>
            <td>Customer's Note</td>
            <td class="customer-note">

            </td>
        </tr>
        <tr>
            <td>Reseller</td>
            <td class="reseller-full-name">

            </td>
        </tr>
        <tr>
            <td>Creation Date</td>
            <td class="creation-date"></td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="status"></td>
        </tr>
        <tr>
            <td>Product Title</td>
            <td class="product-title">

            </td>
        </tr>
        <tr>
            <td >Plan</td>
            <td class="plan">

            </td>
        </tr>
        <tr>
            <td>Modem</td>
            <td class="modem">

            </td>
        </tr>
        <?php
        if ($order->getModem() == "inventory") {
            ?>
            <tr>
                <td>Modem MAC</td>
                <td>
                    <?= $order->getModemInventoryMAC() ?>
                </td>
            </tr>
            <?php
        } else if ($order->getModem() == "own_modem") {
            ?>
            <tr>
                <td>modem serial number</td>
                <td>
                    <?= $order->getModemSerialNumber() ?>
                </td>
            </tr>
            <tr>
                <td>modem mac address</td>
                <td>
                    <?= $order->getModemMACAddress() ?>
                </td>
            </tr>
            <tr>
                <td>modem modem type</td>
                <td>
                    <?= $order->getModemType() ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td>Router</td>
            <td class="router">

            </td>
        </tr>
        <?php
        if ($order->getCableSubscriber() == "yes") {
            ?>
            <tr>
                <td>Cable subscriber</td>
                <td class="cable-subscriber">

                </td>
            </tr>
            <tr>
                <td>Current cable provider</td>
                <td class="current-cable-provider">

                </td>
            </tr>
            <tr>
                <td>Cancellation date</td>
                <td class="cancellation-date">

                </td>
            </tr>
            <?php
        } else if ($order->getCableSubscriber() == "no") {
            ?>
            <tr>
                <td>installation date 1</td>
                <td class="installation-date-1">

                </td>
            </tr>
            <tr>
                <td>installation time 1</td>
                <td class="installation-time-1">

                </td>
            </tr>
            <tr>
                <td>installation date 2</td>
                <td class="installation-date-2">

                </td>
            </tr>
            <tr>
                <td>installation time 2</td>
                <td class="installation-time-2">

                </td>
            </tr>
            <tr>
                <td>installation date 3</td>
                <td class="installation-date-3">

                </td>
            </tr>
            <tr>
                <td>installation time 3</td>
                <td class="installation-time-3">

                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td>additional service</td>
            <td class="additional-service">

            </td>
        </tr> 
        <tr>
            <td>Subscription Ref</td>
            <td  class="subscription-ref" style="font-weight: bold;">

            </td>
        </tr>
        <tr>
            <td>Secure Card Ref</td>
            <td  class="subscription-card-ref" style="font-weight: bold;">

            </td>
        </tr>
        <tr>
            <td>Payment Ref</td>
            <td  class="subscription-payment-ref" style="font-weight: bold;">

            </td>
        </tr>             	
    </table>
</div>



<form class="register-form" method="post">
    <?php
    $admin_result = $dbTools->query("select * from `admins` where `admin_id`='" . $order->getAdminID() . "'");
    $admin_row = $dbTools->fetch_assoc($admin_result);
    ?>
    <i>Last Update by '<?= $admin_row["username"]; ?>' on <?= $order->getUpdateDate(); ?></i><br/><br/>
    <div class="form-group">

        <label for="email">Status:</label>
        <select  name="status" class="form-control">
            <option <?php if ($order->getStatus() == "sent") echo "selected"; ?> value="sent">Sent</option>
            <option <?php if ($order->getStatus() == "processing") echo "selected"; ?> value="processing">processing</option>
            <option <?php if ($order->getStatus() == "complete") echo "selected"; ?> value="complete">Complete</option>
        </select>
    </div>
    <div class="form-group">
        <label for="email">Completion:</label>
        <input type="text" name="completion" value="<?= $order->getCompletion(); ?>" class="form-control" placeholder="Completion"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation date:</label>
        <input type="text" readonly="" name="actual_installation_date" value="<?php if ($order->getActualInstallationDate() != null) echo $order->getActualInstallationDate()->format("Y-m-d"); ?>" class="form-control datepicker" placeholder="Actual installation date"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation time from:</label>
        <input type="text" name="actual_installation_time_from" value="<?= $order->getActualInstallationTimeFrom(); ?>" class="form-control" placeholder="Actual installation time from"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation time to:</label>
        <input type="text" name="actual_installation_time_to" value="<?= $order->getActualInstallationTimeTo(); ?>" class="form-control" placeholder="Actual installation time to"/>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>

<br/>
<br/>
<br/>
<div class="panel panel-danger">
    <div class="panel-heading">Logs/Notes</div>
    <div class="panel-body">
        <table id="myTable" class="display table table-striped table-bordered">
            <thead>
            <th style="width:10%">ID</th>
            <th style="width:70%">Note</th>
            <th style="width:10%">Date</th>
            <th style="width:10%">Admin</th>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="register-form" method="post">
            <div class="form-group">
                <label>Note:</label>
                <textarea name="note" style="width:100%;" class="form-control"></textarea> 
            </div>
            <input type="submit" class="btn btn-default submit"  value="Send">
        </form>
    </div>
</div>
<?php
include_once "../footer.php";
?>