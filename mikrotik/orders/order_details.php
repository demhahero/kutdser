<?php
include_once "../header.php";
?>

<?php
// $order = $dbTools->objOrderTools($_GET["order_id"], 2);
//
// if (isset($_POST["status"])) {
//     $order->setStatus($_POST["status"]);
//     $order->setCompletion($_POST["completion"]);
//
//     if ($_POST["actual_installation_date"] != "")
//         $order->setActualInstallationDate(new DateTime($_POST["actual_installation_date"]));
//
//     $order->setUpdateDate(new DateTime());
//     $order->setAdminID($admin_id);
//     $order->setActualInstallationTimeFrom($_POST["actual_installation_time_from"]);
//     $order->setActualInstallationTimeTo($_POST["actual_installation_time_to"]);
//     $order->setVLNumber($_POST["vl_number"]);
//     $result = $order->doUpdate();
//     if ($result)
//         echo "<div class='alert alert-success'>done</div>";
// }


?>
<script>
    $(document).ready(function () {
      var current_customer_id=-1;
      function customerLog(customer_id){
        $.getJSON("<?= $api_url ?>orders/customer_log_api.php?customer_id="+customer_id, function (result) {
          table.clear().draw();

                    $.each(result['customer_logs'], function (i, field) {
                        table.row.add([
                            field['customer_log_id'],
                            field['note'],
                            field['log_date'],
                            field['username']
                        ]).draw(false);
                    });
        });

      }
      $("#send_by_email").click(function(){
        var url=$(this).attr("data-href");
        $.getJSON(url
          , function (response, status) {
              response = $.parseJSON(response);
              if (!response.error  ) {
                alert(response.message);//email sent
              }
              else{
                alert(response.message);//email didn't sent
              }
            });
      })
      var usageUrl="";
      $.post("<?= $api_url ?>orders/order_details_api.php",
        {
          action:"get_order_by_id",
          order_id: <?=$_GET["order_id"]?>,

        }
        , function (response, status) {
            response = $.parseJSON(response);
            if (!response.error  ) {
              current_customer_id=response.order_details.customer_id;
              customerLog(response.order_details.customer_id);
              $("input[name=\"customer_id\"]").val(response.order_details.customer_id);

              document.title="Order "+response.order_details.order_id+"'s details"
              $('#displayed_order_id').html(response.order_details.order_id);
              $('#send_by_email').attr("data-href", "<?=$api_url?>print/send_invoice.php?order_id="+response.order_details.order_id);

              usageUrl="http://38.104.226.51/ahmed/netflow_graph2.php?ip="+response.order_details.ip_address;

              if (response.order_details.modem === "inventory") {
                  $('#modem_inventory_mac').html(response.order_details.mac_address);
                  $('#modem_inventory').show();
                  $('.own_modem').hide();
                } else if (response.order_details.modem == "own_modem") {
                  $('#serial_number').html(response.order_details.serial_number);
                  $('#modem_mac_address').html(response.order_details.modem_mac_address);
                  $('#modem_type').html(response.order_details.modem_type);
                  $('#modem_inventory').hide();
                  $('.own_modem').show();

              }

              if (response.order_details.cable_subscriber === "yes") {
                $('.yes_cable_subscriber').show();
                $('.not_cable_subscriber').hide();
              }
              else if(response.order_details.cable_subscriber === "no"){
                $('.yes_cable_subscriber').hide();
                $('.not_cable_subscriber').show();
              }

              $("#admin_user_name").html(response.order_details.username+' on '+response.order_details.update_date);


              $("select[name=\"status\"]").val(response.order_details.status);
              $("input[name=\"completion\"]").val(response.order_details.completion);
              $("input[name=\"vl_number\"]").val(response.order_details.vl_number);
              $("input[name=\"actual_installation_date\"]").val(response.order_details.actual_installation_date);
              $("input[name=\"actual_installation_time_from\"]").val(response.order_details.actual_installation_time_from);
              $("input[name=\"actual_installation_time_to\"]").val(response.order_details.actual_installation_time_to);
            }
          });
        $('#usage').click(function(){
          window.open(usageUrl, 'myWindow', 'width=1200,height=500');
        });
        table2 = $('#myTable2').DataTable({
            responsive: true
        });
        $('.dataTables_empty').html('<div class="loader"></div>');

        $.getJSON("<?= $api_url ?>orders/order_api.php?order_id=<?= $_GET["order_id"] ?>", function (field) {

                        $(".displyed-order-id").html(field['displayed_order_id']);
                        $(".customer-full-name").html(field['customer_name']);
                        $(".customer-address").html(field['address'] + field['city'] + " " +
                                field['address_line_1'] + " " + field['address_line_2'] + " " +
                                field['postal_code']);
                        $(".customer-email").html(field['email']);
                        $(".customer-phone").html(field['phone']);
                        $(".customer-note").html(field['note']);
                        $(".product-title").html(field['product_title']);
                        $(".plan").html(field['plan']);
                        $(".creation-date").html(field['creation_date']);
                        $(".status").html(field['status']);
                        $(".router").html(field['router']);
                        $(".modem").html(field['modem']);
                        $(".cancellation-date").html(field['cancellation_date']);
                        $(".cable-subscriber").html(field['cable_subscriber']);
                        $(".current-cable-provider").html(field['current_cable_provider']);
                        $(".additional-service").html(field['additional_service']);
                        $(".completion").html(field['completion']);
                        $(".reseller-full-name").html(field['reseller_name']);
                        $(".installation-date-1").html(field['installation_date_1']);
                        $(".installation-time-1").html(field['installation_time_1']);
                        $(".installation-date-2").html(field['installation_date_2']);
                        $(".installation-time-2").html(field['installation_time_2']);
                        $(".installation-date-3").html(field['installation_date_3']);
                        $(".installation-time-3").html(field['installation_time_3']);
                        $(".subscription-ref").html("SS_" + field["merchantref"]);
                        $(".subscription-card-ref").html("CARD_" + field["merchantref"]);
                        $(".subscription-payment-ref").html("P_" + field["merchantref"]);

        });


        $.getJSON("<?= $api_url ?>orders/customer_requests_api.php?order_id=<?= $_GET["order_id"] ?>", function (result) {

                    $.each(result['requests'], function (i, field) {
                        action_on_date="";
                        if(field['action_on_date'] != null){
                            action_on_date = field['action_on_date'].split(' ');
                            action_on_date = action_on_date[0];
                        }

                        table2.row.add([
                            '<a href="request_details.php?request_id='+field['request_id']+'" >'+field['request_id']+'</a>',
                            field["order_id"],
                            field["full_name"],
                            field['reseller_name'],
                            field['action'],
                            field['product_title'],
                            action_on_date,
                            field['creation_date'],
                            field['verdict'],
                            field["username"]
                        ]).draw(false);
                    });
        });


        $(".submit").click(function () {
            <?php
            $dt = new DateTime();
            ?>
            var customer_id = $("input[name=\"customer_id\"]").val();
            var log_date = "<?= $dt->format("Y-m-d H:i:s") ?>";
            var note = $("textarea[name=\"note\"]").val();
            $.post("<?= $api_url ?>orders/customer_log_api.php",
            {
              customer_id: customer_id,
              log_date: log_date,
              note: note,
              type: "general",
              completion: "1",
              admin_id: '<?= $admin_id ?>'
            }, function (data, status) {
                data = $.parseJSON(data);
                if (data.inserted == true) {
                    alert("Log inserted");
                    //location.reload();
                    customerLog(current_customer_id);
                } else
                    alert("Error, try again");
            });
            return false;
        });
    });
