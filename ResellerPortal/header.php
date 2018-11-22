<?php
if(!@include_once "../../api/dbconfig.php")
  include_once "../api/dbconfig.php";
?>
<!doctype html>
<html>
    <head>
      <link rel="shortcut icon" href="<?=$site_url?>/img/favicon.png" />

      <link rel="stylesheet" type="text/css" href="<?=$site_url?>/css/datatables.min.css"/>
      <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">

      <script type="text/javascript" src="<?=$site_url?>/js/datatables.min.js"></script>

      <script src='<?=$site_url?>/js/script.js'></script>
      <link rel="stylesheet" href="<?=$site_url?>/css/template.css">

      <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/options.js></script>

      <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/bootstrap-datepicker.min.js></script>
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
    <center>
        <?php if ($is_new_system) { ?>

            <div class="alert alert-danger" style="width: 75%;">
                <a href="<?= $site_url ?>/system_alerts/system_alerts.php">
                    <strong>***New Updates!</strong> Dear Reseller <span style="color: red;" ><?= $username; ?></span>, Please click here to know more about our new features that have been added recently.
                </a>
            </div>

            <?php
        }
        ?>
    </center>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <?php if (!$is_new_system) { ?>
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?= $site_url ?>/index.php"><img style="margin-top: -10px;" width="150px" src="<?= $site_url ?>/img/logo.png"/></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?= $site_url ?>/customers.php">Customers</a></li>
                        <li><a href="<?= $site_url ?>/my_resellers.php">My Resellers</a></li>
                        <li><a href="<?= $site_url ?>/notes.php">Notes</a></li>

                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="logout" href="#">Logout</a></li>
                    </ul>
                </div><!--/.nav-collapse -->



            <?php } else { ?>
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?= $site_url ?>/index.php"><img style="margin-top: -10px;" width="150px" src="<?= $site_url ?>/img/logo.png"/></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?= $site_url ?>/customers/customers.php">Customers</a></li>
                        <li><a href="<?= $site_url ?>/orders/orders.php">Orders</a></li>
                        <li><a href="<?= $site_url ?>/shop/shop.php">Shop</a></li>
                        <li><a href="<?= $site_url ?>/requests/requests.php">Requests</a></li>
                        <li><a href="<?= $site_url ?>/customers/my_resellers.php">My Resellers</a></li>
                        <?PHP if ($reseller_id==="190")
                        {
                          ?>
                          <li><a href="<?= $site_url ?>/shop/shop_test.php">test api shop</a></li>
                          <li><a href="<?= $site_url ?>/customers/statistics.php">Statistics</a></li>
                        <?PHP
                        }
                        ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a  style="color:red" href="#">Hello "<?= $username; ?>"</a></li>
                        <li><a class="logout" href="#">Logout</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            <?php } ?>
        </div>
    </nav>
    <div class="container">
