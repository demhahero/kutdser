<?php
include_once "../api/dbconfig.php";
?>




<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=$login_title?> | Administration</title>

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
    <!-- jquery-ui -->
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

    <!-- jQuery -->
    <script src="<?= $site_url ?>/gentelella/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?= $site_url ?>/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- jQuery-UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
                        password: password
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
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form id="login_form">
              <h1>Login Form</h1>
              <div>
                  <input type="text" class="form-control" name="username" placeholder="Username" required="" />
              </div>
              <div>
                  <input type="password" class="form-control" name="password" placeholder="Password" required="" />
              </div>
              <div>
                  <input type="submit" class="btn btn-default submit" value="Login" >
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">


                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> <?=$login_title?>!</h1>
                  <p>Powered by AmProTelecom!</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
