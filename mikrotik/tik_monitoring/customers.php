<?php
include_once "../header.php";
?>


<script>
    $(document).ready(function () {

        $('.dataTables_empty').html('<div class="loader"></div>');

        $('#myTable2').DataTable({
            "order": [[0, "desc"]],
            "bProcessing": true,
            "serverSide": true,
            "ajax": {
                url: "<?= $api_url ?>tik_monitoring_customers_api.php", // json datasource
                type: "post", // type of method  , by default would be get
                error: function () {  // error handling code
                    $("#employee_grid_processing").css("display", "none");
                }
            }
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

<title>Support</title>
<div class="page-header">
    <a class="last" href="">Support</a>    
</div>
<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%">ID</th>
    <th style="width: 17%">Full Name</th>
    <th style="width: 17%">Reseller</th>
    <th style="width: 13%">Phone</th>
    <th style="width: 13%">Modem MAC</th>
    <th style="width: 13%">Router MAC</th>
    <th style="width: 12%">IP</th>
    <th style="width: 10%">VL Number</th>
    <th>Address</th>
    <th>Merchant Ref</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>