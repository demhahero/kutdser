<?php
include_once "../header.php";

$reseller_id=0;
if(isset($_GET["reseller_id"]))
  $reseller_id = intval($_GET["reseller_id"]);
?>

<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var data_id={
      "data_id":<?=$reseller_id?>
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= $api_url ?>customers/reseller_customers_api.php", // json datasource
            "type": "post", // type of method  , by default would be get
            "data": data_id,
            "dataSrc": function ( json ) {

                $("#reseller_full_name").html(json.reseller_full_name+"'s customers");
                document.title=json.reseller_full_name+"'s customers";
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
</style>

<title>'s customers</title>
<div class="page-header">
    <a href="resellers.php">Resellers</a>
    <span class="glyphicon glyphicon-play"></span>
    <a id="reseller_full_name" class="last" href=""></a>
</div>
<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Invoices</th>
    <th>Orders</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
