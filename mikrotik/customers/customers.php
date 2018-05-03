<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?=$api_url?>customers_api.php", function (result) {
            $.each(result['customers'], function (i, field) {
                table.row.add([
                    field['customer_id'],
                    field['full_name'],
                    field['reseller'][0]['full_name'],
                    field['phone'],    
                    field['email'], 
                    '<a href="customer_orders.php?customer_id='+field['customer_id']+'">Invoices</a>',
                    '<a href="customer_orders.php?customer_id='+field['customer_id']+'">Orders</a>',
                    '<a href="customer_by_month.php?customer_id='+field['customer_id']+'&month=4&year=2018">status</a>'
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
    <th style="width: 10%">ID</th>
    <th style="width: 20%">Full Name</th>
    <th style="width: 20%">Reseller</th>
    <th style="width: 10%">Phone</th>
    <th style="width: 20%">Email</th>
    <th style="width: 5%">Invoices</th>
    <th style="width: 5%">Orders</th>
    <th style="width: 5%">Monthly</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>