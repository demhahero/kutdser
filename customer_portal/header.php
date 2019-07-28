<?php
if(!@include_once "../../api/dbconfig.php")
  include_once "../api/dbconfig.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?= $site_url ?>/images/favicon.png">

    <title>AM-PRO</title>

    <link rel="stylesheet" type="text/css" href="<?=$site_url?>/css/datatables.min.css"/>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="<?= $site_url ?>/css/bootstrap.min.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?= $site_url ?>/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?= $site_url ?>/css/custom.css" rel="stylesheet">
    <link href="<?= $site_url ?>/style.css" rel="stylesheet">
    <link href="<?= $site_url ?>/css/responsive.css" rel="stylesheet">
    <!-- Plugins -->
    <link href="<?= $site_url ?>/css/plugins.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700&display=swap" rel="stylesheet">
    <link href="<?= $site_url ?>/fonts/ionicons/css/ionicons.min.css" rel="stylesheet">

    <script src='<?= $site_url ?>/js/jquery-3.2.1.min.js'></script>
    <!-- jQuery-UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Print Excel and PDF for datatables -->
    <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/css/datatable2.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="<?= $site_url ?>/js/datatable.min.js"></script>
    <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/bootstrap-datepicker.min.js></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link rel="stylesheet" href="<?=$site_url?>/css/bootstrap-datepicker3.css">
    <script>
      $(document).ready(function () {
        $(".logout").click(function(){
          $.post("<?= $api_url ?>authentication/authentication_api.php",
                  {
                    action:"logout"
                  }
          , function (data_response, status) {
              data_response = $.parseJSON(data_response);
              if (data_response.logout == true) {
                window.location.href = '<?=$site_url?>/login.php';
              } else
              {
                  alert("logout failed");

                }
          });
        });
      });
    </script>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark animated">
    <div class="container fw-700">
        <a class="navbar-brand" href="<?= $site_url ?>/#">
            <img src="<?= $site_url ?>/images/logo.png" alt="" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item _selection">
                    <a class="nav-link" href="<?= $site_url ?>/orders/customer_orders.php">Orders</a>
                    <a class="nav-link" href="<?= $site_url ?>/invoices/customer_invoices.php">Invoices</a>
                </li>

            </ul>
            <div class="form-inline _logins">
                <?php
                if(!isset($_SESSION["session_id"]))
                { ?>
                <a href="<?= $site_url ?>/login.php" class="btn btn-light">LOG IN</a>
              <?php }else {?>
                <a href="#" class="btn btn-light logout">LOG OUT</a>
                <?php } ?>
                <div class="dropdown">
                    <a href="<?= $site_url ?>/#" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fr</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?= $site_url ?>/#">EN</a>
                        <a class="dropdown-item" href="<?= $site_url ?>/#">FR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- Banner -->
<section class="_banner __adj">
    <div class="container middle _relative _z9">
        <div class="text-center">
            <h1>Broadband, Phone & TV</h1>
            <h3>High-performance connectivity for the entire home</h3>
        </div>
    </div>
    <!-- Particles -->
    <div id="p-circle-white" class="_particles"></div>
</section>
<!-- Deals -->
<section class="_deals">
    <div class="container _relative _z9">
