<?php
include_once "../header.php";
?>

<?php
$reseller_id = intval($_GET["reseller_id"]);
$reseller = $dbTools->query("SELECT full_name from customers where customer_id=" . $reseller_id);
$reseller_full_name="";
while ($reseller_row = $dbTools->fetch_assoc($reseller)) {
	$reseller_full_name=$reseller_row["full_name"];
}
?>
<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');
	
        $.getJSON("<?= $api_url ?>orders_by_month_for_reseller.php?reseller_id=<?= $_GET["reseller_id"] ?>&month=4&year=2018", function (result) {
                    var total=0;
					var odd_even=false
					$.each(result['customers'], function (i, customer) {
                        
						
						$.each(customer["orders"], function (i2, orders) {
							
							$.each(orders["monthInfo"], function (i3, monthInfo) {
								var product_price=parseFloat(monthInfo["product_price"]).toFixed(2);
								var product_title=monthInfo["product_title"];
								var days=monthInfo["days"];
								if(monthInfo["product_price_2"])
								{
									product_title=monthInfo["product_title"]+" ("+monthInfo["days"]+" days), "+monthInfo["product_title_2"]+" ("+monthInfo["days_2"]+" days)";
									product_price=monthInfo["product_price"].toFixed(2)+"$, "+monthInfo["product_price_2"].toFixed(2)+"$";
								}
								
								total+= parseFloat(monthInfo["total_price"]);
								var style='role="row" class="odd"';
								if(odd_even)
								{
									style='role="row" class="even"';
									odd_even=false;
								}
								else{
									odd_even=true;
								}
								
								$(".customers_table").append('<tr '+style+'>'
								+'<td style="width: 5%;">'+customer["customer_id"]+'</td>'
								+'<td style="width: 25%;">'+customer["full_name"]+'</td>'
								+'<td style="width: 20%;">'+product_title+'</td>'
								+'<td style="width: 15%;">'+orders["start_active_date"]+'</td>'
								+'<td style="width: 15%;">'+orders["recurring_date"]+'</td>'
								+'<td style="width: 10%;">'+product_price+'</td>'
								+'<td style="width: 10%;">'+parseFloat(monthInfo["total_price"]).toFixed(2)+'</td>'
								/*
								+'<td>'+parseFloat(monthInfo["remaining_days_price"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["router_price"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["modem_price"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["setup_price"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["additional_service_price"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["qst_tax"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["gst_tax"]).toFixed(2)+'</td>'
								+'<td>'+parseFloat(monthInfo["total_price"]).toFixed(2)+'</td>'
								*/
								+'</tr>');
								
							});
						});
                        
                        
                    });
					
					$(".total").html('Total without tax: '+total.toFixed(2)+'$');
							
                });
            });
</script>
<title><?= $reseller_full_name; ?>'s customers</title>
<div class="page-header">  
    <a href="resellers.php">Resellers</a> 
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href=""><?= $reseller_full_name; ?>'s customers</a>  
</div>

<a href="reseller_customers_monthly_generateXLS.php?reseller_id=<?=$reseller_id?>" class="btn btn-primary">XML</a>

<br><br>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Product</th>
    <th>Start Date</th>
    <th>Recurring Start</th>
    <th>Product price</th>
    <th>total</th>
</thead>
<tbody class="customers_table">
    
   
</tbody>
</table>
<div class="total">
</div>
<div class="total_with_tax">
</div>
<?php
include_once "../footer.php";
?>