</script>
<title></title>
<div class="page-header">
    <a href="orders.php">Orders</a>
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href="">Order <span id="displayed_order_id"></span>'s details</a>

</div>

<a target="_blank" href="<?= $api_url ?>print/print_order.php?order_id=<?php echo $_GET["order_id"]; ?>" class="btn btn-success print-button">Print</a>
<button id="send_by_email" class="btn btn-danger print-button check-alert" data-href="#">Send by Email</button>
<a id="usage" class="btn btn-primary print-button" href="#">Usage</a>

<br>
<br>
<div>
    <table class="display table table-striped table-bordered">
        <tr>
            <td style="width:20%;">order ID</td>
            <td class="displyed-order-id"></td>
        </tr>
        <tr>
            <td>Completion</td>
            <td class="completion">

            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td class="customer-full-name">

            </td>
        </tr>
        <tr>
            <td>Customer's Address</td>
            <td class="customer-address">

            </td>
        </tr>
        <tr>
            <td>Customer's Email</td>
            <td class="customer-email">

            </td>
        </tr>
        <tr>
            <td>Customer's Phone</td>
            <td class="customer-phone">

            </td>
        </tr>
        <tr>
            <td>Customer's Note</td>
            <td class="customer-note">

            </td>
        </tr>
        <tr>
            <td>Reseller</td>
            <td class="reseller-full-name">

            </td>
        </tr>
        <tr>
            <td>Creation Date</td>
            <td class="creation-date"></td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="status"></td>
        </tr>
        <tr>
            <td>Product Title</td>
            <td class="product-title">

            </td>
        </tr>
        <tr>
            <td >Plan</td>
            <td class="plan">

            </td>
        </tr>
        <tr>
            <td>Modem</td>
            <td class="modem">

            </td>
        </tr>

            <tr id="modem_inventory">
                <td>Modem MAC</td>
                <td id="modem_inventory_mac">

                </td>
            </tr>

            <tr class="own_modem">
                <td>modem serial number</td>
                <td id="serial_number">

                </td>
            </tr>
            <tr class="own_modem">
                <td>modem mac address</td>
                <td id="mac_address">

                </td>
            </tr>
            <tr class="own_modem">
                <td>modem modem type</td>
                <td id="modem_type">

                </td>
            </tr>

        <tr>
            <td>Router</td>
            <td class="router">

            </td>
        </tr>

            <tr class="yes_cable_subscriber">
                <td>Cable subscriber</td>
                <td class="cable-subscriber">

                </td>
            </tr>
            <tr class="yes_cable_subscriber">
                <td>Current cable provider</td>
                <td class="current-cable-provider">

                </td>
            </tr>
            <tr class="yes_cable_subscriber">
                <td>Cancellation date</td>
                <td class="cancellation-date">

                </td>
            </tr>

            <tr class="not_cable_subscriber">
                <td>installation date 1</td>
                <td class="installation-date-1">

                </td>
            </tr>
            <tr class="not_cable_subscriber">
                <td>installation time 1</td>
                <td class="installation-time-1">

                </td>
            </tr>
            <tr class="not_cable_subscriber">
                <td>installation date 2</td>
                <td class="installation-date-2">

                </td>
            </tr>
            <tr class="not_cable_subscriber">
                <td>installation time 2</td>
                <td class="installation-time-2">

                </td>
            </tr>
            <tr class="not_cable_subscriber">
                <td>installation date 3</td>
                <td class="installation-date-3">

                </td>
            </tr>
            <tr class="not_cable_subscriber">
                <td>installation time 3</td>
                <td class="installation-time-3">

                </td>
            </tr>

        <tr>
            <td>additional service</td>
            <td class="additional-service">

            </td>
        </tr>
        <tr>
            <td>Subscription Ref</td>
            <td  class="subscription-ref" style="font-weight: bold;">

            </td>
        </tr>
        <tr>
            <td>Secure Card Ref</td>
            <td  class="subscription-card-ref" style="font-weight: bold;">

            </td>
        </tr>
        <tr>
            <td>Payment Ref</td>
            <td  class="subscription-payment-ref" style="font-weight: bold;">

            </td>
        </tr>
    </table>
