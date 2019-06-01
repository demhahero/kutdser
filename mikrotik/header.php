<?php
if (!@include_once "../../api/dbconfig.php")
    include_once "../api/dbconfig.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/css/datatable.min.css"/>
        <link rel="stylesheet" href="<?= $site_url ?>/css/bootstrap-datetimepicker.min.css">

        <!-- Bootstrap -->
        <link href="<?= $site_url ?>/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?= $site_url ?>/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="<?= $site_url ?>/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">

        <!-- Switchery -->
        <link href="<?= $site_url ?>/gentelella/vendors/switchery/dist/switchery.min.css" rel="stylesheet">

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

        <!-- Print Excel and PDF for datatables -->
        <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/css/datatable2.min.css"/>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="<?= $site_url ?>/js/datatable.min.js"></script>
        <script>
            $(document).ready(function () {
                $(".logout").click(function () {
                    $.post("<?= $api_url ?>authentication/authentication_api.php",
                            {
                                action: "logout"
                            }
                    , function (data_response, status) {
                        data_response = $.parseJSON(data_response);
                        if (data_response.logout == true) {
                            window.location.href = '<?= $site_url ?>/login.php';
                        } else
                        {
                            alert("logout failed");

                        }
                    });
                });
                function getUpdates(){
                $.post("<?= $api_url ?>orders/order_sent_count_api.php",
                        {
                            action: "get_total_order_sent"
                        }
                , function (response, status) {
                    response = $.parseJSON(response);
                    if (!response.error) {
                        if(response.total_order_sent>0)
                        {
                          $('.total_order_sent').html(response.total_order_sent);
                        }
                        else{
                          $('.total_order_sent').html("");
                        }
                        if(response.total_request_sent>0)
                        {
                          $('.total_request_sent').html(response.total_request_sent);
                        }
                        else{
                          $('.total_request_sent').html("");
                        }
                        if(response.total_reseller_request_sent>0)
                        {
                          $('.total_reseller_request_sent').html(response.total_reseller_request_sent);
                        }
                        else{
                          $('.total_reseller_request_sent').html("");
                        }
                    } else {
                        $('.total_order_sent').html("");
                        $('.total_request_sent').html("");
                        $('.total_reseller_request_sent').html("");
                    }
                }
                );
              }
              (function worker() {

                      getUpdates();
                      setTimeout(worker, 60000);

              })();
            });

            (function worker() {
                $.get("<?= $api_url ?>orders/new_orders_notification.php", function (data) {
                    // Now that we've completed the request schedule the next one.

                    if(data != "0"){
                       alert("New Order(s) has(have) been sent");
                    }
                    setTimeout(worker, 60000);
                });
            })();
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

    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                    <div class="left_col scroll-view">
                        <div class="navbar nav_title" style="border: 0;">
                            <a href="<?= $site_url ?>/customers/customers.php" class="site_title"><i class="fa fa-paw"></i> <span>AmProTelecom!</span></a>
                        </div>

                        <div class="clearfix"></div>

                        <!-- menu profile quick info -->
                        <div class="profile clearfix">
                            <div class="profile_pic">
                                <img src="<?= $site_url ?>/gentelella/images/img.jpg" alt="..." class="img-circle profile_img">
                            </div>
                            <div class="profile_info">
                                <span>Welcome,</span>
                                <h2><?= $username; ?></h2>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- /menu profile quick info -->

                        <br />

                        <!-- sidebar menu -->
                        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                            <div class="menu_section">
                                <h3>General</h3>
                                <ul class="nav side-menu">
                                    <li><a><i class="fa fa-male"></i> Customers<span class="blink total_request_sent"></span> <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/customers/customers.php">Customers</a></li>
                                            <li><a href="<?= $site_url ?>/requests/requests.php">Requests<span class="blink total_request_sent"></span></a></li>
                                            <li><a href="<?= $site_url ?>/upcoming_customers/upcoming_customers.php">Upcoming Customers</a></li>
                                            <li><a href="<?= $site_url ?>/expire/expire_soon_orders.php">Expire Soon</a></li>
                                        </ul>
                                    </li>

                                    <li><a><i class="fa fa-shopping-cart"></i> Order <span class="blink total_order_sent" ></span> <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/orders/orders.php">Orders<span class="blink total_order_sent" ></span></a></li>
                                            <li><a href="<?= $site_url ?>/customers/going_to_merge.php">Merges</a></li>
                                            <li><a href="<?= $site_url ?>/orders/phones.php">Phones</a></li>
                                            <li><a href="<?= $site_url ?>/orders/offer_deadline_list.php">Offers Deadline</a></li>
                                        </ul>
                                    </li>

                                    <li><a><i class="fa fa-sitemap"></i>Resellers <span class="blink total_reseller_request_sent"></span> <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/create_customer.php">Create Reseller</a></li>
                                            <li><a href="<?= $site_url ?>/customers/resellers.php">Resellers</a></li>
                                            <li><a href="<?= $site_url ?>/customers/customers_transfer.php">Customers Transfer</a></li>
                                            <li><a href="<?= $site_url ?>/reseller_requests/reseller_requests.php">Requests<span class="blink total_reseller_request_sent"></span></a></li>
                                            <li><a href="<?= $site_url ?>/custom_invoice.php">Custom Invoice</a></li>
                                            <li><a href="<?= $site_url ?>/custom_statement.php">Custom Statement</a></li>
                                            <li><a href="<?= $site_url ?>/information/edit_information.php">Edit Reseller Portal</a></li>
                                        </ul>
                                    </li>
                                    <li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/statistics/orders_between_two_dates.php?date1=2018-04-01&date2=2018-06-01">Orders over duration</a></li>
                                            <li><a href="<?= $site_url ?>/statistics/orders_by_new_transfer.php?date1=2018-04-01&date2=2018-06-01&cable_subscriber=yes">Orders New/Transfer</a></li>
                                            <li><a href="<?= $site_url ?>/statistics/requests_between_two_dates.php?date1=2018-04-01&date2=2018-09-14&type=requests_fees">Requests</a></li>
                                        </ul>
                                    </li>
                                    <li><a><i class="fa fa-inbox"></i> Inventory <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/modems/modems.php">Modems</a></li>
                                            <li><a href="<?= $site_url ?>/routers/routers.php">Routers</a></li>
                                            <li><a href="<?= $site_url ?>/index.php">Routers (user/pass)</a></li>
                                        </ul>
                                    </li>
                                    <li><a><i class="fa fa-support"></i> Support <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/tik_monitoring/customers.php">Support</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="menu_section">
                                <h3>Live On</h3>
                                <ul class="nav side-menu">
                                    <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="<?= $site_url ?>/temp/orders.php">Update Orders</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
                                </ul>
                            </div>

                        </div>
                        <!-- /sidebar menu -->

                        <!-- /menu footer buttons -->
                        <div class="sidebar-footer hidden-small">
                            <a data-toggle="tooltip" data-placement="top" title="Settings">
                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                            </a>
                            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                            </a>
                            <a data-toggle="tooltip" data-placement="top" title="Lock">
                                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                            </a>
                            <a class="logout" data-toggle="tooltip" data-placement="top" title="Logout" href="#">
                                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                            </a>
                        </div>
                        <!-- /menu footer buttons -->
                    </div>
                </div>

                <!-- top navigation -->
                <div class="top_nav">
                    <div class="nav_menu">
                        <nav>
                            <div class="nav toggle">
                                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                            </div>

                            <ul class="nav navbar-nav navbar-right">
                                <li class="">
                                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <img src="<?= $site_url ?>/gentelella/images/img.jpg" alt=""><?= $username; ?>
                                        <span class=" fa fa-angle-down"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                                        <li><a href="javascript:;"> Profile</a></li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge bg-red pull-right">50%</span>
                                                <span>Settings</span>
                                            </a>
                                        </li>
                                        <li><a href="javascript:;">Help</a></li>
                                        <li><a class="logout" href="#"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                    </ul>
                                </li>

                                <li role="presentation" class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-envelope-o"></i>
                                        <span class="badge bg-green total_order_sent"></span>
                                    </a>
                                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">

                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /top navigation -->

                <!-- page content -->
                <div class="right_col" role="main">
                    <div class="">
                        <div class="page-title">
                            <div class="title_left">
                                <h3>Plain Page</h3>
                            </div>

                            <div class="title_right">
                                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search for...">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button">Go!</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
