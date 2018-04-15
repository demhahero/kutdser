<?php
include_once "../header.php";
?>

<?php
$order = $dbTools->objOrderTools($_GET["order_id"], 2);

if (isset($_POST["status"])) {
    $order->setStatus($_POST["status"]);
    $order->setCompletion($_POST["completion"]);
    
    if($_POST["actual_installation_date"] != "")
        $order->setActualInstallationDate(new DateTime($_POST["actual_installation_date"]));
    
    $order->setActualInstallationTimeFrom($_POST["actual_installation_time_from"]);
    $order->setActualInstallationTimeTo($_POST["actual_installation_time_to"]);
    $result = $order->doUpdate();
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>

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
            <td><?= $order->getDisplayedID() ?></td>
        </tr>  
        <tr>
            <td style="width:20%;">Termination Date</td>
            <td><?php if($order->getTerminationDate() != null) echo $order->getTerminationDate()->format("Y-m-d"); ?></td>
        </tr> 
        <tr>
            <td>Completion</td>
            <td>
                <?= $order->getCompletion() ?>
            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>
                <?= $order->getCustomer()->getFullName(); ?>
            </td>
        </tr>
        <tr>
            <td>Customer's Address</td>
            <td>
                <?= $order->getCustomer()->getAddress(); ?>
            </td>
        </tr>
        <tr>
            <td>Customer's Email</td>
            <td>
                <?= $order->getCustomer()->getEmail(); ?>
            </td>
        </tr>
        <tr>
            <td>Customer's Phone</td>
            <td>
                <?= $order->getCustomer()->getPhone(); ?>
            </td>
        </tr>
        <tr>
            <td>Customer's Note</td>
            <td>
                <?= $order->getCustomer()->getNote(); ?>
            </td>
        </tr>
        <tr>
            <td>Reseller</td>
            <td>
                <?= $order->getReseller()->getFullName(); ?>
            </td>
        </tr>
        <tr>
            <td>Creation Date</td>
            <td><?= $order->getCreationDate()->format("Y-m-d") ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><?= $order->getStatus() ?></td>
        </tr>
        <tr>
            <td>Product Name</td>
            <td>
                <?= $order->getProduct()->getTitle(); ?>
            </td>
        </tr>
        <tr>
            <td>Plan</td>
            <td>
                <?= $order->getPlan() ?>
            </td>
        </tr>
        <tr>
            <td>Modem</td>
            <td>
                <?= $order->getModem() ?>
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
            <td>
                <?= $order->getRouter() ?>
            </td>
        </tr>
        <?php
        if ($order->getCableSubscriber() == "yes") {
        ?>
        <tr>
            <td>Cable subscriber</td>
            <td>
                <?= $order->getCableSubscriber() ?>
            </td>
        </tr>
        <tr>
            <td>Current cable provider</td>
            <td>
                <?= $order->getCurrentCableProvider() ?>
            </td>
        </tr>
        <tr>
            <td>Cancellation date</td>
            <td>
                <?= $order->getCancellationDate() ?>
            </td>
        </tr>
        <?php
        } else if ($order->getCableSubscriber() == "no") {
        ?>
        <tr>
            <td>installation date 1</td>
            <td>
                <?= $order->getInstallationDate1() ?>
            </td>
        </tr>
        <tr>
            <td>installation time 1</td>
            <td>
                <?= $order->getInstallationTime1() ?>
            </td>
        </tr>
        <tr>
            <td>installation date 2</td>
            <td>
                <?= $order->getInstallationDate2() ?>
            </td>
        </tr>
        <tr>
            <td>installation time 2</td>
            <td>
                <?= $order->getInstallationTime2() ?>
            </td>
        </tr>
        <tr>
            <td>installation date 3</td>
            <td>
                <?= $order->getInstallationDate3() ?>
            </td>
        </tr>
        <tr>
            <td>installation time 3</td>
            <td>
                <?= $order->getInstallationTime3() ?>
            </td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <td>additional service</td>
            <td>
                <?= $order->getAdditionalService() ?>
            </td>
        </tr> 
        <tr>
            <td>Subscription Ref</td>
            <td style="background-color: gray;">
                <?="SS_" . $order->getCustomer()->getMerchant()->getMerchantRef()?>
            </td>
        </tr>
        <tr>
            <td>Secure Card Ref</td>
            <td style="background-color: gray;">
                <?="CARD_" . $order->getCustomer()->getMerchant()->getMerchantRef()?>
            </td>
        </tr>
        <tr>
            <td>Payment Ref</td>
            <td style="background-color: gray;">
                <?="P_" . $order->getCustomer()->getMerchant()->getMerchantRef()?>
            </td>
        </tr>             	
    </table>
</div>

<form class="register-form" method="post">
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
        <input type="text" readonly="" name="actual_installation_date" value="<?php if($order->getActualInstallationDate() != null) echo $order->getActualInstallationDate()->format("Y-m-d"); ?>" class="form-control datepicker" placeholder="Actual installation date"/>
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
<?php
include_once "../footer.php";
?>