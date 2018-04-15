<?php
include_once "../header.php";
?>

<?php

//Save product id & order options in session
$product_id = intval($_POST["speed"]);
$_SESSION["product_id"] = $product_id;
$_SESSION["options"] = $_POST["options"];

?>

<title>Customer's Information</title>
<div class="page-header">
    <h4>Customer's Information</h4>    
</div>
<form class="register-form" action="checkout.php" method="post">
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
        <label for="email">Note:</label>
        <textarea type="text" name="note" class="form-control" /></textarea>
    </div>
    <br>
    <br>
    <input type="submit" class="btn btn-danger btn-block btn-lg"  value="Checkout!">
</form>

<?php
include_once "../footer.php";
?>
