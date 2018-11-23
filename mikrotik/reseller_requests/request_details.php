<?php

include_once "../header.php";

$request_id = intval($_GET["request_id"]);
?>

<script>
    $(document).ready(function () {

      function isNumber(n) {
       return !isNaN(parseFloat(n)) && isFinite(n);
      }

      $("#no_verdict").hide();
      $("#moving_approved").hide();
      $("#moving_approved").hide();


      function refresh_page_data(){
        $.post("<?= $api_url ?>reseller_requests/request_details_api.php"
          ,{
                "request_id":<?=$request_id?>,
                "post_action": "get_request_details"
            }, function (data) {

            data = $.parseJSON(data);
            if (data.error != true) {

              if (data.request_row !== null) {

                  $("#action").html(data.request_row.action);
                  $("#action_on_date").html(data.request_row.action_on_date);
                  $("#verdict_date").html(data.request_row.verdict_date);
                  $("#verdict").html(data.request_row.verdict);
                  $("#username").html(data.request_row.username);
                  $("#reseller_full_name").html(data.request_row.reseller_full_name);
                  $("#modem_mac_address").html(data.request_row.modem_mac_address);
                  $("#modem_type").html(data.request_row.modem_type);
                  $("#modem_serial_number").html(data.request_row.modem_serial_number);
                  $("#note").html(data.request_row.note);
                  if(!data.request_row.verdict || data.request_row.verdict.length<=0)
                  {
                    $("#no_verdict").show();
                  }else{
                    $("#no_verdict").hide();
                  }


                  $(".no_request_row").hide();
                  $(".request_row_tr").show();
                }
                else{
                  $(".no_request_row").show();
                  $(".request_row_tr").hide();
                }
              }
          });
      }

      refresh_page_data();
///////// form submit ajax call
$("#no_verdict").submit(function(e){
  e.preventDefault();
  var verdict=$("select[name=\"verdict\"]").val();

  $.post("<?= $api_url ?>reseller_requests/request_details_api.php"
    ,{
          "reseller_request_id":<?=$request_id?>,
          "post_action": "edit_request",
          "verdict":verdict,

      }, function (data) {

      data = $.parseJSON(data);
      if (data.edited == true) {
        alert("Success: data updated");
        refresh_page_data();
      }
      else{
        alert("Error: update failed");
      }
    });
});

///////// end form submit

    });
</script>
<title>Request Details</title>
<div class="page-header">
    <h4>Request Details</h4>
</div>

<br>


    <form id="no_verdict" class="confirm-form" >

        <div class="form-group">
          <div class="form-group">
            <label for="email">Verdict:</label>
            <select  name="verdict" class="form-control">
                <option  value="approve">approve</option>
                <option  value="disapprove">disapprove</option>
            </select>
          </div>
        </div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>

          <div id="moving_approved">
          </div>

    <div id="verdict_found">
        <table class="display table table-striped table-bordered">
            <tr>
                <td id="username_verdict_date">

                </td>
            </tr>
        </table>
    </div>

<div class="row" style="width:100% !important;">
    <div class="col-lg-12 col-md-12 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Request Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">

                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Reseller Name:</td>
                          <td id="reseller_full_name">

                          </td>
                          <td class=" bg-success" style="width: 20%;" rowspan="2">Admin:</td>
                          <td id="username" rowspan="2">

                          </td>
                        </tr>
                        <tr class="request_row_tr">

                          <td class=" bg-success" style="width: 20%;">Action:</td>
                          <td id="action">

                          </td>

                        </tr>
                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Action On Date:</td>
                          <td id="action_on_date" >

                          </td>
                          <td class=" bg-success" style="width: 20%;" rowspan="2">Verdict:</td>
                          <td id="verdict" rowspan="2">

                          </td>

                        </tr>
                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Modem Type:</td>
                          <td id="modem_type">

                          </td>

                        </tr>
                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Modem Mac Address:</td>
                          <td id="modem_mac_address">

                          </td>
                          <td class=" bg-success" style="width: 20%;" rowspan="3">Verdict Date:</td>
                          <td id="verdict_date" rowspan="3">

                          </td>
                        </tr>
                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Modem Serial Number:</td>
                          <td id="modem_serial_number">

                          </td>

                        </tr>
                        <tr class="request_row_tr">
                          <td class=" bg-success" style="width: 20%;">Note:</td>
                          <td id="note">

                          </td>

                        </tr>


                </table>
            </div>
        </div>
        </p>
    </div>
</div>



<?php
include_once "../footer.php";
?>
