<?php
include_once "../header.php";
?>



<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');
    

        $.getJSON("<?= $api_url ?>tik_monitoring_customer_api.php?customer_id=<?= $_GET["customer_id"] ?>", function (result) {

                    $.each(result['customers'], function (i, field) {
                        
                        $(".customer-id").html(field['customer_id']);
                        $(".full-name").html(field['full_name']);
                        $(".reseller").html(field['reseller'][0]['full_name']);
                        $(".modem-mac").html(field['modem'][0]["mac_address"]);
                        $(".router-mac").html(field['modem'][0]["router_mac_address"]);
                     
                        $(".address").html(field['address']);
                        $(".phone").html(field['phone']);
                        $(".ip").html(field['modem'][0]["ip_address"]);
                    });
                });

                $.getJSON("<?= $api_url ?>customer_log_api.php?customer_id=<?= $_GET["customer_id"] ?>", function (result) {

                            $.each(result['customer_logs'], function (i, field) {
                                table.row.add([
                                    field['customer_log_id'],
                                    field['note'],
                                    field['log_date'],
                                    field['admin'][0]['username']
                                ]).draw(false);
                            });
                        });

                        $(".submit").click(function () {
<?php
$dt = new DateTime();
?>
                            var customer_id = "<?= $_GET['customer_id'] ?>";
                            var log_date = "<?= $dt->format("Y-m-d H:i:s") ?>";
                            var note = $("textarea[name=\"note\"]").val();
                            $.post("<?= $api_url ?>customer_log_api.php", {customer_id: customer_id, log_date: log_date, note: note, type: "general", completion: "1", admin_id: '<?= $admin_id ?>'}, function (data, status) {
                                data = $.parseJSON(data);
                                if (data.inserted == true) {
                                    alert("Log inserted");
                                    location.reload();
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
    <th style="width:10%">ID</th>
    <th style="width:70%">Note</th>
    <th style="width:10%">Date</th>
    <th style="width:10%">Admin</th>
</thead>
<tbody>

</tbody>
</table>

<form class="register-form" method="post">
    <div class="form-group">
        <label>Note:</label>
        <textarea name="note" style="width:100%;" class="form-control"></textarea> 
    </div>
    <input type="submit" class="btn btn-default submit"  value="Send">
</form>

<?php
include_once "../footer.php";
?>