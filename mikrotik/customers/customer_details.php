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

<title>Customer Details</title>
<div class="page-header">
    <a class="last" href="">Customer Details</a>    
</div>

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