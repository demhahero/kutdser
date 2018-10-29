<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?= $api_url ?>customers/orders_by_month_for_customer_merge.php?month=9&year=2018", function (result) {
            $.each(result['customers'], function (i, field) {
                table.row.add([
                    field['customer_name'],
                    field['reseller_name'],
                    '<a target="_blank" href="<?= $site_url ?>/orders/print_order.php?order_id='+field['orders'][0]['order_id']+'">'+field['orders'][0]['product_title']+'<a>',
                    '<a target="_blank" href="<?= $site_url ?>/orders/print_order.php?order_id='+field['orders'][1]['order_id']+'">'+field['orders'][1]['product_title']+'<a>',
                    field['orders'][0]['monthInfo']['recurring_price'],
                    field['orders'][1]['monthInfo']['recurring_price'],
                    parseFloat(field['orders'][0]['monthInfo']['recurring_price'])+parseFloat(field['orders'][1]['monthInfo']['recurring_price']),
                    "SS_"+field['merchantref'],
                ]).draw(false);
            });
        });
    });
</script>
<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 60px;
        height: 60px;
        margin:0 auto;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<title>Customers</title>
<div class="page-header">
    <a class="last" href="">Customers</a>
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th style="width: 15%">Customer</th>
    <th style="width: 15%">Reseller</th>
    <th style="width: 15%">product 1</th>
    <th style="width: 15%">product 2</th>
    <th style="width: 10%">recurring 1</th>
    <th style="width: 10%">recurring 2</th>
    <th style="width: 10%">Total</th>
    <th style="width: 10%">Subscription Ref</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>
