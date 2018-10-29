<?php
include_once "../header.php";
?>

<?php
$c = curl_init('http://38.104.226.51/ahmed/subscribers_list.php');
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//curl_setopt(... other options you want...)

$html = curl_exec($c);
?>

<script>
    $(document).ready(function () {

        $.getJSON("<?= $api_url ?>customers/customer_edit_api.php?customer_id=<?= $_GET["customer_id"] ?>", function (result) {

                    $("input[name=\"address_line_1\"]").val(result['customer']['address_line_1']);
                    $("input[name=\"address_line_2\"]").val(result['customer']['address_line_2']);
                    $("input[name=\"postal_code\"]").val(result['customer']['postal_code']);
                    $("input[name=\"city\"]").val(result['customer']['city']);
                });

                $(".submit").click(function () {
                    var customer_id = "<?= $_GET['customer_id'] ?>";
                    var address_line_1 = $("input[name=\"address_line_1\"]").val();
                    var address_line_2 = $("input[name=\"address_line_2\"]").val();
                    var postal_code = $("input[name=\"postal_code\"]").val();
                    var city = $("input[name=\"city\"]").val();
                    $.post("<?= $api_url ?>customers/customer_edit_api.php",
                            {customer_id: customer_id, address_line_1: address_line_1, address_line_2: address_line_2,
                                postal_code: postal_code, city: city}
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

<title>Customer Details</title>
<div class="page-header">
    <a class="last" href="">Customer Details</a>
</div>


<form class="register-form" method="post">
    <div class="form-group">
        <label>Address:</label>
        <div class="form-group">
            <label>Address line 1:</label>
            <input type="text" name="address_line_1" class="form-control" />
        </div>
        <div class="form-group">
            <label>Address line 2:</label>
            <input type="text" name="address_line_2" class="form-control" />
        </div>
        <div class="form-group">
            <label>Postal code:</label>
            <input type="text" name="postal_code" class="form-control" />
        </div>
        <div class="form-group">
            <label>City:</label>
            <input type="text" name="city" class="form-control" />
        </div>
    </div>
    <input type="submit" class="btn btn-default submit"  value="Send">
</form>

<?php
include_once "../footer.php";
?>
