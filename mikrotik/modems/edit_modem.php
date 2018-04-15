<?php
include_once "../header.php";
?>
<?php
$modemTools = $dbTools->objModemTools(intval($_GET["modem_id"]));


if (isset($_POST["mac_address"])) {

    (isset($_POST["is_ours"])) ? $is_ours = "yes" : $is_ours = "no";

    $modemTools->setMACAddress(mysql_real_escape_string($_POST["mac_address"]));
    $modemTools->setType(mysql_real_escape_string($_POST["type"]));
    $modemTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
    $modemTools->setIsOurs($is_ours);
    $modemTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
    $modemTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
    
    $result = $modemTools->doUpdate();
    
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Edit Modem</title>
<div class="page-header">
    <h4>Edit Modem</h4>    
</div>

<form class="register-form" method="post">
    <div class="form-group">
        <label>MAC Address/Phone:</label>
        <input type="text" name="mac_address" value="<?= $modemTools->getMACAddress() ?>" class="form-control" placeholder="mac address"/>
    </div>
    <div class="form-group">
        <label>Serial Number:</label>
        <input type="text" name="serial_number" value="<?= $modemTools->getSerialNumber() ?>" class="form-control" placeholder="serial number"/>
    </div>
    <div class="form-group">
        <label>Type:</label>
        <input type="text" name="type" value="<?= $modemTools->getType() ?>" class="form-control" placeholder="type"/>
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            $resellers = $dbTools->customer_query("select * from `customers` where `is_reseller` = '1'");
            foreach ($resellers as $reseller) {
                if ($reseller->getCustomerID() == $modemTools->getReseller()->getCustomerID())
                    echo "<option selected value='" . $reseller->getCustomerID() . "'>" . $reseller->getFullName() . "</option>";
                else
                    echo "<option value='" . $reseller->getCustomerID() . "'>" . $reseller->getFullName() . "</option>";
            }
            ?>   
        </select>
    </div>
    <div class="form-group">
        <label>Customer:</label>
        <select  name="customer_id" class="form-control">
            <option value="0">No Customer</option>
            <?php
            $customers = $dbTools->customer_query("select * from `customers` where `is_reseller` = '0'");
            foreach ($customers as $customer) {
                if ($customer->getCustomerID() == $modemTools->getCustomer()->getCustomerID())
                    echo "<option selected value='" . $customer->getCustomerID() . "'>" . $customer->getFullName() . "</option>";
                else
                    echo "<option value='" . $customer->getCustomerID() . "'>" . $customer->getFullName() . "</option>";
            }
            ?>     
        </select>
    </div>
    <div class="form-group">
        <label>Is ours:</label>
        <input type="checkbox" name="is_ours" <?php if ($modemTools->getIsOurs() == "yes") echo "checked"; ?> value="yes" class="form-control" placeholder=""/>
    </div>  
    <input type="submit" class="btn btn-default" value="Update">
</form>

<?php
include_once "../footer.php";
?>