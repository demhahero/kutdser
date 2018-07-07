<?php
include_once "../header.php";
?>

<?php
$dbTools->query("SET CHARACTER SET utf8");
$request_id = intval($_GET["request_id"]);

// get request info and reseller info
$query = "SELECT `request_id`, reseller.`customer_id`, `admins`.`username`
,reseller.`full_name`, `requests`.`order_id`, `creation_date`, `action`,
 `action_value`,`admins`.`admin_id`, `verdict`, `verdict_date`, `action_on_date`,
 `product_price`, `requests`.`note`, `product_title`, `product_category`,
 `product_subscription_type`, `modem_mac_address`, `modem_id`,`requests`.`city`,`requests`.`address_line_1`,`requests`.`address_line_2`,`requests`.`postal_code`
FROM `requests`
INNER JOIN `customers` as reseller on `reseller`.`customer_id`= `requests`.`reseller_id`
LEFT JOIN `admins` on `admins`.`admin_id`=`requests`.`admin_id`
WHERE `request_id`=" . $request_id;
$request = $dbTools->query($query);
$request_row = $dbTools->fetch_assoc($request);

/// get request's order info
$request_order_query = "SELECT *,`customers`.`full_name` FROM `orders`
INNER JOIN `order_options` on `order_options`.`order_id`=`orders`.`order_id`
INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
where `orders`.`order_id`=" . $request_row['order_id'];
$request_order = $dbTools->query($request_order_query);
$request_order_row = $dbTools->fetch_assoc($request_order);
/// indentify start active date
$start_active_date = "";
if ($request_order_row["product_category"] === "phone") {
    $start_active_date = $request_order_row["creation_date"];
} else if ($request_order_row["product_category"] === "internet") {
    if ($request_order_row["cable_subscriber"] === "yes") {
        $start_active_date = $request_order_row["cancellation_date"];
    } else {
        $start_active_date = $request_order_row["installation_date_1"];
    }
}


/// get last approved request for this order if exist;
$last_request_query = "SELECT `request_id`, reseller.`customer_id`, `admins`.`username`
,reseller.`full_name`, `requests`.`order_id`, `creation_date`, `action`,
 `action_value`,`admins`.`admin_id`, `verdict`, `verdict_date`, `action_on_date`,
 `product_price`, `requests`.`note`, `product_title`, `product_category`,
 `product_subscription_type`, `modem_mac_address`,`requests`.`city`,`requests`.`address_line_1`,`requests`.`address_line_2`,`requests`.`postal_code`
FROM `requests`
INNER JOIN `customers` as reseller on `reseller`.`customer_id`= `requests`.`reseller_id`
LEFT JOIN `admins` on `admins`.`admin_id`=`requests`.`admin_id`

WHERE `requests`.`order_id`=" . $request_row['order_id'] . " and `requests`.`action_on_date` < N'" . $request_row['action_on_date'] . "' and verdict='approve' ORDER BY action_on_date DESC LIMIT 1";

$last_request = $dbTools->query($last_request_query);
$last_request_row = $dbTools->fetch_assoc($last_request);


$product_price = $request_order_row['product_price'];
$product_title = $request_order_row['product_title'];
$product_category = $request_order_row['product_category'];
$product_subscription_type = $request_order_row['product_subscription_type'];
if (sizeof($last_request_row) > 0) {
    $product_price = $last_request_row['product_price'];
    $product_title = $last_request_row['product_title'];
    $product_category = $last_request_row['product_category'];
    $product_subscription_type = $last_request_row['product_subscription_type'];
}


if (isset($_POST["verdict"])) {


    if ($_POST["action"] === "moving") {
        $verdict_date = new DateTime();

        $query_update_request = "UPDATE `requests` SET
    `admin_id`=N'" . $admin_id . "',
    `verdict`=N'" . $_POST["verdict"] . "',
    `verdict_date`=N'" . $verdict_date->format('Y-m-d') . "',
    `product_price`=N'" . $product_price . "',
    `product_title`=N'" . $product_title . "',
    `product_category`=N'" . $product_category . "',
    `product_subscription_type`=N'" . $product_subscription_type . "'
    WHERE `requests`.`request_id`=" . $_POST["request_id"];
        $request_result = $dbTools->query($query_update_request);
    } else {

        if ($_POST["action"] === "swap_modem" && $_POST["verdict"] === "approve") {

            $query_update_request = "update `modems` set `customer_id`='0' "
                    . "where `customer_id`='" . $request_order_row["customer_id"] . "'";
            $request_result = $dbTools->query($query_update_request);

            $query_update_request = "update `modems` set `customer_id`='" . $request_order_row["customer_id"] . "' "
                    . "where `modem_id`='" . $request_row["modem_id"] . "'";
            $request_result = $dbTools->query($query_update_request);
        }

        $verdict_date = new DateTime();

        $query_update_request = "UPDATE `requests` SET
    `admin_id`=N'" . $admin_id . "',
    `verdict`=N'" . $_POST["verdict"] . "',
    `verdict_date`=N'" . $verdict_date->format('Y-m-d') . "'
    WHERE `requests`.`request_id`=" . $_POST["request_id"];
        $request_result = $dbTools->query($query_update_request);
    }


    if ($request_result) {
        if ($_POST["verdict"] === "approve" && $_POST["action"] === "moving") {
            $query_update_order = "UPDATE `orders` SET
          `status`=N'sent'
          WHERE `orders`.`order_id`=" . $_POST["order_id"];
            $order_result = $dbTools->query($query_update_order);
            if ($order_result) {
                echo "<script>window.location.href = \"" . $site_url . "/requests/requests.php\";</script>";
            }
        } else {
            echo "<script>window.location.href = \"" . $site_url . "/requests/requests.php\";</script>";
        }
    }
}
?>

