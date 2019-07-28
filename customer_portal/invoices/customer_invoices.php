<?php
include_once "../header.php";

$dateNow=new DateTime();
$year=(isset($_GET["year"])?$_GET["year"]:$dateNow->format("Y"));
$month=(isset($_GET["month"])?$_GET["month"]:$dateNow->format("m"));

?>


<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');
    var data_id={
      customer_id:<?=$customer_id?>,
      year:<?=$year?>,
      month:<?=$month?>
    };
    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>customer_portal/customer_invoices_api.php", // json datasource
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
<style>
.dataTables_wrapper .row{
  width:100%
}
</style>

<title> invoices</title>
<div class="page-header">
    <span class="glyphicon glyphicon-play"></span>
</div>
<form class="register-form form-inline" method="get">
    <input name="customer_id" style="display:none;" value="<?= $customer_id ?>"/>
    <div class="form-group">
        <label for="email">Month:</label>
        <select  name="month" class="form-control">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                if ($month == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
        <label for="email">Year:</label>
        <select  name="year" class="form-control">
            <?php
            for ($i = 2017; $i <= 2020; $i++) {
                if ($year == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
    </div>
    <input type="submit" class="btn btn-default" value="Search">
</form>

<table id="myTable2" class="display table table-striped table-bordered">
    <thead>
    <th>invoice ID</th>
    <th>Order ID</th>
    <th>Type</th>
    <th> Valid Date From</th>
    <th> Valid Date to</th>
    <th>Print</th>
</thead>
<tbody>

</tbody>
</table>

<?php
include_once "../footer.php";
?>
