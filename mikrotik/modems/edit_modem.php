<?php
include_once "../header.php";
?>
<?php
$edit_id=intval($_GET["modem_id"]);
// $modemTools = $dbTools->objModemTools(intval($_GET["modem_id"]));
//
//
// if (isset($_POST["mac_address"])) {
//
//     (isset($_POST["is_ours"])) ? $is_ours = "yes" : $is_ours = "no";
//
//     $modemTools->setMACAddress(mysql_real_escape_string($_POST["mac_address"]));
//     $modemTools->setType(mysql_real_escape_string($_POST["type"]));
//     $modemTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
//     $modemTools->setIsOurs($is_ours);
//     $modemTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
//     $modemTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
//
//     $result = $modemTools->doUpdate();
//
//     if ($result)
//         echo "<div class='alert alert-success'>done</div>";
// }
?>

<script>
    $(document).ready(function () {
      $.getJSON("<?= $api_url ?>customers/add_customer_api.php?action=get_all_customers", function (result) {
          $.each(result['customers'], function (i, item) {

              $("select[name=\"customer_id\"]").append($('<option>', {
                  value: item.customer_id,
                  text : item.full_name
              }));
          });
          $.getJSON("<?= $api_url ?>customers/add_customer_api.php?action=get_all_resellers", function (result) {
              $.each(result['resellers'], function (i, item) {

                  $("select[name=\"reseller_id\"]").append($('<option>', {
                      value: item.customer_id,
                      text : item.full_name
                  }));
              });
              $.post("<?= $api_url ?>modems/edit_modem_api.php"
              ,{
                    "edit_id":<?=$edit_id?>,
                    "action": "get_modem_by_id"
                }, function (data) {
                data = $.parseJSON(data);
                if (data.error != true) {
                  $("input[name=\"mac_address\"]").val(data.modem.mac_address);
                  $("input[name=\"serial_number\"]").val(data.modem.serial_number);
                  $("input[name=\"type\"]").val(data.modem.type);
                  $("select[name=\"reseller_id\"]").val(data.modem.reseller_id);
                  $("select[name=\"customer_id\"]").val(data.modem.customer_id);

                  if (data.modem.is_ours==="yes")
                    {
                      $( "#is_ours" ).prop( "checked", true );
                    }
                    else{
                      $( "#is_ours" ).prop( "checked", false );
                    }

                  }
                  else{
                    alert("error loading data, please contact admin");
                  }
              });
          });
      });
      $( ".update-form" ).submit(function( event ) {
          event.preventDefault();
          var is_ours="no";
          if ($('#is_ours').is(":checked"))
          {
            is_ours="yes";
          }
          var mac_address=$("input[name=\"mac_address\"]").val();
          var serial_number=$("input[name=\"serial_number\"]").val();
          var type=$("input[name=\"type\"]").val();
          var reseller_id=$("select[name=\"reseller_id\"]").val();
          var customer_id=$("select[name=\"customer_id\"]").val();

          $.post("<?= $api_url ?>modems/edit_modem_api.php",
                  {
                    "action":"edit_modem",
                    "edit_id":<?=$edit_id?>,
                    "mac_address": mac_address,
                    "serial_number": serial_number,
                    "type": type,
                    "reseller_id": reseller_id,
                    "customer_id": customer_id,
                    "is_ours" : is_ours
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
<title>Edit Modem</title>
<div class="page-header">
    <h4>Edit Modem</h4>
</div>

<form class="update-form" method="post">
    <div class="form-group">
        <label>MAC Address/Phone:</label>
        <input type="text" name="mac_address"  class="form-control" placeholder="mac address"/>
    </div>
    <div class="form-group">
        <label>Serial Number:</label>
        <input type="text" name="serial_number"  class="form-control" placeholder="serial number"/>
    </div>
    <div class="form-group">
        <label>Type:</label>
        <input type="text" name="type"  class="form-control" placeholder="type"/>
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>

        </select>
    </div>
    <div class="form-group">
        <label>Customer:</label>
        <select  name="customer_id" class="form-control">
            <option value="0">No Customer</option>

        </select>
    </div>
    <div class="form-group">
        <label>Is ours:</label>
        <input type="checkbox" id="is_ours" name="is_ours"  value="yes" class="form-control" placeholder=""/>
    </div>
    <input type="submit" class="btn btn-default" value="Update">
</form>

<?php
include_once "../footer.php";
?>
