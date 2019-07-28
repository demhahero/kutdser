<?php
include_once "../header.php";

?>

<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var data_id={
      "data_id":0
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= $api_url ?>customer_portal/customer_orders_api.php", // json datasource
            "type": "post", // type of method  , by default would be get
            "data": data_id,
            "dataSrc": function ( json ) {

                $("#customer_full_name").html(json.customer_full_name+"'s orders");
                document.title=json.customer_full_name+"'s orders";
                return json.data;
              },
            error: function () {  // error handling code
                $("#myTable2").css("display", "none");
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

    .dataTables_wrapper .row{
      width:100%
    }
</style>
<title>Customer's Orders</title>
<div class="page-header">
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" id="customer_full_name" href=""></a>
</div>
<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Product</th>
    <th>Creation Date</th>
    <th>Make request</th>
</thead>

<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
