<?php
include_once "../header.php";
?>

<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>customers/resellers_api.php", // json datasource
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

<title>Resellers</title>
<div class="page-header">
    <a class="last" href="">Resellers</a>
</div>
<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Customers</th>
    <th>Resellers Statistics</th>
    <th>Statistics</th>
    <th>Edit discount</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