<title>Request Details</title>
<div class="page-header">
    <h4>Request Details</h4>
</div>

<br>


<?php
if ($request_row["verdict"] == "") {
    ?>
    <form class="register-form" method="post">

        <div class="form-group">
            <input type="hidden" name="request_id" value="<?= $request_id ?>"/>
            <input type="hidden" name="action" value="<?= $request_row["action"] ?>"/>
            <input type="hidden" name="order_id" value="<?= $request_row["order_id"] ?>"/>
            <label for="email">Verdict:</label>
            <select  name="verdict" class="form-control">
                <option  value="approve">approve</option>
                <option  value="disapprove">disapprove</option>
            </select>
        </div>
        <input type="submit" class="btn btn-default" value="Submit">
    </form>
    <?php
} else {
    if ($request_row["action"] === "moving" && $request_row["verdict"] === "approve") {
        ?>

        <a target="_blank" href="<?= $site_url ?>/requests/print_request.php?order_id=
           <?= $request_row["order_id"] ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print Invoice </a>
       <?PHP } ?>
    <div>
        <table class="display table table-striped table-bordered">
            <tr>
                <td>
                    "<?= $request_row["username"] ?>"
                    <?= $request_row["verdict"] ?>
                    on
                    <?= $request_row["verdict_date"] ?>
                </td>
            </tr>
        </table>
    </div>
<?PHP } ?>
<div class="row" style="width:100% !important;">
    <div class="col-lg-12 col-md-12 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Request Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">
                    <?PHP
                    if (sizeof($request_row) > 0) {
                        ?>
                        <tr>
                            <td class=" bg-success">Action:</td>
                            <td>
                                <?= $request_row['action'] ?>
                            </td>

                            <td class=" bg-success">Action On Date:</td>
                            <td>
                                <?= $request_row['action_on_date'] ?>
                            </td>
                            <td class=" bg-success">Verdict Date:</td>
                            <td >
                                <?= $request_row['verdict_date'] ?>
                            </td>


                            <td class=" bg-success">Verdict:</td>
                            <td >
                                <?= $request_row['verdict'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Admin:</td>
                            <td>
                                <?= $request_row['username'] ?>
                            </td>
                            <td class=" bg-success">Reseller Name:</td>
                            <td>
                                <?= $request_row['full_name'] ?>
                            </td>
                            <td class=" bg-success">Modem Mac Address:</td>
                            <td>
                                <?= $request_row['modem_mac_address'] ?>
                            </td>
                            <td class=" bg-success">Note:</td>
                            <td>
                                <?= $request_row['note'] ?>
                            </td>



                        </tr>
                        <tr>
                            <td class=" bg-success">Product Name:</td>
                            <td>
                                <?= $request_row['product_title'] ?>
                            </td>
                            <td class=" bg-success">Product price:</td>
                            <td>
                                <?= $request_row['product_price'] ?>
                            </td>

                            <td class=" bg-success">Product Type:</td>
                            <td>
                                <?= $request_row['product_subscription_type'] ?>
                            </td>

                            <td class=" bg-success">Product Category:</td>
                            <td>
                                <?= $request_row['product_category'] ?>
                            </td>



                        </tr>
                        <tr>
                            <td class=" bg-success">City:</td>
                            <td>
                                <?= $request_row['city'] ?>
                            </td>
                            <td class=" bg-success">Address Line 1:</td>
                            <td>
                                <?= $request_row['address_line_1'] ?>
                            </td>

                            <td class=" bg-success">Address Line 2:</td>
                            <td>
                                <?= $request_row['address_line_2'] ?>
                            </td>
                            <td class=" bg-success">Postal Code:</td>
                            <td>
                                <?= $request_row['postal_code'] ?>
                            </td>
                        </tr>
                        <?PHP
                    } else {
                        ?>
                        <tr>
                            <td>There are no previous Requests</td>

                        </tr>
                    <?PHP }
                    ?>
                </table>
            </div>
        </div>
        </p>
    </div>
</div>
<div class="row" style="width:100% !important;">
    <div class="col-lg-6 col-md-6 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Order Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">
                    <tr>
                        <td>Customer Name</td>
                        <td>
                            <?= $request_order_row['full_name'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%;">order ID</td>
                        <td><?PHP
                            if ((int) $request_order_row['order_id'] <= 10380) {
                                echo $request_order_row['order_id'];
                            } else {
                                echo (((0x0000FFFF & (int) $request_order_row['order_id']) << 16) + ((0xFFFF0000 & (int) $request_order_row['order_id']) >> 16));
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Start Active Date</td>
                        <td>
                            <?= $start_active_date ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Product Type</td>
                        <td>
                            <?= $request_order_row['product_subscription_type'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Product Category</td>
                        <td>
                            <?= $request_order_row['product_category'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Product Name</td>
                        <td>
                            <?= $request_order_row['product_title'] ?>
                        </td>

                    <tr>
                        <td>Creation Date</td>
                        <td><?= $request_order_row['creation_date'] ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><?= $request_order_row['status'] ?></td>
                    </tr>

                    <tr>
                        <td>Plan</td>
                        <td>
                            <?= $request_order_row['plan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Modem</td>
                        <td>
                            <?= $request_order_row['modem'] ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Router</td>
                        <td>
                            <?= $request_order_row['router'] ?>
                        </td>
                    </tr>
                    <?php
                    if ($request_order_row['cable_subscriber'] == "yes") {
                        ?>
                        <tr>
                            <td>Cable subscriber</td>
                            <td>
                                <?= $request_order_row['cable_subscriber'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Current cable provider</td>
                            <td>
                                <?= $request_order_row['current_cable_provider'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Cancellation date</td>
                            <td>
                                <?= $request_order_row['cancellation_date'] ?>
                            </td>
                        </tr>
                        <?php
                    } else if ($request_order_row['cable_subscriber'] == "no") {
                        ?>
                        <tr>
                            <td>installation date 1</td>
                            <td>
                                <?= $request_order_row['installation_date_1'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>installation time 1</td>
                            <td>
                                <?= $request_order_row['installation_time_1'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>installation date 2</td>
                            <td>
                                <?= $request_order_row['installation_date_2'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>installation time 2</td>
                            <td>
                                <?= $request_order_row['installation_time_2'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>installation date 3</td>
                            <td>
                                <?= $request_order_row['installation_date_3'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>installation time 3</td>
                            <td>
                                <?= $request_order_row['installation_time_3'] ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td>additional service</td>
                        <td>
                            <?= $request_order_row['additional_service'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation date:</td>
                        <td>
                            <?php if ($request_order_row['actual_installation_date']) echo $request_order_row['actual_installation_date']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation time from:</td>
                        <td>
                            <?= $request_order_row['actual_installation_time_from']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation time to:</td>
                        <td>
                            <?= $request_order_row['actual_installation_time_to']; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </p>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Previous Request Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">
                    <?PHP
                    if (sizeof($last_request_row) > 0) {
                        ?>
                        <tr>
                            <td class=" bg-success">Action:</td>
                            <td>
                                <?= $last_request_row['action'] ?>
                            </td>

                            <td class=" bg-success">Action On Date:</td>
                            <td>
                                <?= $last_request_row['action_on_date'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Verdict Date:</td>
                            <td >
                                <?= $last_request_row['verdict_date'] ?>
                            </td>


                            <td class=" bg-success">Verdict:</td>
                            <td >
                                <?= $last_request_row['verdict'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Admin:</td>
                            <td>
                                <?= $last_request_row['username'] ?>
                            </td>
                            <td class=" bg-success">Reseller Name:</td>
                            <td>
                                <?= $last_request_row['full_name'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Modem Mac Address:</td>
                            <td>
                                <?= $last_request_row['modem_mac_address'] ?>
                            </td>
                            <td class=" bg-success">Note:</td>
                            <td>
                                <?= $last_request_row['note'] ?>
                            </td>



                        </tr>
                        <tr>
                            <td class=" bg-success">Product Name:</td>
                            <td>
                                <?= $last_request_row['product_title'] ?>
                            </td>
                            <td class=" bg-success">Product price:</td>
                            <td>
                                <?= $last_request_row['product_price'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Product Type:</td>
                            <td>
                                <?= $last_request_row['product_subscription_type'] ?>
                            </td>

                            <td class=" bg-success">Product Category:</td>
                            <td>
                                <?= $last_request_row['product_category'] ?>
                            </td>



                        </tr>
                        <tr>
                            <td class=" bg-success">City:</td>
                            <td>
                                <?= $last_request_row['city'] ?>
                            </td>
                            <td class=" bg-success">Address Line 1:</td>
                            <td>
                                <?= $last_request_row['address_line_1'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class=" bg-success">Address Line 2:</td>
                            <td>
                                <?= $last_request_row['address_line_2'] ?>
                            </td>
                            <td class=" bg-success">Postal Code:</td>
                            <td>
                                <?= $last_request_row['postal_code'] ?>
                            </td>
                        </tr>
                        <?PHP
                    } else {
                        ?>
                        <tr>
                            <td>There are no previous Requests</td>

                        </tr>
                    <?PHP }
                    ?>
                </table>
            </div>
        </div>
        </p>
    </div>
</div>



<?php
include_once "../footer.php";
?>
