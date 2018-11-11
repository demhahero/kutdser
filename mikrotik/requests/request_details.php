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
        $.post("<?= $api_url ?>requests/request_details_api.php"
          ,{
                "request_id":<?=$request_id?>,
                "post_action": "get_request_details"
            }, function (data) {

            data = $.parseJSON(data);
            if (data.error != true) {

              if (data.request_row !== null) {

                  var action = data.request_row.action;
                  if (data.request_row.action === "change_speed" && isNumber(data.request_row.modem_id) && parseInt(data.request_row.modem_id) > 0) {
                      action = "swap modem and change speed";
                  }
                  $("#action").html(action);
                  $("#action_on_date").html(data.request_row.action_on_date);
                  $("#verdict_date").html(data.request_row.verdict_date);
                  $("#verdict").html(data.request_row.verdict);
                  $("#username").html(data.request_row.username);
                  $("#reseller_full_name").html(data.request_row.reseller_full_name);
                  $("#modem_mac_address").html(data.request_row.modem_mac_address);
                  $("#note").html(data.request_row.note);
                  $("#product_title").html(data.request_row.product_title);
                  $("#product_price").html(data.request_row.product_price);
                  $("#product_subscription_type").html(data.request_row.product_subscription_type);
                  $("#product_category").html(data.request_row.product_category);
                  $("#city").html(data.request_row.city);
                  $("#address_line_1").html(data.request_row.address_line_1);
                  $("#address_line_2").html(data.request_row.address_line_2);
                  $("#postal_code").html(data.request_row.postal_code);
                  $("#full_name").html(data.request_row.full_name);
                  $("#email").html(data.request_row.email);
                  $("#phone").html(data.request_row.phone);



                  $(".no_request_row").hide();
                  $(".request_row_tr").show();
                }
                else{
                  $(".no_request_row").show();
                  $(".request_row_tr").hide();
                }
    ///////////// last request
    if (data.last_request_row !== null) {

        var last_action = data.last_request_row.action;
        if (data.last_request_row.action === "change_speed" && isNumber(data.last_request_row.modem_id) && parseInt(data.last_request_row.modem_id) > 0) {
            last_action = "swap modem and change speed";
        }
        $("#last_action").html(last_action);
        $("#last_action_on_date").html(data.last_request_row.action_on_date);
        $("#last_verdict_date").html(data.last_request_row.verdict_date);
        $("#last_verdict").html(data.last_request_row.verdict);
        $("#last_username").html(data.last_request_row.username);
        $("#last_reseller_full_name").html(data.last_request_row.reseller_full_name);
        $("#last_modem_mac_address").html(data.last_request_row.modem_mac_address);
        $("#last_note").html(data.last_request_row.note);
        $("#last_product_title").html(data.last_request_row.product_title);
        $("#last_product_price").html(data.last_request_row.product_price);
        $("#last_product_subscription_type").html(data.last_request_row.product_subscription_type);
        $("#last_product_category").html(data.last_request_row.product_category);
        $("#last_city").html(data.last_request_row.city);
        $("#last_address_line_1").html(data.last_request_row.address_line_1);
        $("#last_address_line_2").html(data.last_request_row.address_line_2);
        $("#last_postal_code").html(data.last_request_row.postal_code);
        $("#last_full_name").html(data.last_request_row.full_name);
        $("#last_email").html(data.last_request_row.email);
        $("#last_phone").html(data.last_request_row.phone);



        $(".no_last_request_data").hide();
        $(".last_request_data").show();
      }
      else{
        $(".no_last_request_data").show();
        $(".last_request_data").hide();
      }

                $("#order_full_name").html(data.request_order_row.full_name);
                $("#order_displayed_order_id").html(data.request_order_row.displayed_order_id);
                $("#order_start_active_date").html(data.request_order_row.start_active_date);
                $("#order_product_subscription_type").html(data.request_order_row.product_subscription_type);
                $("#order_product_category").html(data.request_order_row.product_category);
                $("#order_product_title").html(data.request_order_row.product_title);
                $("#order_creation_date").html(data.request_order_row.creation_date);
                $("#order_plan").html(data.request_order_row.plan);
                $("#order_modem").html(data.request_order_row.modem);
                $("#order_status").html(data.request_order_row.status);
                $("#order_router").html(data.request_order_row.router);
                $("#order_cable_subscriber").html(data.request_order_row.cable_subscriber);

                $("#order_current_cable_provider").html(data.request_order_row.current_cable_provider);
                $("#order_cancellation_date").html(data.request_order_row.cancellation_date);
                $("#order_installation_date_1").html(data.request_order_row.installation_date_1);
                $("#order_installation_time_1").html(data.request_order_row.installation_time_1);
                $("#order_installation_date_2").html(data.request_order_row.installation_date_2);
                $("#order_installation_time_2").html(data.request_order_row.installation_time_2);
                $("#order_installation_date_3").html(data.request_order_row.installation_date_3);
                $("#order_installation_time_3").html(data.request_order_row.installation_time_3);
                $("#order_additional_service").html(data.request_order_row.additional_service);
                $("#order_actual_installation_date").html(data.request_order_row.actual_installation_date);
                $("#order_actual_installation_time_from").html(data.request_order_row.actual_installation_time_from);
                $("#order_actual_installation_time_to").html(data.request_order_row.actual_installation_time_to);



                if (data.request_order_row.cable_subscriber === "yes")
                {
                  $(".order_cable_subscriber_tr").show();
                  $(".order_no_cable_subscriber_tr").hide();


                } else if (data.request_order_row.cable_subscriber === "no")
                {
                  $(".order_cable_subscriber_tr").hide();
                  $(".order_no_cable_subscriber_tr").show();
                }



    // fill form fields
              $("input[name=\"request_id\"]").val("<?= $request_id ?>");
              $("input[name=\"action\"]").val(data.request_row.action);
              $("input[name=\"order_id\"]").val(data.request_row.order_id);

              $("input[name=\"modem_id\"]").val(data.request_row.modem_id);
              $("input[name=\"full_name\"]").val(data.request_row.full_name);
              $("input[name=\"phone\"]").val(data.request_row.phone);
              $("input[name=\"email\"]").val(data.request_row.email);
              $("input[name=\"product_title\"]").val(data.request_row.product_title);
              $("input[name=\"product_price\"]").val(data.request_row.product_price);
              $("input[name=\"product_category\"]").val(data.request_row.product_category);
              $("input[name=\"product_subscription_type\"]").val(data.request_row.product_subscription_type);
              $("input[name=\"customer_id\"]").val(data.request_order_row.customer_id);

              if(data.request_row.verdict.length<=0)
              {
                $("#no_verdict").show();

                if(data.request_row.action==="terminate" && Date.parse(data.request_order_row.start_active_date) >= Date.parse(data.request_row.action_on_date) )// 1 = greater, -1 = less than, 0 = equal
                {
                  $("input[name=\"fees_charged\"]").val("0");
                }
                else if(data.request_row.action==="terminate" || data.request_row.action==="moving")
                {
                  $("input[name=\"fees_charged\"]").val("82");
                }else if(data.request_row.action==="change_speed")
                {
                  $("input[name=\"fees_charged\"]").val("7");
                }
                else{
                  $("input[name=\"fees_charged\"]").val("0");
                }
                $("#moving_approved").hide();
                $("#moving_approved").hide();
              }else
              {
                var username_verdict_date='"'+data.request_row.username+'"'+  data.request_row.verdict+' on  '+data.request_row.verdict_date;
                $("#username_verdict_date").html(username_verdict_date);
                $("#no_verdict").hide();
                $("#verdict_found").show();
                if(data.request_row.action === "moving" && data.request_row.verdict === "approve")
                {
                  $("#moving_approved").html('<a target="_blank" href="<?= $site_url ?>/requests/print_request.php?order_id='+ data.request_row.order_id+' class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print Invoice </a>');
                  $("#moving_approved").show();
                }
                else{
                  $("#moving_approved").html("");
                  $("#moving_approved").hide();
                }
              }
            }
          });
      }

      refresh_page_data();
