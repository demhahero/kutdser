<?php
include_once "../header.php";
$month=isset($_GET["month"])?$_GET["month"]:5;
$year=isset($_GET["year"])?$_GET["year"]:2018;
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

        $.getJSON("<?= $api_url ?>orders_by_month_for_customer.php?customer_id=<?= $_GET["customer_id"] ?>&month=<?= $month ?>&year=<?= $year ?>", function (result) {
          var total=0;
          var totalWoR=0;
					var totalWT=0;
					$.each(result['orders'], function (i, field) {
            $('#customer_header').html("Customer Name: "+field["customer_name"]+', Reseller Name: '+field["reseller_name"])
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
							+'<td class="bg-danger">'+parseFloat(monthInfo["total_price_with_tax_p7"]).toFixed(2)+'</td>'
							+'</tr>');

                        });



                    });

					$("table.invoice").append('<tr>'
							+'<td></td>'
              +'<td colspan="3" class="bg-success">Total Price for all orders </td>'
							+'<td class="bg-success">'+totalWoR.toFixed(2)+'$</td>'
              //+'<td></td>'
							//+'<td></td>'
							//+'<td></td>'
							+'<td colspan="4" class="bg-warning">Total Price for all orders with additional prices</td>'
							+'<td class="bg-warning">'+total.toFixed(2)+'$</td>'
							+'<td colspan="2" class="bg-danger">Total Price for all orders With Tax</td>'
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

<title>Customer by month</title>
<div class="page-header">
    <h4>Statistics for month <?= $_GET["month"] ?> year <?= $_GET["year"] ?> </h4>
    <h3 id="customer_header"></h3>
</div>
<form class="register-form form-inline" method="get">
    <input name="customer_id" style="display:none;" value="<?= $_GET["customer_id"] ?>"/>
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


<br><br>
<h5>Month Info</h5>
<!--
<table class="display table table-striped table-bordered">
    <tr>
        <td style="width:20%;">Product Title</td>
        <td class="product-title"></td>
    </tr>
    <tr>
        <td style="width:20%;">Product Price</td>
        <td class="product-price"></td>
    </tr>
    <tr>
        <td style="width:20%;">Remaining Days Price</td>
        <td class="remaining-days-price"></td>
    </tr>
    <tr>
        <td style="width:20%;">Router Price</td>
        <td class="router-price"></td>
    </tr>
    <tr>
        <td style="width:20%;">Modem Price</td>
        <td class="modem-price"></td>
    </tr>
    <tr>
        <td style="width:20%;">Setup Price</td>
        <td class="setup-price"></td>
    </tr>
    <tr>
        <td style="width:20%;">Additional Service Price</td>
        <td class="additional-service-price"></td>
    </tr>
	<tr>
        <td style="width:20%;">qst tax</td>
        <td class="qst_tax"></td>
    </tr>
	<tr>
        <td style="width:20%;">gst tax</td>
        <td class="gst_tax"></td>
    </tr>
	<tr>
        <td style="width:20%;">Total Price</td>
        <td class="total-price"></td>
    </tr>
</table>
-->
<table class="invoice display table table-striped table-bordered">
    <tr>
        <td >Product Title</td>
        <td >Payment Method</td>
        <td >Product Price</td>
        <td >Remaining Days Price</td>
        <td> Commission base amount </td>
        <td >Router Price</td>
        <td >Modem Price</td>
        <td >Setup Price</td>
        <td >Additional Service Price</td>
		    <td >Subtotal</td>
        <td >qst tax</td>
        <td >gst tax</td>
        <td >Total Price With Tax</td>

    </tr>
</table>
<h5>Order</h5>
<table class="orders display table table-striped table-bordered">
<tr>
  <td>#No</td>
  <td>Title</td>
  <td>Start Active Date</td>
  <td>Recurring Date</td>
  <td>Price</td>
</tr>
</table>

<h5>Requests</h5>
<table class="requests display table table-striped table-bordered">
    <tr>
        <td style="width:20%;">Action</td>
        <td style="width:20%;">Action on Date</td>
        <td style="width:20%;">Product Title</td>
        <td style="width:20%;">Product Price</td>

    </tr>
</table>
<?php
include_once "../footer.php";
?>
