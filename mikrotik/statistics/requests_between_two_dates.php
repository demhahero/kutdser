<?php
include_once "../header.php";
?>

<?php
$type = $_GET["type"];
if (isset($_GET["date1"])) {
    $date1 = $_GET["date1"] . " 00:00:00";
    $date2 = $_GET["date2"] . " 00:00:00";

} else {
    $date1 = "2018-04-01 00:00:00";
    $date2 = "2018-06-28 00:00:00";
}
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');
        $.getJSON("<?= $api_url ?>statistics/requests_between_two_dates.php?date1=<?= $date1 ?>&date2=<?= $date2 ?>&type=<?= $type ?>", function (result) {
                    $.each(result['requests'], function (i, field) {
                        action_on_date = "";
                        if (field['action_on_date'] != null) {
                            action_on_date = field['action_on_date'].split(' ');
                            action_on_date = action_on_date[0];
                        }
                        table.row.add([
                            '<a href="<?=$site_url?>/requests/request_details.php?request_id=' + field['request_id'] + '" >' + field['request_id'] + '</a>',
                            field["order_id"],
                            field["full_name"],
                            field["reseller_name"],
                            field['action'],
                            field['product_title'],
                            action_on_date,
                            field['creation_date'],
                            field['verdict'],
                            field["username"]
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

<title>Statistics - Requests</title>
<div class="page-header">
    <a class="last" href="">Statistics - Requests</a>
</div>
<form class="register-form form-inline" method="get">
    <div class="form-group">
        <label for="email">Date 1:</label>
        <input name="date1" readonly="" value="<?= $_GET["date1"] ?>" class="form-control datepicker">
        <label for="email">Date 2:</label>
        <input name="date2" readonly="" value="<?= $_GET["date2"] ?>" class="form-control datepicker">
        <label for="email">Type:</label>
        <select  name="type" class="form-control">
            <option <?php if ($_GET['type'] == "requests_fees") echo "selected"; ?> value="requests_fees">Requests fees</option>
            <option <?php if ($_GET['type'] == "terminate") echo "selected"; ?> value="terminate">Terminate</option>
        </select>
    </div>
    <input type="submit" class="btn btn-default" value="Search">
</form>
<br/>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%;">ID</th>
    <th style="width: 10%;">Order</th>
    <th style="width: 15%;">Customer</th>
    <th style="width: 15%;">Reseller</th>
    <th style="width: 5%;">Action</th>
    <th style="width: 5%;">Product title</th>
    <th style="width: 10%;">Action on Date</th>
    <th style="width: 15%;">Date</th>
    <th style="width: 10%;">Verdict</th>
    <th style="width: 10%;">Admin</th>
</thead>
<tbody>
</tbody>
</table>



<?php
include_once "../footer.php";
?>
