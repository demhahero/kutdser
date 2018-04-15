<?php
include_once "../header.php";
?>

<?php

$upcoming_customer = $dbTools->objUpcomingCustomerTools($_GET["upcoming_customer_id"]);

if (isset($_POST["full_name"])){
    $upcoming_customer->setFullName($_POST["full_name"]);
    $upcoming_customer->setAddress($_POST["address"]);
    $upcoming_customer->setEmail($_POST["email"]);
    $upcoming_customer->setPhone($_POST["phone"]);
    $upcoming_customer->setNote($_POST["note"]);
    $upcoming_customer->setCreationDate(date("Y-m-d H:i:s"));
    
    $result = $upcoming_customer->doUpdate();
    if($result){
        echo "<div class='alert alert-success'>done</div>";
    }
}
?>

<title>Edit Upcoming Customer</title>
<div class="page-header">
    <h4>Edit Upcoming Customer</h4>    
</div>

<form class="register-form" method="post">
    <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?=$upcoming_customer->getFullName()?>" class="form-control" placeholder="Full Name"/>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="text" name="email" value="<?=$upcoming_customer->getEmail()?>" class="form-control" placeholder="Email"/>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" value="<?=$upcoming_customer->getPhone()?>" class="form-control" placeholder="Phone"/>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <textarea type="text" name="address" class="form-control" /><?=$upcoming_customer->getAddress()?></textarea>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea type="text" name="note" class="form-control" /><?=$upcoming_customer->getNote()?></textarea>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>
<?php
include_once "../footer.php";
?>
