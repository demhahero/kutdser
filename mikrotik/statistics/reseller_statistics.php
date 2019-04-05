<?php
include_once "../header.php";
$reseller_id=0;
$dateNow=new DateTime();
$year=(isset($_GET["year"])?$_GET["year"]:$dateNow->format("Y"));
$month=(isset($_GET["month"])?$_GET["month"]:$dateNow->format("m"));
if(isset($_GET["reseller_id"]))
  $reseller_id = intval(filter_input(INPUT_GET, 'reseller_id', FILTER_VALIDATE_INT));

?>


<script>
$(document).ready(function () {
      $('.dataTables_empty').html('<div class="loader"></div>');
      var data_id={
        reseller_id:<?=$reseller_id?>,
        year:<?=$year?>,
        month:<?=$month?>
      };
      var table2=$('#myTable2').DataTable({
          "bProcessing": true,
          "serverSide": true,
          "scrollX": true,   // enables horizontal scrolling
          "createdRow": function ( row, data, index ) {
            //console.log(row);
            //console.log(data);
            //console.log(index);
            $('td', row).eq(6).addClass('bg-success-temp');
            $('td', row).eq(8).addClass('bg-warning-temp');
            $('td', row).eq(9).addClass('bg-danger-temp');
          },
          "ajax":
          {
              url: "<?= $api_url ?>statistics/reseller_statistics_api.php", // json datasource
              type: "post", // type of method  , by default would be get
              data:data_id,
              "dataSrc": function ( json )
              {
                  return json.data;
              },
              error: function ()
              {  // error handling code
                  $("#myTable2").css("display", "none");
              }
          }
      });
      $( "#myTable2 tbody" ).on( "click", ".change_commission", function()
      {
        $('#change_commission').modal({show:true});
        var order_id = $(this).attr('data-id');
        var reseller_commission_percentage = $(this).attr('data-id-2');
        $("input[name=\"order_id\"]").val(order_id);
        $("input[name=\"reseller_commission_percentage\"]").val(reseller_commission_percentage);
      });

      //////////////// form post for reseller commission percentage
      $( ".update-form" ).submit(function( event ) {
          event.preventDefault();
          var order_id=$("input[name=\"order_id\"]").val();
          var reseller_commission_percentage=$("input[name=\"reseller_commission_percentage\"]").val();
          $.post("<?= $api_url ?>statistics/update_order_reseller_commission_percentage_api.php",
            {
              "action":"update_order_reseller_commission_percentage",
              "edit_id":order_id,
              "reseller_commission_percentage": reseller_commission_percentage
            }
          , function (data, status) {
              data = $.parseJSON(data);
              if (data && data.updated == true) {
                  alert("Record updated");
                  $('#change_commission').modal("hide");
                  table2.ajax.reload();
              } else
              {
                alert("Error: udpate record failed, try again later");
                $('#change_commission').modal("hide");
              }
          });
        });
      ///////////////// end form post
      // get total values
      $.post("<?= $api_url ?>statistics/reseller_statistics_total_api.php",
              data_id
      , function (data, status) {
          data = $.parseJSON(data);
        ////////////// add total prices for Commission base, all orders with tax and subtotal
        $("#totalTable").html('<tr>'
            +'<td  class="bg-default">Commission Base Amount </td>'
            +'<td class="bg-default">'+data.commission_base_amount+'$</td>'
            +'<td  class="bg-success-temp">Monthly commission </td>'
            +'<td class="bg-success-temp">'+data.monthly_commission+'$</td>'
            +'<td  class="bg-warning-temp">Total Price for subtotal</td>'
            +'<td class="bg-warning-temp">'+data.subtotal+'$</td>'
            +'<td  class="bg-danger-temp">Total Price for all orders With Tax</td>'
            +'<td class="bg-danger-temp">'+data.total_with_tax+'$</td>'
            +'</tr>');
      ////////////////////////// add total terminated, new and transfer orders
        $("#totalTable").append('<tr>'
            +'<td colspan="2" class="bg-default">Total terminated orders </td>'
            +'<td class="bg-default">'+data.total_terminated+'</td>'
            +'<td  class="bg-default">Total New Orders</td>'
            +'<td class="bg-default">'+data.total_new+'</td>'
            +'<td colspan="2" class="bg-default">Total Transfer Orders</td>'
            +'<td class="bg-default">'+data.total_transfer+'</td>'
            +'</tr>');

        $("#totalTable").append('<tr>'
            +'<td colspan="2" class="bg-default">Total terminated orders per month </td>'
            +'<td class="bg-default">'+data.total_terminated_per_month+'</td>'
            +'<td  class="bg-default">Total New Orders per month</td>'
            +'<td class="bg-default">'+data.total_new_per_month+'</td>'
            +'<td colspan="2" class="bg-default">Total Transfer Orders per month</td>'
            +'<td class="bg-default">'+data.total_transfer_per_month+'</td>'
            +'</tr>');
        });
});
</script>
<style>
.bg-success-temp{
  background-color: #000000;
  color:#FFFFFF;
}
.bg-warning-temp{
  background-color: #0f0690;
  color:#FFFFFF;
}
.bg-danger-temp{
  background-color: #fbf06e;
  color:#000000;
}


</style>
<title>Reseller Statistics</title>
<div class="page-header">
    <a href="customers.php">Customers</a>
    <span class="glyphicon glyphicon-play"></span>
    <a id="customer_full_name" class="last" href=""></a>
</div>
<form class="register-form form-inline" method="get">
    <input name="reseller_id" style="display:none;" value="<?= $_GET["reseller_id"] ?>"/>
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
      <th style="width:50px">ID</th>
      <th style="width:100px">Full Name</th>
      <th style="width:100px">Product</th>
      <th style="width:50px">Product Price</th>
      <th style="width:100px">Valid From</th>
      <th style="width:100px">Commission base amount</th>
      <th style="width:100px">Monthly commission</th>
      <th style="width:50px">Type</th>
      <th style="width:50px">Subtotal</th>
      <th style="width:50px">total with Tax </th>
      <th style="width:50px">Payment Method </th>
      <th style="width:50px">Join Type </th>
      <th style="width:100px">Start Active Date </th>
      <th style="width:50px">Reseller Commission percentage</th>
</thead>
<tbody>

</tbody>
</table>
<table id="totalTable" class="display table table-striped table-bordered">

</table>

<div id="change_commission" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Change Reseller Commission</h4>
      </div>
      <div class="modal-body">

        <form class="update-form">
            <div class="form-group">
                <label>Reseller commission percentage for this order only:</label>
                <input type="number" min="-1" max="100" name="reseller_commission_percentage" value="-1" class="form-control" placeholder="Reseller commission percentage"/>
            </div>
                <input type="hidden" name="order_id" value="-1"/>
            <input type="submit" class="btn btn-primary" value="Change">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>
  </div>
</div>

<?php
include_once "../footer.php";
?>