///////// form submit ajax call
$("#no_verdict").submit(function(e){
  e.preventDefault();
  var request_id=$("input[name=\"request_id\"]").val();
  var action=$("input[name=\"action\"]").val();
  var order_id=$("input[name=\"order_id\"]").val();

  var modem_id=$("input[name=\"modem_id\"]").val();
  var full_name=$("input[name=\"full_name\"]").val();
  var phone=$("input[name=\"phone\"]").val();
  var email=$("input[name=\"email\"]").val();
  var product_title=$("input[name=\"product_title\"]").val();
  var product_price=$("input[name=\"product_price\"]").val();
  var product_category=$("input[name=\"product_category\"]").val();
  var product_subscription_type=$("input[name=\"product_subscription_type\"]").val();
  var customer_id=$("input[name=\"customer_id\"]").val();
  var verdict=$("select[name=\"verdict\"]").val();
  var fees_charged=$("input[name=\"fees_charged\"]").val();

  $.post("<?= $api_url ?>requests/request_details_api.php"
    ,{
          "request_id":<?=$request_id?>,
          "post_action": "edit_request",
          "action":action,
          "order_id":order_id,
          "modem_id":modem_id,
          "full_name":full_name,
          "phone":phone,
          "email":email,
          "product_title":product_title,
          "product_price":product_price,
          "product_category":product_category,
          "product_subscription_type":product_subscription_type,
          "customer_id":customer_id,
          "verdict":verdict,
          "fees_charged":fees_charged

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


    <form id="no_verdict" class="register-form" >

        <div class="form-group">
          <input type="hidden" name="product_price" />
          <input type="hidden" name="product_title" />
          <input type="hidden" name="product_category" />
          <input type="hidden" name="product_subscription_type" />
          <input type="hidden" name="request_id" />
          <input type="hidden" name="action" />
          <input type="hidden" name="modem_id" />
          <input type="hidden" name="full_name" />
          <input type="hidden" name="phone" />
          <input type="hidden" name="email" />
          <input type="hidden" name="customer_id" />
          <input type="hidden" name="order_id" />
          <div class="form-group">
            <label for="email">Verdict:</label>
            <select  name="verdict" class="form-control">
                <option  value="approve">approve</option>
                <option  value="disapprove">disapprove</option>
            </select>
          </div>
          <div class="form-group">
            <label for="email">Fees charged:</label>
            <input type="number" name="fees_charged" style="width:100px;" value="0"/> $
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
                            <td class=" bg-success">Action:</td>
                            <td id="action">

                            </td>

                            <td class=" bg-success">Action On Date:</td>
                            <td id="action_on_date">

                            </td>
                            <td class=" bg-success">Verdict Date:</td>
                            <td id="verdict_date">

                            </td>


                            <td class=" bg-success">Verdict:</td>
                            <td id="verdict">

                            </td>
                        </tr>
                        <tr class="request_row_tr">
                            <td class=" bg-success">Admin:</td>
                            <td id="username">

                            </td>
                            <td class=" bg-success">Reseller Name:</td>
                            <td id="reseller_full_name">

                            </td>
                            <td class=" bg-success">Modem Mac Address:</td>
                            <td id="modem_mac_address">

                            </td>
                            <td class=" bg-success">Note:</td>
                            <td id="note">

                            </td>



                        </tr>
                        <tr class="request_row_tr">
                            <td class=" bg-success">Product Name:</td>
                            <td id="product_title">

                            </td>
                            <td class=" bg-success">Product price:</td>
                            <td id="product_price">

                            </td>

                            <td class=" bg-success">Product Type:</td>
                            <td id="product_subscription_type">

                            </td>

                            <td class=" bg-success">Product Category:</td>
                            <td id="product_category">

                            </td>



                        </tr>
                        <tr class="request_row_tr">
                            <td class=" bg-success">City:</td>
                            <td id="city">

                            </td>
                            <td class=" bg-success">Address Line 1:</td>
                            <td id="address_line_1">

                            </td>

                            <td class=" bg-success">Address Line 2:</td>
                            <td id="address_line_2">

                            </td>
                            <td class=" bg-success">Postal Code:</td>
                            <td id="postal_code">

                            </td>
                        </tr>

                        <tr class="request_row_tr">
                            <td class=" bg-success">Full Name:</td>
                            <td id="full_name">

                            </td>
                            <td class=" bg-success">Email:</td>
                            <td id="email">

                            </td>

                            <td class=" bg-success">Phone:</td>
                            <td id="phone">

                            </td>
                            <td class=" bg-success"></td>
                            <td>

                            </td>
                        </tr>

                        <tr class="no_request_row">
                            <td>There are no previous Requests</td>

                        </tr>

                </table>
            </div>
        </div>
        </p>
    </div>
</div>
<div class="row" style="width:100% !important;">
    <div class="col-lg-6 col-md-6 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Order Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">
                    <tr>
                        <td>Customer Name</td>
                        <td id="order_full_name">

                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%;">order ID</td>
                        <td id="order_displayed_order_id">
                        </td>
                    </tr>
                    <tr>
                        <td>Start Active Date</td>
                        <td id="order_start_active_date">

                        </td>
                    </tr>
                    <tr>
                        <td>Product Type</td>
                        <td id="order_product_subscription_type">

                        </td>
                    </tr>
                    <tr>
                        <td>Product Category</td>
                        <td id="order_product_category">

                        </td>
                    </tr>
                    <tr>
                        <td>Product Name</td>
                        <td id="order_product_title">

                        </td>

                    <tr>
                        <td>Creation Date</td>
                        <td id="order_creation_date">
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td id="order_status">
                        </td>
                    </tr>

                    <tr>
                        <td>Plan</td>
                        <td id="order_plan">

                        </td>
                    </tr>
                    <tr>
                        <td>Modem</td>
                        <td id="order_modem">

                        </td>
                    </tr>

                    <tr>
                        <td>Router</td>
                        <td id="order_router">

                        </td>
                    </tr>

                        <tr class="order_cable_subscriber_tr">
                            <td>Cable subscriber</td>
                            <td id="order_cable_subscriber">

                            </td>
                        </tr>
                        <tr class="order_cable_subscriber_tr">
                            <td>Current cable provider</td>
                            <td id="order_current_cable_provider">

                            </td>
                        </tr>
                        <tr class="order_cable_subscriber_tr">
                            <td>Cancellation date</td>
                            <td id="order_cancellation_date">

                            </td>
                        </tr>

                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation date 1</td>
                            <td id="order_installation_date_1">

                            </td>
                        </tr>
                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation time 1</td>
                            <td id="order_installation_time_1">

                            </td>
                        </tr>
                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation date 2</td>
                            <td id="order_installation_date_2">

                            </td>
                        </tr>
                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation time 2</td>
                            <td id="order_installation_time_2">

                            </td>
                        </tr>
                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation date 3</td>
                            <td id="order_installation_date_3">

                            </td>
                        </tr>
                        <tr class="order_no_cable_subscriber_tr">
                            <td>installation time 3</td>
                            <td id="order_installation_time_3">

                            </td>
                        </tr>

                    <tr>
                        <td>additional service</td>
                        <td id="order_additional_service">

                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation date:</td>
                        <td id="order_actual_installation_date">

                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation time from:</td>
                        <td id="order_actual_installation_time_from">

                        </td>
                    </tr>
                    <tr>
                        <td>Actual installation time to:</td>
                        <td id="order_actual_installation_time_to">

                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </p>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12" >
        <p class="rounded form-row form-row-wide">
        <div class="panel panel-success">
            <div class="panel-heading">Previous Request Info</div>
            <div class="panel-body">
                <table class="display table table-striped table-bordered">

                        <tr class="last_request_data">
                            <td class=" bg-success">Action:</td>
                            <td id="last_action">

                            </td>

                            <td class=" bg-success">Action On Date:</td>
                            <td id="last_action_on_date">

                            </td>
                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Verdict Date:</td>
                            <td id="last_verdict_date">

                            </td>


                            <td class=" bg-success">Verdict:</td>
                            <td id="last_verdict">

                            </td>
                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Admin:</td>
                            <td id="last_username">

                            </td>
                            <td class=" bg-success">Reseller Name:</td>
                            <td id="last_full_name">

                            </td>
                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Modem Mac Address:</td>
                            <td id="last_modem_mac_address">

                            </td>
                            <td class=" bg-success">Note:</td>
                            <td id="last_note">

                            </td>



                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Product Name:</td>
                            <td id="last_product_title">

                            </td>
                            <td class=" bg-success">Product price:</td>
                            <td id="last_product_price">

                            </td>
                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Product Type:</td>
                            <td id="last_product_subscription_type">

                            </td>

                            <td class=" bg-success">Product Category:</td>
                            <td id="last_product_category">

                            </td>



                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">City:</td>
                            <td id="last_city">

                            </td>
                            <td class=" bg-success">Address Line 1:</td>
                            <td id="last_address_line_1">

                            </td>
                        </tr>
                        <tr class="last_request_data">
                            <td class=" bg-success">Address Line 2:</td>
                            <td id="last_address_line_2">

                            </td>
                            <td class=" bg-success">Postal Code:</td>
                            <td id="last_postal_code">

                            </td>
                        </tr>

                        <tr class="no_last_request_data">
                            <td>There are no previous Requests</td>

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
