<?php
include_once "../header.php";
?>
<?php
if (isset($_POST["mac_address"])) {
    (isset($_POST["is_ours"]))? $is_ours = "yes" : $is_ours = "no";
    
    $modemTools = $dbTools->objModemTools(null);
    $modemTools->setMACAddress(mysql_real_escape_string($_POST["mac_address"]));
    $modemTools->setType(mysql_real_escape_string($_POST["type"]));
    $modemTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
    $modemTools->setIsOurs($is_ours);
    $modemTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
    $modemTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
    
    $result = $modemTools->doInsert();

    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Create Modem</title>
<div class="page-header">
    <h4>Create Modem</h4>    
</div>

<form class="register-form" method="post">
    <div class="form-group">
        <label>MAC Address/Phone:</label>
        <input type="text" name="mac_address" value="" class="form-control" placeholder="mac address"/>
    </div>
    <div class="form-group">
        <label>Serial Number:</label>
        <input type="text" name="serial_number" value="" class="form-control" placeholder="serial number"/>
    </div>
    <div class="form-group">
        <label>Type:</label>
        <input type="text" name="type" value="" class="form-control" placeholder="type"/>
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            $resellers = $dbTools->customer_query("select * from `customers` where `is_reseller` = '1'");
            foreach ($resellers as $reseller) {
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
                echo "<option value='" . $customer->getCustomerID() . "'>" . $customer->getFullName() . "</option>";
            }
            ?>    
        </select>
    </div>
    <div class="form-group">
        <label>Is ours:</label>
        <input type="checkbox" name="is_ours" value="yes" class="form-control" placeholder=""/>
    </div>    
    <input type="submit" class="btn btn-default" value="create">
</form>

<?php
include_once "../footer.php";
?>