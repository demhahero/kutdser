<?php
if(!@include_once "../../api/dbconfig_direct.php")
  include_once "../api/dbconfig_direct.php";
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
                            <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><img style="margin-top: -10px;" width="150px" src="<?= $site_url ?>/img/logo.png"/></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="shop.php">Shop</a></li>
                          </ul>
                    <ul class="nav navbar-nav navbar-right">
                      
                    </ul>
                </div>
        </div>
    </nav>
    <div class="container">
