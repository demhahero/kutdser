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
        $('.dataTables_empty').html('<div class="loader"></div>');
        var subscribers_list = <?= $html ?>;

        $.getJSON("<?= $api_url ?>tik_monitoring_customer_api.php?customer_id=<?=$_GET["customer_id"]?>", function (result) {

            $.each(result['customers'], function (i, field) {
                var subscriber = subscribers_list.find(function (e) {
                    return e.mac_address == field['modem'][0]["mac_address"].toLowerCase()
                });
                var ip_address = "";
                var plan = "";
                var router_mac_address = "";
                if(subscriber){
                    ip_address = subscriber.ip_address;
                    plan = subscriber.plan;
                    router_mac_address = subscriber.router_mac_address;
                }
                $(".customer-id").html(field['customer_id']);
                $(".full-name").html(field['full_name']);
                $(".reseller").html(field['reseller'][0]['full_name']);
                $(".modem-mac").html(field['modem'][0]["mac_address"]);
                $(".router-mac").html(router_mac_address);
                $(".plan").html(plan);
                $(".address").html(field['address']);
                $(".phone").html(field['phone']);
                $(".ip").html(ip_address);
            });
        });
        
        $.getJSON("<?= $api_url ?>customer_log_api.php?customer_id=<?=$_GET["customer_id"]?>", function (result) {

            $.each(result['customers'], function (i, field) {
                var subscriber = subscribers_list.find(function (e) {
                    return e.mac_address == field['modem'][0]["mac_address"].toLowerCase()
                });
                var ip_address = "";
                var plan = "";
                var router_mac_address = "";
                if(subscriber){
                    ip_address = subscriber.ip_address;
                    plan = subscriber.plan;
                    router_mac_address = subscriber.router_mac_address;
                }
                $(".customer-id").html(field['customer_id']);
                $(".full-name").html(field['full_name']);
                $(".reseller").html(field['reseller'][0]['full_name']);
                $(".modem-mac").html(field['modem'][0]["mac_address"]);
                $(".router-mac").html(router_mac_address);
                $(".plan").html(plan);
                $(".address").html(field['address']);
                $(".phone").html(field['phone']);
                $(".ip").html(ip_address);
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

<title>Support</title>
<div class="page-header">
    <a class="last" href="">Support</a>    
</div>
<table class="display table table-striped table-bordered">
    <tr>
        <td style="width:20%">ID</td>
        <td class="customer-id"></td>
    </tr>
    <tr>
        <td>Full Name</td>
        <td class="full-name"></td>
    </tr>
    <tr>
        <td>Reseller</td>
        <td class="reseller"></td>
    </tr>
    <tr>
        <td>Phone</th>
        <td class="phone"></td>
    </tr>
    <tr>
        <td>Modem MAC</td>
        <td class="modem-mac"></td>
    </tr>
    <tr>
        <td>Router MAC</td>
        <td class="router-mac"></td>
    </tr>
    <tr>
        <td>IP</td>
        <td class="ip"></td>
    </tr>
    <tr>
        <td>Plan</td>
        <td class="plan"></td>
    </tr>
    <tr>
        <td>Address</td>
        <td class="address"></td>
    </tr>
<tbody>
</tbody>
</table>

<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Type</th>
    <th>Note</th>
    <th>Due Date</th>
    <th>Completion</th>
    <th>Functions</th>
</thead>
<tbody>
	
</tbody>
</table>
<?php
include_once "../footer.php";
?>