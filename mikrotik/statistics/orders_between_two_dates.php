<?php
include_once "../header.php";
?>

<?php
if (isset($_GET["date1"])) {
    $date1 = $_GET["date1"] . " 00:00:00";
    $date2 = $_GET["date2"] . " 00:00:00";
} else {
    $date1 = "2018-04-01 00:00:00";
    $date2 = "2018-06-28 00:00:00";
}
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?= $api_url ?>orders_between_two_dates.php?date1=<?= $date1 ?>&date2=<?= $date2 ?>", function (result) {
                    $.each(result['orders'], function (i, field) {
                        table.row.add([
                            '<a href="order_details.php?order_id=' + field['order_id'] + '" >' + field['displayed_order_id'] + '</a>',
                            '<a href="<?= $site_url . "/edit_customer.php?customer_id=" ?>' + field['customer']["0"]["customer_id"] + '">' + field['customer']["0"]["full_name"] + '</a>',
                            '<a href="<?= $site_url . "/edit_customer.php?customer_id=" ?>' + field['reseller']["0"]["customer_id"] + '">' + field['reseller']["0"]["full_name"] + '</a>',
                            field["product_title"],
                            field['creation_date'],
                            field['status'],
                            field['cable_subscriber'],
                            "<a target='_blank' href='<?= $site_url ?>/orders/print_order.php?order_id=" + field['order_id'] + "'><img src='<?= $site_url ?>/img/print-icon.png' style='width: 25px;' /></a>"
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

<title>Statistics - Orders over a Duration</title>
<div class="page-header">
    <a class="last" href="">Statistics - Orders over a Duration</a>    
</div>
<form class="register-form form-inline" method="get">
    <div class="form-group">
        <label for="email">Date 1:</label>
        <input name="date1" readonly="" value="<?=$_GET["date1"]?>" class="form-control datepicker">
        <label for="email">Date 2:</label>
        <input name="date2" readonly="" value="<?=$_GET["date2"]?>" class="form-control datepicker">
    </div>
    <input type="submit" class="btn btn-default" value="Search">
</form>
<br/>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 10%">ID</th>
    <th style="width: 20%">Customer</th>
    <th style="width: 20%">Reseller</th>
    <th style="width: 15%">Product</th>
    <th style="width: 15%">Date</th>
    <th style="width: 5%">Status</th>
    <th style="width: 5%">C.S.</th>
    <th style="width: 10%">Print</th>
</thead>
<tbody>
</tbody>
</table>



<?php
include_once "../footer.php";
?>