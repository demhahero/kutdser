<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {

        $(".update-form").submit(function (event) {
            event.preventDefault();

            var resellerportal_notification = $("textarea[name=\"resellerportal_notification\"]").val();

            $.post("<?= $api_url ?>information/edit_resellerportal_notification.php",
                    {
                        "action": "edit_resellerportal_notification",
                        "resellerportal_notification": resellerportal_notification
                    }
            , function (data, status) {
                data = $.parseJSON(data);
                if (data.edited == true) {
                    alert("value updated");

                } else
                    alert("Error: " + data.error);
            });
        });

        $.get("<?= $api_url ?>information/get_resellerportal_notification.php",
                {
                    action: "logout"
                }
        , function (data_response, status) {
            data_response = $.parseJSON(data_response);

            $(".resellerportal-notification").html(data_response["information"]["resellerportal_notification"]);
        });

    });
</script>
<title>Edit Reseller Portal</title>
<div class="page-header">
    <h4>Edit Reseller Portal</h4>
</div>

<form class="update-form" method="post">
    <div class="form-group">
        <label>Reseller Portal notification:</label>
        <textarea name="resellerportal_notification"  class="resellerportal-notification form-control"></textarea>
    </div>
    <input type="submit" class="btn btn-default" value="Update">
</form>

<?php
include_once "../footer.php";
?>
