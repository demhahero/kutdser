<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "header.php";
?>

<?php
include_once "../api/dbconfig.php";

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[rand(0, $max)];
    }
    return $str;
}

$date = new DateTime();

if (isset($_POST["full_name"])) {

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);


    $customer_query = "INSERT INTO `customers` (
        			`username` ,
				`password` ,
				`full_name` ,
				`address` ,
				`email`,
				`phone`,
                                `is_reseller`,
                                `reseller_id`,
                                `is_new_system`,
                                `session_id`
				)
				VALUES (
            ?,"
            . " ?,"
            . " ?,"
            . " ?,"
            . " ?,"
            . " ?,"
            . " '1' ,"
            . " '0' ,"
            . " '1' ,"
            . " ?"
            . ")";

    $stmt1 = $dbTools->getConnection()->prepare($customer_query);

 
    $param_value1 = ($_POST["username"]);
    $param_value2 = ($password);
    $param_value3 = ($_POST["full_name"]);
    $param_value4 = ($_POST["address"]);
    $param_value5 = ($_POST["email"]);
    $param_value6 = ($_POST["phone"]);
    $param_value7 = random_str(32);
    $stmt1->bind_param('sssssss', $param_value1, $param_value2, $param_value3, $param_value4, $param_value5,
            $param_value6, $param_value7
    ); // 's' specifies the variable type => 'string'


    

    if ($stmt1->execute()) {
        //header("Location: customers.php");
        //die();
        echo "<div class='alert alert-success'>done</div>";
    }
}


?>

<title>Create Customer</title>
<div class="page-header">
    <h4>Create Customer</h4>    
</div>
<form class="register-form" method="post">
    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" value="" class="form-control" placeholder="Username"/>
    </div>
    <div class="form-group">
        <label >Password:</label>
        <input type="text" name="password" value="" class="form-control" placeholder="Password"/>
    </div>
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

    <input type="submit" class="btn btn-default" value="create">
</form>

<?php
include_once "footer.php";
