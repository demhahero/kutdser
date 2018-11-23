<?php
include_once "../header.php";
?>


<title>Make a request</title>

<script>
    $(document).ready(function () {
        $('#datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '+0d'
        });

        $(".submit").click(function () {

            var action_on_date = $("input[name=\"action_on_date\"]").val();
            var note = $("textarea[name=\"note\"]").val();
            var reseller_id = "<?= $reseller_id ?>";
            var action = $("select[name=\"action\"]").val();
            var modem_mac_address = $("input[name=\"modem_mac_address\"]").val();
            var modem_serial_number = $("input[name=\"modem_serial_number\"]").val();
            var modem_type = $("input[name=\"modem_type\"]").val();

            $.post("<?= $api_url ?>reseller_requests/add_request_api.php",
                    {

                        action: action,
                        action_on_date: action_on_date,
                        modem_mac_address: modem_mac_address,
                        modem_serial_number: modem_serial_number,
                        modem_type: modem_type,
                        note: note,
                        reseller_id: reseller_id},
                     function (data, status) {
                      data = $.parseJSON(data);
                      if (data.inserted == true) {
                          alert("Request sent");
                          location.href = "reseller_requests.php";
                      } else {
                          if (data.error !== "null")
                              alert(data.error);
                          else
                              alert("Error, try again");
                      }

                  });
            return false;
        });


    });
</script>
<form class="add-form" method="post">
    <div class="form-group">
        <label>Make a request:</label>

    </div>

    <div class="form-group">
        <label>Action:</label>
        <select name="action" class="form-control">
            <option data-value="add_modem" value="add_modem">Add Modem</option>
        </select>
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" id="datepicker" class="form-control" />
    </div>
    <div class="form-group">
        <label>Modem Type:</label>
        <input type="text" name="modem_type" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Modem Mac Address:</label>
        <input type="text" name="modem_mac_address" class="form-control"/>

    </div>
    <div class="form-group">
        <label>Modem Serial Number:</label>
        <input type="text" name="modem_serial_number" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea name="note" class="form-control"></textarea>
    </div>
    <input type="submit" class="btn btn-primary submit"  value="Send">
</form>

<?php
include_once "../footer.php";
?>
