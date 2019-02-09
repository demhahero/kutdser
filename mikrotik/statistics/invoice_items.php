<?php
include_once "../header.php";
$invoice_id=0;
if(isset($_GET["invoice_id"]))
  $invoice_id = intval(filter_input(INPUT_GET, 'invoice_id', FILTER_VALIDATE_INT));

?>


<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');
    var data_id={
      invoice_id:<?=$invoice_id?>
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>statistics/invoice_items_api.php", // json datasource
            type: "post", // type of method  , by default would be get
            data:data_id,
            "dataSrc": function ( json ) {

                return json.data;
              },
            error: function () {  // error handling code
                $("#myTable2").css("display", "none");
            }
        }
    });

});
</script>

<title>'s invoices</title>
<div class="page-header">
    <a href="customers.php">Customers</a>
    <span class="glyphicon glyphicon-play"></span>
    <a id="customer_full_name" class="last" href=""></a>
</div>
<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Name</th>
    <th>Base Price</th>
    <th>Paid type</th>
    <th>Paid Price</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
