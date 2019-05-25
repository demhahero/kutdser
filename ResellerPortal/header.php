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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

      <script type="text/javascript" src="<?=$site_url?>/js/datatables.min.js"></script>

      <script src='<?=$site_url?>/js/script.js'></script>
      <link rel="stylesheet" href="<?=$site_url?>/css/template.css">

      <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/options.js></script>

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
            
            $.get("<?= $api_url ?>information/get_resellerportal_notification.php",
                      {
                        action:"logout"
                      }
              , function (data_response, status) {
                  data_response = $.parseJSON(data_response);
                  if(data_response["information"]["resellerportal_notification"] != ""){
                      $(".resellerportal-notification").show();
                  }
                  $(".resellerportal-notification").html($(".resellerportal-notification").html() 
                          + " " + data_response["information"]["resellerportal_notification"]);
              });
          });
          </script>

          <style>
              .blink {
                  color:red;
                  vertical-align: super;
                  font-size: 12px;
                  animation: blink-animation 1s steps(5,start) infinite;
                  -webkit-animation: blink-animation 1s steps(5,start) infinite;
              }

              /* Safari */
              @-webkit-keyframes spin {
                  to { visibility: hidden; }
              }

              @keyframes blink-animation {
                  to { visibility: hidden; }
              }
          </style>
    </head>

    <body>
    <center>
        <?php if ($is_new_system) { ?>

            <div class="resellerportal-notification alert alert-danger" style="width: 75%; display: none;">
                <strong>Warning!</strong> Dear Reseller <span style="color: #ef3232;" ><?= $username; ?>: </span>
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
                          <li><a href="<?= $site_url ?>/customers/statistics.php">Statistics</a></li>
                          <li><a href="<?= $site_url ?>/shop/shop_test1.php">Shop tv</a></li>

                        <?PHP
                        }
                        ?>
                        <li><a href="<?= $site_url ?>/reseller_requests/reseller_requests.php">My Requests<span class="blink">new</span></a></li>
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
