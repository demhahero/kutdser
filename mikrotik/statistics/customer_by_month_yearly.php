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

        $.getJSON("<?= $api_url ?>orders_by_month_for_customer_yearly.php?customer_id=<?= $_GET["customer_id"] ?>&month=<?= $_GET["month"] ?>&year=<?= $_GET["year"] ?>", function (result) {
                    var total=0;
					var totalWT=0;
					$.each(result['orders'], function (i, field) {
                        var product_price;
                        var product_title;
                        var request_product_price;
                        var request_product_title;
                        var request_action_on_date;
                        var current_product_price;
                        var current_product_title;

                        $(".order-product-title").html(field["product_title"]);
                        $(".order-product-price").html(field["product_price"] + "$");

						if(field["requests"].length===1)
						{
							$.each(field["requests"], function (i2, field2) {
								$("table.requests").append('<tr><td>' + field2["action"] + '</td><td>' + field2["action_on_date"] + '</td><td>' + field2["product_title"] + "$" + '</td><td>' + field2["product_price"] + '</td></tr>');
							});
						}
						else{
							for(var i=field["requests"].length-2;i>=0;i--)
							{
								
									
								$("table.requests").append(
								'<tr><td>' 
								+ field["requests"][i]["action"] 
								+ '</td><td>' 
								+ field["requests"][i]["action_on_date"] 
								+ '</td><td>' 
								+ field["requests"][i]["product_title"] + "$" 
								+ '</td><td>' 
								+ field["requests"][i]["product_price"] + '</td></tr>');
							}
						}

						$.each(field["monthInfo"], function (i2, monthInfo) {
							var product_price=parseFloat(monthInfo["product_price"]).toFixed(2);
							var product_title=monthInfo["product_title"];
							var days=monthInfo["days"];
							if(monthInfo["product_price_2"])
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
							total+= parseFloat(monthInfo["total_price_with_out_tax"]);
							totalWT+= parseFloat(monthInfo["total_price_with_tax_p7"]);
							$("table.invoice").append('<tr>'
							+'<td>'+product_title+'</td>'
							+'<td>'+product_price+'</td>'
							+'<td>'+parseFloat(monthInfo["remaining_days_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["router_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["modem_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["setup_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["additional_service_price"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["total_price_with_out_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["qst_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["gst_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["total_price_with_tax"]).toFixed(2)+'</td>'
							+'<td>'+parseFloat(monthInfo["total_price_with_tax_p7"]).toFixed(2)+'</td>'
							+'</tr>');

                        });



                    });

					$("table.invoice").append('<tr>'
							+'<td></td>'
							+'<td></td>'
							+'<td></td>'
							+'<td></td>'
							//+'<td></td>'
							//+'<td></td>'
							+'<td colspan="3">Total Price for all orders</td>'
							+'<td>'+total.toFixed(2)+'</td>'
							+'<td colspan="3">Total Price with Tax for all orders</td>'
							+'<td>'+totalWT.toFixed(2)+'</td>'
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
    <h4>Customer by month</h4>
</div>

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
        <td style="width:20%;">Product Title</td>

        <td style="width:20%;">Product Price</td>

        <td style="width:20%;">Remaining Days Price</td>

        <td style="width:20%;">Router Price</td>

        <td style="width:20%;">Modem Price</td>

        <td style="width:20%;">Setup Price</td>

        <td style="width:20%;">Additional Service Price</td>

        <td style="width:20%;">Total price WoT</td>

        <td style="width:20%;">qst tax</td>

        <td style="width:20%;">gst tax</td>

        <td style="width:20%;">Total price WT</td>

        <td style="width:20%;">Total Price Plus 7$ CST</td>

    </tr>
</table>
<h5>Order</h5>
<table class="display table table-striped table-bordered">
    <tr>
        <td style="width:20%;">Order - Product Title</td>
        <td class="order-product-title"></td>
    </tr>
    <tr>
        <td style="width:20%;">Order - Product Price</td>
        <td class="order-product-price"></td>
    </tr>
</table>

<h5>Requests</h5>
<table class="requests display table table-striped table-bordered">
    <tr>
        <td style="width:20%;">action</td>
        <td style="width:20%;">Date</td>
        <td style="width:20%;">Product Title</td>
        <td style="width:20%;">Product Price</td>

    </tr>
</table>
<?php
include_once "../footer.php";
?>
