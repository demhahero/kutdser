<?php
if (!@include_once "../../api/dbconfig_direct.php")
    include_once "../api/dbconfig_direct.php";
?>
<!doctype html>
<html>
    <head>
        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-fix.css" rel="stylesheet">
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/custom.css" rel="stylesheet">
        <link href="style.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
        <!-- Plugins -->
        <link href="css/plugins.css" rel="stylesheet">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700&display=swap" rel="stylesheet">
        <link href="fonts/ionicons/css/ionicons.min.css" rel="stylesheet">         

        <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/css/datatables.min.css"/>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

        <script type="text/javascript" src="<?= $site_url ?>/js/datatables.min.js"></script>

        <script src='<?= $site_url ?>/js/script.js'></script>

        <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/options.js></script>

        <script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/bootstrap-datepicker.min.js></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        <link rel="stylesheet" href="<?= $site_url ?>/css/bootstrap-datepicker3.css">


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

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="images/favicon.png">

        <title>AM-PRO</title>



    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark animated">
            <div class="container fw-700">
                <a class="navbar-brand" href="#">
                    <img src="images/logo.png" alt="" />
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item _selection">
                            <a class="nav-link" href="#">residential</a>
                            <a class="nav-link" href="#">business</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">products</a>
                            <ul class="dropdown-menu" aria-labelledby="dropdown01">
                                <li><a class="nav-link" href="#">Product One</a></li>
                                <li><a class="nav-link" href="#">Product Two</a></li>
                                <li><a class="nav-link" href="#">Product Three</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">find your plan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">company</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">support</a>
                        </li>
                    </ul>
                    <div class="form-inline _logins">
                        <a href="#" class="btn btn-light">LOG IN</a>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fr</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#">EN</a>
                                <a class="dropdown-item" href="#">FR</a>
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

