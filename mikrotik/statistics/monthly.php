<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("https://www.amprotelecom.com/draft/api/orders_by_month.php?month=2", function (result) {
            $.each(result['orders'], function (i, field) {
                table.row.add([
                    '<a href="order_details.php?order_id='+field['order_id']+'" >'+field['order_id']+'</a>',
                    '<a href="<?= $site_url . "/edit_customer.php?customer_id=" ?>'+field['customer']["0"]["customer_id"]+'">'+field['customer']["0"]["full_name"]+'</a>',
                    '<a href="<?= $site_url . "/edit_customer.php?customer_id=" ?>'+field['reseller']["0"]["customer_id"]+'">'+field['reseller']["0"]["full_name"]+'</a>',
                    field['product']["0"]["title"],
                    field['creation_date'],
                    field['status'],
                    "<a target='_blank' href='<?= $site_url ?>/orders/print_order.php?order_id="+field['order_id']+"'><img src='<?= $site_url ?>/img/print-icon.png' style='width: 25px;' /></a>"
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

<title>Orders</title>
<div class="page-header">
    <a class="last" href="">Orders</a>    
</div>

<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 10%">ID</th>
    <th style="width: 20%">Customer</th>
    <th style="width: 15%">Reseller</th>
    <th style="width: 15%">Product</th>
    <th style="width: 15%">Date</th>
    <th style="width: 10%">Status</th>
    <th style="width: 10%">Print</th>
</thead>
<tbody>
</tbody>
</table>



<?php
include_once "../footer.php";
?>