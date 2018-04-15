<?php
include_once "../header.php";
?>
<?php
$routerTools = $dbTools->objRouterTools(intval($_GET["router_id"]));


if (isset($_POST["serial_number"])) {

    (isset($_POST["is_ours"])) ? $is_ours = "yes" : $is_ours = "no";
    (isset($_POST["is_sold"])) ? $is_sold = "yes" : $is_sold = "no";

    $routerTools->setIsSold($is_sold);
    $routerTools->setType(mysql_real_escape_string($_POST["type"]));
    $routerTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
    $routerTools->setIsOurs($is_ours);
    $routerTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
    $routerTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
    
    $result = $routerTools->doUpdate();
    
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Edit Router</title>
<div class="page-header">
    <h4>Edit Router</h4>    
</div>

<form class="register-form" method="post">
    <div class="form-group">
        <label>Serial Number:</label>
        <input type="text" name="serial_number" value="<?= $routerTools->getSerialNumber() ?>" class="form-control" placeholder="serial number"/>
    </div>
    <div class="form-group">
        <label>Type:</label>
        <input type="text" name="type" value="<?= $routerTools->getType() ?>" class="form-control" placeholder="type"/>
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>
            <?php
            $resellers = $dbTools->customer_query("select * from `customers` where `is_reseller` = '1'");
            foreach ($resellers as $reseller) {
                if ($reseller->getCustomerID() == $routerTools->getReseller()->getCustomerID())
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
                if ($customer->getCustomerID() == $routerTools->getCustomer()->getCustomerID())
                    echo "<option selected value='" . $customer->getCustomerID() . "'>" . $customer->getFullName() . "</option>";
                else
                    echo "<option value='" . $customer->getCustomerID() . "'>" . $customer->getFullName() . "</option>";
            }
            ?>     
        </select>
    </div>
    <div class="form-group">
        <label>Is ours:</label>
        <input type="checkbox" name="is_ours" <?php if ($routerTools->getIsOurs() == "yes") echo "checked"; ?> value="yes" class="form-control" placeholder=""/>
    </div>  
    <div class="form-group">
        <label>Is Sold:</label>
        <input type="checkbox" name="is_sold" <?php if ($routerTools->getIsSold() == "yes") echo "checked"; ?> value="yes" class="form-control" placeholder=""/>
    </div>  
    <input type="submit" class="btn btn-default" value="Update">
</form>

<?php
include_once "../footer.php";
?>