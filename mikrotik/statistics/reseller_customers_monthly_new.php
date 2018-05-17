<?php
include_once "../header.php";

$month=isset($_GET["month"])?$_GET["month"]:5;
$year=isset($_GET["year"])?$_GET["year"]:2018;
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?= $api_url ?>orders_by_month_for_reseller.php?reseller_id=<?= $_GET["reseller_id"] ?>&month=<?= $month ?>&year=<?= $year ?>", function (result) {
          var total=0;
          var totalWoR=0;
					var totalWT=0;
          $.each(result['customers'], function (index, customers) {

					$.each(customers['orders'], function (i, field) {
            $(".last").html(field["reseller_name"]+"'s Customers")
                        var product_price;
                        var product_title;
                        var request_product_price;
                        var request_product_title;
                        var request_action_on_date;
                        var current_product_price;
                        var current_product_title;
                        $("table.orders").append(
                        '<tr>'
                            +'<td style="width:20%;">#'+(i+1)+'</td>'
                            +'<td >'+field["product_title"]+'</td>'
                            +'<td style="width:20%;">'+field["start_active_date"]+'</td>'
                            +'<td style="width:20%;">'+field["recurring_date"]+'</td>'
                            +'<td>'+field["product_price"]+'</td>'
          							+'</tr>');



                        $.each(field["requests"], function (i2, field2) {
                            $("table.requests").append('<tr><td>' + field2["action"] + '</td><td>' + field2["action_on_date"] + '</td><td>' + field2["product_title"] + "" + '</td><td>' + field2["product_price"] + '$</td></tr>');
                        });

						$.each(field["monthInfo"], function (i2, monthInfo) {
							var product_price=parseFloat(monthInfo["product_price"]).toFixed(2);
							var product_title=monthInfo["product_title"];
							var days=monthInfo["days"];
              if (typeof monthInfo["product_price_2"] !== 'undefined' && monthInfo["product_price_2"] !== null)

							{
								product_title=monthInfo["product_title"]+" ("+monthInfo["days"]+" days), "+monthInfo["product_title_2"]+" ("+monthInfo["days_2"]+" days)";
								product_price=monthInfo["product_price"].toFixed(2)+"$, "+monthInfo["product_price_2"].toFixed(2)+"$";
							}
							/*
							$(".product-title").html(product_title);
							$(".product-price").html(product_price);
							$(".remaining-days-price").html(monthInfo["remaining_days_price"]);
							$(".router-price").html(monthInfo["router_price"]);
							$(".modem-price").html(monthInfo["modem_price"]);
							$(".setup-price").html(monthInfo["setup_price"]);
							$(".additional-service-price").html(monthInfo["additional_service_price"]);
							$(".qst_tax").html(monthInfo["qst_tax"]);
							$(".gst_tax").html(monthInfo["gst_tax"]);
							$(".total-price").html(monthInfo["total_price"]);
							*/
              totalWoR+= parseFloat(monthInfo["total_price_with_out_router"]);
							total+= parseFloat(monthInfo["total_price_with_out_tax"]);
							totalWT+= parseFloat(monthInfo["total_price_with_tax_p7"]);
              table.row.add([
                  customers['customer_id'],
                  customers['full_name'],
                  product_title,
                  field['payment_method'],
                  field['start_active_date'],
                  field['join_type'],
                  field['recurring_date'],
                  monthInfo["total_price_with_out_router"],
                  monthInfo["action"],
                  monthInfo["total_price_with_out_tax"],
                  monthInfo["total_price_with_tax_p7"]
              ]).draw(false);
/*
							$("table.invoice").append('<tr>'
							+'<td>'+product_title+'</td>'
              +'<td>'+field["payment_method"]+'</td>'
							+'<td>'+product_price+'</td>'
							+'<td>'+parseFloat(monthInfo["remaining_days_price"]).toFixed(2)+'</td>'
              +'<td class="bg-success">'+parseFloat(monthInfo["total_price_with_out_router"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["router_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["modem_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["setup_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["additional_service_price"]).toFixed(2)+'</td>'
							+'<td class="bg-warning">'+parseFloat(monthInfo["total_price_with_out_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["qst_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["gst_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["total_price_with_tax"]).toFixed(2)+'</td>'
							+'<td class="bg-danger">'+parseFloat(monthInfo["total_price_with_tax_p7"]).toFixed(2)+'</td>'
							+'</tr>');
*/
                        });



                    });

          });


					$("#totalTable").append('<tr>'
							+'<td></td>'
              +'<td colspan="3" class="bg-success">Total Price for all orders </td>'
							+'<td class="bg-success">'+totalWoR.toFixed(2)+'$</td>'
              //+'<td></td>'
							//+'<td></td>'
							//+'<td></td>'
							+'<td colspan="4" class="bg-warning">Total Price for all orders with additional prices</td>'
							+'<td class="bg-warning">'+total.toFixed(2)+'$</td>'
							+'<td colspan="3" class="bg-danger">Total Price for all orders With Tax</td>'
							+'<td class="bg-danger">'+totalWT.toFixed(2)+'$</td>'
							+'</tr>');

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

<title><?= "reseller" //$reseller->getFullName(); ?>'s customers</title>
<div class="page-header">
    <a href="resellers.php">Resellers</a>
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href=""></a>
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
    <a href="reseller_customers_monthly_generateXLS.php?reseller_id=<?=$_GET["reseller_id"]?>" class="btn btn-primary">XML</a>
</form>


<br><br>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Product</th>
    <th>Payment Method</th>
    <th>Start Date</th>
    <th>Join Type</th>
    <th>Recurring Start</th>
    <th>Commission base amount</th>
    <th>Type</th>
    <th>Subtotal</th>
    <th>total with Tax </th>
</thead>
<tbody>


</tbody>
</table>
<table id="totalTable" class="display table table-striped table-bordered">

</table>
<p id="total_price_with_out_router"></p>
<p id="total_price_with_out_tax"></p>
<p id="total_price_with_tax_p7"></p>
<?php
//echo "Total without tax:" . number_format((float) $total_without_tax, 2, '.', '') . "$";
//echo "<br>";
//echo "Total with tax:" . number_format((float) $total_with_tax, 2, '.', '') . "$";
?>
<?php
include_once "../footer.php";
?>
