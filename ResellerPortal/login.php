<?php
include_once "../api/dbconfig.php";
?>

<?php
// $username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
// if ($username != FALSE) {
//
//     $reseller_result = $dbToolsReseller->query("select * from `customers` where `username`='" . $username . "'");
//     while ($row  = $reseller_result->fetch_assoc()) {
//         if (password_verify($_POST["password"], $row["password"])) {
//             //$session_id = md5(microtime() . $_SERVER['REMOTE_ADDR']);
//             $session_id = $row["session_id"];
//             $dbToolsReseller->query("Update `customers` set `session_id` = '" . $session_id . "' where `customer_id` = '" . $row["customer_id"] . "'");
//
//             setcookie("session_id", $session_id, time() + (86400 * 30), "/");
//             if ($row["is_new_system"] == "1") {
//                 header('Location: customers/customers.php');
//             } else {
//                 header('Location: customers.php');
//             }
//         }
//     }
// }
?>

<link rel="stylesheet" href="css/login.css">
<script src='js/jquery-3.2.1.min.js'></script>
<script src='js/login.js'></script>
<title>Reseller Portal</title>
<script>
    $(document).ready(function () {
        $("#login_form").submit(function (e) {
          e.preventDefault();
          var username = $("input[name=\"username\"]").val();
          var password = $("input[name=\"password\"]").val();

          $.post("<?= $api_url ?>authentication/authentication_api.php",
                  {
                    action:"login",
                    username: username,
                    password: password,
                    reseller:"yes"
                  }
          , function (data_response, status) {
              data_response = $.parseJSON(data_response);
              if (data_response.login == true) {
                window.location.href = 'customers/customers.php';
              } else
              {
                  alert(data_response.message);

                }
          });
        });
    });
</script>

<div class="login-page">
    <div class="form">
        <center>
            <img style="margin-top: -10px;" width="200px" src="img/logo.png"/>
            <br/>
            <h1 style="color:#fa7921;">Reseller Portal</h1>
        </center>
        <br>
        <form class="login-form" id="login_form" >
            <input type="text" name="username" placeholder="username"/>
            <input type="password" name="password" placeholder="password"/>
            <button class='login_button'>login</button>
        </form>
    </div>
</div>
