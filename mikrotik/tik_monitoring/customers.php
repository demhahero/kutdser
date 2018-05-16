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

        $.getJSON("<?= $api_url ?>tik_monitoring_customers_api.php", function (result) {

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
                
                table.row.add([
                    "<a target='_blank' href='http://38.104.226.51/ahmed/netflow_graph2.php?ip="+ip_address+"'>"+field['customer_id']+"</a>",
                    "<a href='customer_details.php?customer_id="+field['customer_id']+"'>"+field['full_name']+"</a>",
                    field['reseller'][0]['full_name'],
                    field['phone'],
                    field['modem'][0]["mac_address"],
                    router_mac_address,
                    ip_address,
                    plan,
                    field["address"]
                ]).draw(false);
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
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%">ID</th>
    <th style="width: 17%">Full Name</th>
    <th style="width: 17%">Reseller</th>
    <th style="width: 13%">Phone</th>
    <th style="width: 13%">Modem MAC</th>
    <th style="width: 13%">Router MAC</th>
    <th style="width: 12%">IP</th>
    <th style="width: 10%">Plan</th>
    <th>Address</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>