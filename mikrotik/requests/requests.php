<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?=$api_url?>requests_api.php", function (result) {
            $.each(result['requests'], function (i, field) {
                action_on_date="";
                if(field['action_on_date'] != null){
                    action_on_date = field['action_on_date'].split(' ');
                    action_on_date = action_on_date[0];
                }
                table.row.add([
                    '<a href="request_details.php?request_id='+field['request_id']+'" >'+field['request_id']+'</a>',
                    field['order']["0"]["order_id"],
                    field['customer']["0"]["full_name"],
                    field['reseller']["0"]["full_name"],
                    field['action'],
                    field['product_title'],
                    action_on_date,
                    field['creation_date'],
                    field['verdict'],
                    field['admin']["0"]["username"]
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

<title>Requests</title>
<div class="page-header">
    <h4>Requests</h4>    
</div>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%;">ID</th>
    <th style="width: 10%;">Order</th>
    <th style="width: 15%;">Customer</th>
    <th style="width: 15%;">Reseller</th>
    <th style="width: 5%;">Action</th>
    <th style="width: 5%;">Product title</th>
    <th style="width: 10%;">Action on Date</th>
    <th style="width: 15%;">Date</th>
    <th style="width: 10%;">Verdict</th>
    <th style="width: 10%;">Admin</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>