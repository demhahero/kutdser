<?php
include_once "../header.php";
$customer_id=0;
if(isset($_GET["customer_id"]))
  $customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));

?>


<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');
    var data_id={
      data_id:<?=$customer_id?>
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>customers/customer_invoice_api.php", // json datasource
            type: "post", // type of method  , by default would be get
            data:data_id,
            "dataSrc": function ( json ) {

                $("#customer_full_name").html(json.customer_full_name+"'s invoices");
                document.title=json.customer_full_name+"'s invoices";
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
    <th>Year - Month</th>
    <th>Invoice</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
