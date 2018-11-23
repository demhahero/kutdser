<?php
include_once "../header.php";
?>
<?php
$edit_id=intval($_GET["reseller_request_id"]);

?>

<script>
    $(document).ready(function () {
      $('#datepicker').datepicker({
          format: 'mm/dd/yyyy',
          startDate: '+0d'
      });
              $.post("<?= $api_url ?>reseller_requests/edit_reseller_request_api.php"
              ,{
                    "edit_id":<?=$edit_id?>,
                    "post_action": "get_reseller_request_by_id"
                }, function (data) {
                data = $.parseJSON(data);
                if (data.error != true) {
                  $("input[name=\"modem_mac_address\"]").val(data.reseller_request.mac_address);
                  $("input[name=\"modem_serial_number\"]").val(data.reseller_request.serial_number);
                  $("input[name=\"modem_type\"]").val(data.reseller_request.type);
                  $("select[name=\"action\"]").val(data.reseller_request.action);
                  $("input[name=\"action_on_date\"]").val(data.reseller_request.action_on_date);
                  $("textarea[name=\"note\"]").val(data.reseller_request.note);


                  }
                  else{
                    alert("error loading data, please contact admin");
                  }
              });

      $( ".update-form" ).submit(function( event ) {
          event.preventDefault();
          var action_on_date = $("input[name=\"action_on_date\"]").val();
          var note = $("textarea[name=\"note\"]").val();
          var action = $("select[name=\"action\"]").val();
          var modem_mac_address = $("input[name=\"modem_mac_address\"]").val();
          var modem_serial_number = $("input[name=\"modem_serial_number\"]").val();
          var modem_type = $("input[name=\"modem_type\"]").val();

          $.post("<?= $api_url ?>reseller_requests/edit_reseller_request_api.php",
                  {
                    "post_action":"edit_reseller_request",
                    "edit_id":<?=$edit_id?>,
                    "action": action,
                    "action_on_date": action_on_date,
                    "modem_mac_address": modem_mac_address,
                    "modem_serial_number": modem_serial_number,
                    "modem_type": modem_type,
                    "note": note,
                  }
          , function (data, status) {
              data = $.parseJSON(data);
              if (data.edited == true) {
                  alert("value updated");

              } else
                  alert("Error: "+data.error);
          });
        });

    });
</script>
<title>Edit reseller_request</title>
<div class="page-header">
    <h4>Edit reseller_request</h4>
</div>

<form class="update-form" method="post">
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
