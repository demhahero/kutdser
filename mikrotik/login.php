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
        header('Location: customers/customers.php');
    }
}
?>



<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AmProTelecom | Administration</title>

    <!-- Bootstrap -->
    <link href="<?= $site_url ?>/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= $site_url ?>/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?= $site_url ?>/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?= $site_url ?>/gentelella/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?= $site_url ?>/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form method="post">
              <h1>Login Form</h1>
              <div>
                  <input type="text" class="form-control" name="username" placeholder="Username" required="" />
              </div>
              <div>
                  <input type="password" class="form-control" name="password" placeholder="Password" required="" />
              </div>
              <div>
                  <input type="submit" class="btn btn-default submit" value="Login" href="index.html">
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
        

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> AmProTelecom!</h1>
                  <p>©2018 All Rights Reserved. AmProTelecom! is a Bootstrap 3 template. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>