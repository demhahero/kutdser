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

        $.getJSON("<?= $api_url ?>orders_by_month_for_customer.php?customer_id=<?= $_GET["customer_id"] ?>&month=<?= $_GET["month"] ?>&year=<?= $_GET["year"] ?>", function (result) {
                    $.each(result['orders'], function (i, field) {
                        var product_price;
                        var product_title;
                        var request_product_price;
                        var request_product_title;
                        var request_action_on_date;
                        
                        $(".order-product-title").html(field["product_title"]);
                        $(".order-product-price").html(field["product_price"] + "$");
                        
                        product_price = field["product_price"];
                        product_title = field["product_title"];
                        
                        $.each(field["requests"], function (i2, field2) {
                            $(".request-creation-date").html(field2["creation_date"]);
                            $(".request-action-on-date").html(field2["action_on_date"]);
                            $(".request-product-price").html(field2["product_price"] + "$");
                            $(".request-product-title").html(field2["product_title"]);
                            request_action_on_date = new Date(field2["action_on_date"]);
                            request_product_price = field2["product_price"];
                            request_product_title = field2["product_title"];
                        });

                        var start_active_date = new Date(field["start_active_date"]);
                        var selected_date = new Date("<?= $_GET["year"] ?>-<?= $_GET["month"] ?>-02");

                        if (start_active_date.getMonth() == 11) {
                            var start_active_date_next_month = new Date(start_active_date.getFullYear() + 1, 0, 1);
                        } else {
                            var start_active_date_next_month = new Date(start_active_date.getFullYear(), start_active_date.getMonth() + 1, 1);
                        }

                        if (start_active_date.getFullYear() == selected_date.getFullYear() 
                                && start_active_date.getMonth() == selected_date.getMonth()) {
                            $(".product-price").html(product_price);
                            $(".product-title").html(product_title);
                            if (parseFloat(field["remaining_days_price"]) == 0) {
                                $(".remaining-days-price").html(field["remaining_days_price"]);
                            } else {
                                $(".remaining-days-price").html(field["remaining_days_price"]);
                            }
                            $(".router-price").html(field["router_price"]);
                            $(".modem-price").html(field["modem_price"]);
                            $(".setup-price").html(field["setup_price"]);
                            $(".additional-service-price").html(field["additional_service_price"]);
                        } else if (parseFloat(field["remaining_days_price"]) > 0 
                                && start_active_date_next_month.getFullYear() == selected_date.getFullYear() 
                                && start_active_date_next_month.getMonth() == selected_date.getMonth()) {
                            
                            if(request_product_price != null){
                                var days_in_month = new Date("<?= $_GET["year"] ?>", "<?= $_GET["month"] ?>", 0).getDate();
                                $(".product-title").html(product_title+", "+request_product_title);
                                $(".product-price").html(product_price+", "+request_product_price);
                                var second_duration = days_in_month - request_action_on_date.getDate();
                                var first_duration = days_in_month - second_duration;
                                var product_day_price = product_price/days_in_month;
                                var request_product_day_price = request_product_price/days_in_month;
                                if(request_action_on_date.getDate()!=1){
                                    $(".product-title").html(product_title+"("+first_duration+"), "+request_product_title+"("+second_duration+")");
                                    $(".product-price").html((product_day_price*first_duration).toFixed(2)+", "+(request_product_day_price*second_duration).toFixed(2));
                                }else{
                                    $(".product-title").html(request_product_title);
                                    $(".product-price").html(request_product_price);
                                }
                            } else{
                                $(".product-title").html(product_title);
                                $(".product-price").html(product_price);
                            }
                        } else{
                            if(request_product_price != null){
                                $(".product-title").html(request_product_title);
                                $(".product-price").html(request_product_price);
                            } else{
                                $(".product-title").html(product_title);
                                $(".product-price").html(product_price);
                            }
                            $(".additional-service-price").html(field["additional_service_price"]);
                        }
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

<title>Customer by month</title>
<div class="page-header">
    <h4>Customer by month</h4>    
</div>

<h5>Month Info</h5>
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
<table class="display table table-striped table-bordered">
    <tr>
        <td style="width:20%;">Request - Creation Date</td>
        <td class="request-creation-date"></td>
    </tr>
    <tr>
        <td style="width:20%;">Request - Action on Date</td>
        <td class="request-action-on-date"></td>
    </tr>
    <tr>
        <td style="width:20%;">Request - Product Title</td>
        <td class="request-product-title"></td>
    </tr>
    <tr>
        <td style="width:20%;">Request - Product Price</td>
        <td class="request-product-price"></td>
    </tr>  
</table>
<?php
include_once "../footer.php";
?>