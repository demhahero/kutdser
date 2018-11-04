<?php
include_once "../header.php";
?>

<title>Order Details</title>
<div class="page-header">
    <h4>Order Details</h4>
</div>

<a target="_blank" href="<?= $site_url ?>/shop/print_order.php?order_id=<?php echo $_GET["order_id"]; ?>" class="btn btn-success print-button">Print</a>
<br>
<br>
<div>
    <table class="display table table-striped table-bordered">
        <?php
        $order = $dbToolsReseller->objOrderTools($_GET["order_id"]);
        ?>
        <tr>
            <td style="width:20%;">order ID</td>
            <td><?=$order->getDisplayedID() ?></td>
        </tr>
        <tr>
            <td>Completion</td>
            <td>
                <?=$order->getCompletion() ?>
            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>
                <?=$order->getCustomer()->getFullName(); ?>
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
                <?=$order->getReseller()->getFullName(); ?>
            </td>
        </tr>
        <tr>
            <td>Creation Date</td>
            <td><?=$order->getCreationDate()->format("Y-m-d") ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><?=$order->getStatus() ?></td>
        </tr>
        <tr>
            <td>Product Name</td>
            <td>
                <?=$order->getProduct()->getTitle(); ?>
            </td>
        </tr>
        <tr>
            <td>Plan</td>
            <td>
                <?=$order->getPlan() ?>
            </td>
        </tr>
        <tr>
            <td>Modem</td>
            <td>
                <?=$order->getModem() ?>
            </td>
        </tr>
        <?php
        if ($order->getModem() == "inventory") {
            ?>
            <tr>
                <td>Modem id</td>
                <td>
                    <?=$order->getModemInventoryMAC() ?>
                </td>
            </tr>
            <?php
        } else if ($order->getModem() == "my_own") {
            ?>
            <tr>
                <td>modem serial number</td>
                <td>
                    <?=$order->getModemSerialNumber() ?>
                </td>
            </tr>
            <tr>
                <td>modem mac address</td>
                <td>
                    <?=$order->getModemMACAddress() ?>
                </td>
            </tr>
            <tr>
                <td>modem modem type</td>
                <td>
                    <?=$order->getModemType() ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td>Router</td>
            <td>
                <?=$order->getRouter() ?>
            </td>
        </tr>
        <?php
        if ($order->getCableSubscriber()  == "yes") {
            ?>
            <tr>
                <td>Cable subscriber</td>
                <td>
                    <?=$order->getCableSubscriber()?>
                </td>
            </tr>
            <tr>
                <td>Current cable provider</td>
                <td>
                    <?=$order->getCurrentCableProvider()?>
                </td>
            </tr>
            <tr>
                <td>Cancellation date</td>
                <td>
                    <?=$order->getCancellationDate()?>
                </td>
            </tr>
            <?php
        } else if ($order->getCableSubscriber() == "no") {
            ?>
            <tr>
                <td>installation date 1</td>
                <td>
                    <?=$order->getInstallationDate1()?>
                </td>
            </tr>
            <tr>
                <td>installation time 1</td>
                <td>
                    <?=$order->getInstallationTime1()?>
                </td>
            </tr>
            <tr>
                <td>installation date 2</td>
                <td>
                    <?=$order->getInstallationDate2()?>
                </td>
            </tr>
            <tr>
                <td>installation time 2</td>
                <td>
                    <?=$order->getInstallationTime2()?>
                </td>
            </tr>
            <tr>
                <td>installation date 3</td>
                <td>
                    <?=$order->getInstallationDate3()?>
                </td>
            </tr>
            <tr>
                <td>installation time 3</td>
                <td>
                    <?=$order->getInstallationTime3()?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td>additional service</td>
            <td>
                <?=$order->getAdditionalService()?>
            </td>
        </tr>
        <tr>
            <td>Actual installation date:</td>
            <td>
                <?php if($order->getActualInstallationDate() != null) echo $order->getActualInstallationDate()->format("Y-m-d"); ?>
            </td>
        </tr>
        <tr>
            <td>Actual installation time from:</td>
            <td>
                <?= $order->getActualInstallationTimeFrom(); ?>
            </td>
        </tr>
        <tr>
            <td>Actual installation time to:</td>
            <td>
                <?= $order->getActualInstallationTimeTo(); ?>
            </td>
        </tr>
    </table>
</div>

<?php
include_once "../footer.php";
?>
