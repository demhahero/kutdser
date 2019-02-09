<?php
include_once "../header.php";
$order_id=0;
$year=(isset($_GET["year"])?$_GET["year"]:1990);
$month=(isset($_GET["month"])?$_GET["month"]:1);
if(isset($_GET["order_id"]))
  $order_id = intval(filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT));

?>


<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');
    var data_id={
      order_id:<?=$order_id?>,
      year:<?=$year?>,
      month:<?=$month?>
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>statistics/customer_invoices_api.php", // json datasource
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
    <th>invoice ID</th>
    <th>Order ID</th>
    <th>Type</th>
    <th> Valid Date From</th>
    <th> Valid Date to</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
