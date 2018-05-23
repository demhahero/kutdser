<?php
include_once "dbconfig.php";
?>
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jq-3.2.1/dt-1.10.16/r-2.2.0/datatables.min.css"/>

        <script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jq-3.2.1/jq-3.2.1/dt-1.10.16/r-2.2.0/datatables.min.js"></script>

        <script src='https://momentjs.com/downloads/moment.js'></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>


<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

        <script src='<?= $site_url ?>/js/script.js'></script>
        <script src='<?= $site_url ?>/js/bootstrap-datetimepicker.min.js'></script>

        <link rel="stylesheet" href="<?= $site_url ?>/css/template.css">
        <link rel="stylesheet" href="<?= $site_url ?>/css/bootstrap-datetimepicker.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


    </head>

    <body>

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
                    <a class="navbar-brand" href="<?= $site_url ?>/index.php"><img style="margin-top: -10px;" width="150px" src="<?= $site_url ?>/img/logo.png"/></a>

                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?= $site_url ?>/index.php">Routers</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Customers
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= $site_url ?>/customers.php?type=2">Customers</a></li>
                                <li><a href="<?= $site_url ?>/internet_customers.php">Internet Customers</a></li>
                                <li><a href="<?= $site_url ?>/terminated_customers.php">Terminated Customers</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= $site_url ?>/customers.php">Resellers</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Bills
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= $site_url ?>/custom_invoice.php">Custom Invoice</a></li>
                                <li><a href="<?= $site_url ?>/custom_statement.php">Custom Statement</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Inventory
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= $site_url ?>/modems/modems.php">Modems</a></li>
                                <li><a href="<?= $site_url ?>/routers/routers.php">Routers</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">New
                                <?php
                                $orders = $dbTools->order_query("select * from `orders` where `status`='sent'", 3);
                                if (count($orders) > 0)
                                    echo "<span class=\"label label-danger\">" . count($orders) . "</span>";
                                ?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= $site_url ?>/orders/orders.php">Orders
                                        <?php
                                        if (count($orders) > 0)
                                            echo "<span class=\"label label-danger\">" . count($orders) . "</span>";
                                        ?>
                                    </a></li>
                                <li><a href="<?= $site_url ?>/customers/customers.php">Customers</a></li>
                                <li><a href="<?= $site_url ?>/customers/resellers.php">Resellers</a></li>
                                <li><a href="<?= $site_url ?>/upcoming_customers/upcoming_customers.php">Upcoming Customers</a></li>
                                <li><a href="<?= $site_url ?>/requests/requests.php">Requests</a></li>
                            </ul>
                        </li>
                        <li>
                          <a href="<?= $site_url ?>/expire/expire_soon_orders.php">Expire Soon
                            <?php
                            $count = $dbTools->order_expiration_count();
                            if ($count > 0)
                                echo "<span id=\"expireCount\" class=\"label label-danger\">" . $count . "</span>";
                            ?></a>
                        </li>
                        <li><a href="<?= $site_url ?>/tik_monitoring/customers.php">Support</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Hello "<?= $username; ?>"</a></li>
                        <li><a href="<?= $site_url ?>/?do=logout">Logout</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
