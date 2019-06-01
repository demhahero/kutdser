<?php
include_once "../header.php";
?>


<script>
    $(document).ready(function () {

        $.getJSON("<?= $api_url ?>orders/edit_last_recurring_price_api.php?order_id=<?= $_GET["order_id"] ?>", function (result) {

            $(".product-name").html(result["order"]["product_title"]);
            $(".start-active-date").html(result["order"]["start_active_date"]);
            $(".product-subscription-type").html(result["order"]["product_subscription_type"]);
            $("input[name=\"product_price\"]").val(result["order"]["product_price"]);
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

<p>Product: <b class="product-name"></b></p>
<p>Recurring Started on: <b class="start-active-date"></b></p>
<p>Subscription type: <b class="product-subscription-type"></b></p>

<form class="register-form" method="post">
    <div class="form-group">
        <div class="form-group">
            <label>Product price:</label>
            <input type="text" name="product_price" class="form-control" />
        </div>
    </div>
    <input type="submit" class="btn btn-default submit"  value="Send">
</form>

<?php
include_once "../footer.php";
?>
