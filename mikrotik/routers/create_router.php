<?php
include_once "../header.php";
?>
<?php
if (isset($_POST["serial_number"])) {
    (isset($_POST["is_ours"]))? $is_ours = "yes" : $is_ours = "no";
    (isset($_POST["is_sold"])) ? $is_sold = "yes" : $is_sold = "no";
    
    $routerTools = $dbTools->objRouterTools(null);
    $routerTools->setIsSold($is_sold);
    $routerTools->setType(mysql_real_escape_string($_POST["type"]));
    $routerTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
    $routerTools->setIsOurs($is_ours);
    $routerTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
    $routerTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
    
    $result = $routerTools->doInsert();

    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Create Router</title>
<div class="page-header">
    <h4>Create Router</h4>    
</div>

<form class="register-form" method="post">
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
    <div class="form-group">
        <label>Is Sold:</label>
        <input type="checkbox" name="is_sold" value="yes" class="form-control" placeholder=""/>
    </div>  
    <input type="submit" class="btn btn-default" value="create">
</form>

<?php
include_once "../footer.php";
?>