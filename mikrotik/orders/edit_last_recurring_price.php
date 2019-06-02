<?php
include_once "../header.php";
?>


<script>
    $(document).ready(function () {

        $.getJSON("<?= $api_url ?>orders/edit_last_recurring_price_api.php?order_id=<?= $_GET["order_id"] ?>", function (result) {
            $(".full-name").html(result["order"]["full_name"]);
            $(".product-name").html(result["order"]["product_title"]);
            $(".start-active-date").html(result["order"]["start_active_date"]);
            $(".offer-end").html(result["order"]["offer_end"]);
            
            $(".product-subscription-type").html(result["order"]["product_subscription_type"]);
            $("input[name=\"product_price\"]").val(result["order"]["last_invoice_product_price"]);
            
            if(result["order"]["last_invoice_product_price"] != "39.9" 
                    && result["order"]["last_invoice_product_price"] != "377.23"){
                alert("CAREFULL, this order has been updated");
                $(".caution").show();
            }
        });
        
        $(".submit").click(function () {
            var order_id = "<?= $_GET['order_id'] ?>";
            var product_price = $("input[name=\"product_price\"]").val();

            $.post("<?= $api_url ?>orders/edit_last_recurring_price_api.php",
                    {order_id: order_id, product_price: product_price}
            , function (data, status) {
                data = $.parseJSON(data);
                if (data.inserted == true) {
                    alert("address updated");
                } else
                    alert("Error, try again");
            });
            return false;
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

<title>Customer - Edit Recurring</title>
<div class="page-header">
    <a class="last" href="">Customer - Edit Recurring</a>
</div>

<p>Full Name: <b class="full-name"></b></p>
<p>Product: <b class="product-name"></b></p>
<p>First Payment: <b class="start-active-date"></b></p>
<p>Offer ends on: <b class="offer-end"></b></p>
<p>Subscription type: <b class="product-subscription-type"></b></p>

<form class="register-form" method="post">
    <div class="form-group">
        <div class="form-group">
            <label>Product price: 
                <br/><b style="color: gray;">Internet 30 Mbps MONTHLY 44.90$</b> 
                <br/> <b style="color: gray;">Internet 30 Mbps YEARLY 538.90$</b> 
                <br/> <b class="caution" style="display: none; color: red; font-size: 25px;">Caution! This price is not the original offer price!!!</b> 
            </label>
                
            <input type="text" name="product_price" class="form-control" />
        </div>
    </div>
    <input type="submit" class="btn btn-default submit"  value="Send">
</form>



<?php
include_once "../footer.php";
?>
