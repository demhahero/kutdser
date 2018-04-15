<?php
include_once "../header.php";
?>

<?php
if (isset($_POST["full_name"])){
    $upcoming_customer = $dbTools->objUpcomingCustomerTools();
    $upcoming_customer->setFullName($_POST["full_name"]);
    $upcoming_customer->setAddress($_POST["address"]);
    $upcoming_customer->setEmail($_POST["email"]);
    $upcoming_customer->setPhone($_POST["phone"]);
    $upcoming_customer->setNote($_POST["note"]);
    $upcoming_customer->setCreationDate(date("Y-m-d H:i:s"));
    
    $admin = $dbTools->objAdminTools($admin_id);
    $upcoming_customer->setAdmin($admin);
    
    $result = $upcoming_customer->doInsert();
    if($result){
        echo "<div class='alert alert-success'>done</div>";
    }
}
?>

<title>Create Upcoming Customer</title>
<div class="page-header">
    <h4>Create Upcoming Customer</h4>    
</div>

<form class="register-form" method="post">
    <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="" class="form-control" placeholder="Full Name"/>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="text" name="email" value="" class="form-control" placeholder="Email"/>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" class="form-control" placeholder="Phone"/>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <textarea type="text" name="address" class="form-control" /></textarea>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea type="text" name="note" class="form-control" /></textarea>
    </div>
    <input type="submit" class="btn btn-default" value="create">
</form>
<?php
include_once "../footer.php";
?>