</div>



<form class="register-form" method="post">

    <i>Last Update by <span id="admin_user_name"></span></i><br/><br/>
    <div class="form-group">

        <label for="email">Status:</label>
        <select  name="status" class="form-control">
            <option  value="sent">Sent</option>
            <option  value="processing">processing</option>
            <option  value="complete">Complete</option>
        </select>
    </div>
    <div class="form-group">
        <label for="email">Completion:</label>
        <input type="text" name="completion" value="" class="form-control" placeholder="Completion"/>
    </div>
    <div class="form-group">
        <label for="email">VL Number:</label>
        <input type="text" name="vl_number" value="" class="form-control" placeholder="VL Number"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation date:</label>
        <input type="text" readonly="" name="actual_installation_date" value="" class="form-control datepicker" placeholder="Actual installation date"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation time from:</label>
        <input type="text" name="actual_installation_time_from" value="" class="form-control" placeholder="Actual installation time from"/>
    </div>
    <div class="form-group">
        <label for="email">Actual installation time to:</label>
        <input type="text" name="actual_installation_time_to" value="" class="form-control" placeholder="Actual installation time to"/>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>

<br/>
<br/>
<br/>
<div class="panel panel-danger">
    <div class="panel-heading">Logs/Notes</div>
    <div class="panel-body">
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
          <input type="hidden" name="customer_id"  value="0"/>
            <div class="form-group">
                <label>Note:</label>
                <textarea name="note" style="width:100%;" class="form-control"></textarea>
            </div>
            <input type="submit" class="btn btn-default submit"  value="Send">
        </form>
    </div>
</div>


<div class="panel panel-success">
    <div class="panel-heading">Requests</div>
    <div class="panel-body">
        <table id="myTable2" class="display table table-striped table-bordered">
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
    </div>
</div>
<?php
include_once "../footer.php";
?>
