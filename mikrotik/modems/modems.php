<?php
include_once "../header.php";
?>

<?php
if (isset($_GET["modem_id"])) {

    $modemTools = $dbTools->objModemTools(intval($_GET["modem_id"]));

    $result = $modemTools->doDelete();

    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>

<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    $('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>modems_api.php", // json datasource
            type: "post", // type of method  , by default would be get
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

<title>Modems</title>
<div class="page-header">
    <h4>Modems</h4>
</div>
<a href="create_modem.php" class="btn btn-primary">+ Create</a>

<br><br>
<table id="myTable2"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%;">ID</th>
    <th style="width: 25%;">MAC Address</th>
    <th style="width: 30%;">Reseller</th>
    <th style="width: 30%;">Customer</th>
    <th style="width: 12%;">Functions</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>
