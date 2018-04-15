<?php
include_once "dbconfig.php";
?>

<?php
$username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
$password = stripslashes(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

$admin_result = $conn_routers->query("select * from `admins` where `username`='" . $username . "'");
while ($admin_row = $admin_result->fetch_assoc()) {
    if (password_verify($password, $admin_row['password'])) {
        $session_id = uniqid('', true);
        $conn_routers->query("update `admins` set `session_id`='" . $session_id . "' where `username`='" . $username . "'");
        $_SESSION["session_id"] = $session_id;
        header('Location: customers.php');
    }
}
?>

<link rel="stylesheet" href="css/login.css">
<script src='js/jquery-3.2.1.min.js'></script>
<script src='js/login.js'></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Management</title>
<div class="login-page">
    <div class="form">
        <center><img style="margin-top: -10px;" width="200px" src="img/logo.png"/></center><br>
        <form class="login-form" method="post">
            <input type="text" name="username" placeholder="username"/>
            <input type="password" name="password" placeholder="password"/>
            <button class='login_button'>login</button>
        </form>
    </div>
</div>