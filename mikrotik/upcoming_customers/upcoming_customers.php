<?php
include_once "../header.php";
?>

<?php
if (isset($_GET["upcoming_customer_id"])) {
    $result = $dbTools->objUpcomingCustomerTools($_GET["upcoming_customer_id"])->doDelete();
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>upcoming_customers/upcoming_customers_api.php", // json datasource
            type: "post", // type of method  , by default would be get
            error: function () {  // error handling code
                $("#myTable2").css("display", "none");
            }
        }
    });
    $( "#myTable2 tbody" ).on( "click", ".edit", function() {
      var edit_id = $(this).attr('data-id');
      window.location.href = "edit_upcoming_customer.php?upcoming_customer_id="+edit_id;
    });
    $( "#myTable2 tbody" ).on( "click", ".remove", function() {

        var delete_id = $(this).attr('data-id');
        $.post("<?= $api_url ?>upcoming_customers/delete_upcoming_customer_api.php",
                {
                  delete_id: delete_id
                }
        , function (data, status) {
            data = $.parseJSON(data);
            if (data.deleted == true) {
                alert("Record deleted");
                table2.ajax.reload();
            } else
                alert("Error: delete record failed, try again later");
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


<title>Upcoming Customers</title>
<div class="page-header">
    <h4>Upcoming Customers</h4>
</div>
<a href="create_upcoming_customer.php" class="btn btn-primary">+ Create</a>
<br/><br/>
<table id="myTable2"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Creation Date</th>
    <th>Admin</th>
    <th>Functions</th>
</thead>
<tbody>

        <tr>
            <td style="width: 5%;"></td>
            <td style="width: 25%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 10%;">

            </td>
        </tr>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
