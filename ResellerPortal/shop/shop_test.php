<?php
include_once "../header.php";
?>
<!-- Include SmartWizard CSS -->
<link href="<?= $site_url ?>/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<!-- Optional SmartWizard theme -->
<link href="<?= $site_url ?>/css/smart_wizard_theme_circles.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />

<!-- Include SmartWizard JavaScript source -->
<script type="text/javascript" src="<?= $site_url ?>/js/jquery.smartWizard.min.js"></script>
<script type="text/javascript" src="<?= $site_url ?>/js/shop_shop_test.js"></script>

<script>
    $(document).ready(function () {
      $("#result").hide();
      $("div.processing-content").hide();
      $("div.succeeded-content").hide();
      $("div.failed-content").hide();
          function orderSubmittedSuccessfully(order_id) {
              $("#result").show();
              $(".shop_form").hide();
              var myarr = order_id.split("_");
              $('.print-button').attr("href", "<?=$api_url?>print/print_order.php?order_id=" + myarr[0]);
              $("h3.order-id").html("Order id: " + myarr[1]);
              $("div.processing-content").hide();
              $("div.succeeded-content").show();
              $("div.failed-content").hide();
          }

          function orderSubmittedfailed(reason) {
            $("#result").show();
            $("div.processing-content").hide();
            $("div.failed-content").show();
            $("span.failed-reason").html("Error: " + reason);
          }
              $.post("<?= $api_url ?>external/get_shopping_products.php"
              ,{
                    "action": "get_shopping_products"
                }, function (data) {
                data = $.parseJSON(data);
                if (data.error != true) {
                  ///////////////////////////////// fill all internet related data
                  $.each(data.internet.products, function (i, product) {

                    var title=product.title+" ("+product.price+"$)";
                        $("#internet_products").append($('<option>', {
                            text : title,
                            real_price:product.price,
                            data_title:product.title,
                            price:product.price,
                            value:product.product_id,
                            type:product.subscription_type
                        }));
                    });
                    $.each(data.internet.extra_needed_fields.plans, function (i, plan) {
                      var plan_html='<label class="radio-inline">';
                      var plan_html1='<input type="radio" checked  class="input-text plan plan-'+plan.value+' custom-options custom_field" data-price="" name="options[plan]" value="'+plan.value+'" />'+plan.name;
                      var plan_html2='</label><br/>';
                      $("#plans").append(plan_html+plan_html1+plan_html2);
                    });

                    $.each(data.internet.extra_needed_fields.inventory_modems, function (i, inventory_modem) {
                          $("#inventory_modems_list").append($('<option>', {
                              text : inventory_modem.name,
                              value:inventory_modem.value,
                          }));
                      });


                    $.each(data.internet.related_services.modems, function (i, modem) {
                      var modem_html='<label class="radio-inline">';
                      var modem_html1='<input type="radio" checked class="input-text modem custom-options custom_field" data-price="'+modem.price+'" name="options[modem]" value="'+modem.value+'" />'+modem.name+'';
                      var modem_html2='</label><br/>';
                      $("#modems").append(modem_html+modem_html1+modem_html2);
                    });

                    $.each(data.internet.related_services.routers, function (i, router) {
                      var rent_router=(router.value==="rent")?"rent-router":"";
                      var title=router.name+"($"+router.price+")";
                      var router_html='<label class="radio-inline">';
                      var router_html1='<input type="radio" class="input-text custom-options custom_field '+rent_router+'" data-price="'+router.price+'" name="options[router]" value="'+router.value+'" />'+title+'';
                      var router_html2='</label><br/>';
                      $("#routers").append(router_html+router_html1+router_html2);
                    });
                    $.each(data.internet.extra_needed_fields.providers, function (i, provider) {
                          $("#current_cable_provider").append($('<option>', {
                              text : provider.name,
                              value:provider.value,
                          }));
                      });


                      var additional_service_html='<label class="checkbox-inline">';
                      var additional_service_html1='<input type="checkbox"  class="input-text custom-options custom_field" data-price="'+data.internet.related_services.additional_service.price+'" name="options[additional_service]" value="'+data.internet.related_services.additional_service.value[0]+'" />'+data.internet.related_services.additional_service.name+'';
                      var additional_service_html2='</label><br/>';
                      $("#additional_service").append(additional_service_html+additional_service_html1+additional_service_html2);

                      var static_ip_html='<label class="checkbox-inline">';
                      var static_ip_html1='<input type="checkbox"  class="input-text custom-options custom_field" data-price="'+data.internet.related_services.static_ip.price+'" name="options[static_ip]" value="'+data.internet.related_services.static_ip.value[0]+'" />'+data.internet.related_services.static_ip.name+'';
                      var static_ip_html2='</label><br/>';
                      $("#static_ip").append(static_ip_html+static_ip_html1+static_ip_html2);
                      ////////////////////end internet related data

                      //////////////////// fill all phone related data
                      $.each(data.phone.products, function (i, product) {

                        var title=product.title+" ("+product.price+"$)";
                            $("#phone_products").append($('<option>', {
                                text : title,
                                real_price:product.price,
                                data_title:product.title,
                                price:product.price,
                                value:product.product_id,
                                type:product.subscription_type
                            }));
                        });

                        $.each(data.phone.related_services.adapter, function (i, adapter) {

                          var title=adapter.name+"($"+adapter.price+")";
                          var adapter_html='<label class="radio-inline">';
                          var adapter_html1='<input type="radio" checked class="input-text plan custom-options custom_field" data-price="'+adapter.price+'" name="options[adapter]" value="'+adapter.value+'" />'+title+'';
                          var adapter_html2='</label><br/>';
                          $("#adapters").append(adapter_html+adapter_html1+adapter_html2);
                        });

                        $.each(data.phone.related_services.you_have_phone_number, function (i, you_have_phone_number) {
                          var new_number=(you_have_phone_number.value==="no")?"new-number":"";
                          var title=you_have_phone_number.name+"($"+you_have_phone_number.price+")";
                          var you_have_phone_number_html='<label class="radio-inline">';
                          var you_have_phone_number_html1='<input type="radio" checked class="phone-subscriber '+new_number+' input-text plan plan-monthly custom-options custom_field" data-price="'+you_have_phone_number.price+'" name="options[you_have_phone_number]" value="'+you_have_phone_number.value+'" />'+title+'';
                          var you_have_phone_number_html2='</label><br/>';
                          $("#you_have_phone_number").append(you_have_phone_number_html+you_have_phone_number_html1+you_have_phone_number_html2);
                        });
                        $.each(data.phone.extra_needed_fields.phone_provinces, function (i, phone_province) {
                              $("#provinces").append($('<option>', {
                                  text : phone_province.name,
                                  value:phone_province.value,
                              }));
                          });


                  }

              });

        $(".shop_form").submit(function(e) {

          $("div.processing-content").show();
          var form = $(this);
          var url = "<?= $api_url ?>external/checkout_processes_api.php";

          $.ajax({
                 type: "POST",
                 url: url,
                 data: form.serialize(), // serializes the form's elements.
                 success: function(data)
                 {
                   data = $.parseJSON(data);
                   if(data.error ===false)
                   {
                     orderSubmittedSuccessfully(data.message);
                   }
                   else{
                     orderSubmittedfailed(data.message);
                   }
                 }
               });

          e.preventDefault(); // avoid to execute the actual submit of the form.
      });

    });
</script>

<title>Shop</title>


<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
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
    #step-1{
        padding: 20px;
    }
    #step-2{
        padding: 20px;
    }
    #step-3{
        padding: 20px;
    }
    #step-4{
        padding: 20px;
    }
</style>

<center id="result">
    <div class="processing-content">
        <h2 style="color:red;">Important: Do not leave or refresh this page until checkout process is done.</h1>
            <h3>Please wait while processing...</h2>
        <div class="loader"></div>
        <h5 class="process-caption" style="color:#00cc00;">Register</h4>
    </div>

    <div class="succeeded-content">
        <div class="alert alert-success order-result">
            <strong>Congratulation!</strong> Order sent successfully!
        </div>
        <h3 class="order-id" style="color: #990099">Order id: 111</h2>
            <a href="" target="_blank" class="print-button"><image class="img-thumbnail" style="width: 50px;" src="<?= $site_url ?>/img/print-icon.png" /></a>
    </div>
    <div class="failed-content">
        <div class="alert alert-danger">
            <strong>Failed!</strong> Error occurred, please call the administrator for more information.<br/>
            <span class="failed-reason"></span>
        </div>
    </div>

</center>
<form class="shop_form" >
  <!-- <input type ="hidden" id="has_discount" name="has_discount" value="<?= $reseller_row['has_discount']?>"/>
  <input type ="hidden" id="free_router" name="free_router" value="<?= $reseller_row['free_router']?>"/>
  <input type ="hidden" id="free_modem"  name="free_modem" value="<?= $reseller_row['free_modem']?>"/>
  <input type ="hidden" id="free_adapter" name="free_adapter" value="<?= $reseller_row['free_adapter']?>"/>
  <input type ="hidden" id="free_installation" name="free_installation" value="<?= $reseller_row['free_installation']?>"/>
  <input type ="hidden" id="free_transfer" name="free_transfer" value="<?= $reseller_row['free_transfer']?>"/> -->
    <!-- SmartWizard html -->
    <div id="smartwizard">
        <ul>
            <li><a href="#step-1">Step 1<br /><small>Choose Product</small></a></li>
            <li><a href="#step-2">Step 2<br /><small>Product details</small></a></li>
            <li><a href="#step-3">Step 3<br /><small>Customer's Information</small></a></li>
            <li><a href="#step-4">Step 4<br /><small>Checkout</small></a></li>
        </ul>

        <div>
            <div id="step-1" class="">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 d-inline">
                        <a href='' class="product-internet"><img src="<?= $site_url ?>/img/Internet-icon.png" class="img-thumbnail" style="width:150px"/></a>
                        <a href=''  class="product-phone"><img src="<?= $site_url ?>/img/phone-icon.png" class="img-thumbnail" style="width:150px"/></a>
                    </div>
                </div>

            </div>
            <div id="step-2" class="">


                <div class="internet">
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-success">
                                <div class="panel-heading">Speed</div>
                                <div class="panel-body">
                                    <select name="product" id="internet_products" class="form-control">

                                    </select>
                                </div>
                            </div>
                            </p>
                        </div>
                    </div>


                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_modem  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Plan</div>
                                <div class="panel-body" id="plans">

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-6" >
                            <p class="rounded form-row form-row-wide custom_modem  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Modem</div>
                                <div class="panel-body">
                                  <div id="modems">
                                  </div>
                                  <div class="modem-inventory-list">
                                      <select name="options[modem_id]" id="inventory_modems_list">

                                      </select>
                                      <input type="checkbox" class="input-text modem custom-options custom_field" data-price="60" name="options[inventory_modem_price]" value="yes" /> Pay modem price ($59.90 deposit)
                                  </div>
                                  <div class="modem-info">
                                      <b>Enter modem Serial Number:</b><br/><input style="width: 100%;" type="text" class="input-text custom-options custom_field" data-price="0" name="options[modem_serial_number]" value="" />
                                      <br/>
                                      <b>Enter modem MAC address:</b><br/><input style="width: 100%;" type="text" class="input-text modem-off custom-options custom_field" data-price="0" name="options[modem_mac_address]" value="" />
                                      <br/>
                                      <b>Enter modem Type:</b><br/><input style="width: 100%;" type="text" class="input-text modem-off custom-options custom_field" data-price="0" name="options[modem_modem_type]" value="" />
                                  </div>
                                </div>
                            </div>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="rounded form-row form-row-wide custom_router  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Router</div>
                                <div class="panel-body" id="routers">

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                    <div class="row"  style="width:100% !important;">
                        <div class="col-sm-6">
                            <p class="rounded form-row form-row-wide custom_are-you-currently-a-cable-subscriber  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Are you currently a cable subscriber?</div>
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" class="subscriber subscriber-on input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" value="yes" />Yes<br/>
                                    </label><br/>
                                    <label class="radio-inline">
                                        <input type="radio" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" value="no" />No<br/>
                                    </label>
                                    </br>
                                    <label class="subscriber-on">
                                        </br></br>
                                        Enter the name of your current cable provider:</br>
                                        <select class="form-control" name="options[current_cable_provider]" id="current_cable_provider">

                                            <option disabled="" selected="">Select a provider</option>

                                        </select>
                                        If other:</br>
                                        <input type="text" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[subscriber_other]"  /><br>
                                        Please select the cancellation date for your current Internet service:</br>

                                        <div class="date4">
                                            <div class="input-group input-append date" id="datePicker4">
                                                <input readonly="readonly" type="text" name="options[cancellation_date]" class="form-control" />
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </div>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="rounded  form-row form-row-wide custom_installation-date  ">
                            <div class="panel panel-primary installation">
                                <div class="panel-heading">Installation date</div>
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <b>1st choice</b>

                                        <div class="date1" style="display:none;">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <input readonly="readonly" type="text" name="" class="form-control" />
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>

                                        <div class="date5">
                                            <div class="input-group input-append date" id="datePicker5">
                                                <input readonly="readonly" type="text" name="options[installation_date_1]" class="form-control" />
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="before 12:00 PM"  />before 12:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="after 5:00 PM" />after 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="All Day " />All Day
                                        </label><br>
                                        <b>2nd choice</b>
                                        <div class="date2">
                                            <div class="input-group input-append date" id="datePicker2">
                                                <input readonly="readonly" type="text" name="options[installation_date_2]" class="form-control" />
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="before 12:00 PM " />before 12:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="after 5:00 PM " />after 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="All Day " />All Day
                                        </label><br>
                                        <b>3rd choice</b>
                                        <div class="date3">
                                            <div class="input-group input-append date" id="datePicker3">
                                                <input readonly="readonly" type="text" name="options[installation_date_3]" class="form-control" />
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="before 12:00 PM " />before 12:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="after 5:00 PM " />after 5:00 PM
                                        </label>
                                        <label class="radio-inline small">
                                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="All Day " />All Day
                                        </label><br>
                                    </label>
                                </div>
                            </div>

                            </p>
                        </div>

                    </div>
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-6" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Additional service</div>
                                <div class="panel-body" id="additional_service">

                                </div>
                            </div>
                            </p>
                        </div>

                        <div class="col-sm-6" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Static IP</div>
                                <div class="panel-body" id="static_ip">

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="phone">
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-success">
                                <div class="panel-heading">Phone</div>
                                <div class="panel-body">
                                    <select name="product" id="phone_products" class="form-control">

                                    </select>
                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_phone-adapter  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Phone Adapter</div>
                                <div class="panel-body" id="adapters">

                                </div>
                            </div>
                            <input type="hidden" name="options[product_type]" value="phone" />
                            </p>
                        </div>
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_do-you-have-a-phone-number  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Do you have a phone number</div>
                                <div class="panel-body">
                                  <div id="you_have_phone_number">

                                  </div>
                                    <label class="current-phone-subscriber">
                                        Your current phone number:<br/>
                                        <input type="text" class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[current_phone_number]" value="" /><br/>
                                    </label><br/>
                                </div>
                            </div>
                            </p>
                        </div>
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_province  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Province</div>
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        Please select your province:<br/>
                                        <select name="options[phone_province]" id="provinces" class="input-text plan plan-monthly custom-options custom_field">
                                        </select>
                                    </label><br/>

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="step-3" class="">
                <div class="form-group">
                    <label>Customer:</label>
                    <select name="customer_id" class="form-control customer_list">
                        <option value="0">New Customer</option>
                        <?php
                        $reseller = $dbToolsReseller->objCustomerTools($reseller_id, 2);
                        if (count($reseller->getResellerCustomers()) > 0)
                            foreach ($reseller->getResellerCustomers() as $customer) {
                                echo "<option type='"
                                . $customer->getSubscriptionType()
                                . "' value=\"" . $customer->getCustomerID() . "\">" . $customer->getFullName()
                                . "</option>\n";
                            }
                        ?>
                    </select>
                </div>
                <hr />
                <div class="new-customer-form">
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="full_name" value="" class="form-control" placeholder="Full Name"/>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="text" name="email" value="" class="form-control" placeholder="Email"/>
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone"/>
                    </div>
                    <div class="form-group">
                        <label>Address line 1:</label>
                        <input type="text" name="address_line_1" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Address line 2:</label>
                        <input type="text" name="address_line_2" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Postal code:</label>
                        <input type="text" name="postal_code" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>City:</label>
                        <input type="text" name="city" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="email">Note:</label>
                        <textarea type="text" name="note" class="form-control" /></textarea>
                    </div>
                </div>
            </div>
            <div id="step-4" class="">
                <div class="order_details">
                    <ul class="list-group">
                        <li class="list-group-item">Product <span class="badge product-name"></span></li>
                        <li class="list-group-item">Setup fees <span class="badge setup-cost"></span></li>
                        <li class="list-group-item">Remaining days cost (<span class="remaining-days-from-to"></span>) <span class="badge remaining-days-cost"></span></li>
                        <li class="list-group-item">Modem cost <span class="badge modem-cost"></span></li>
                        <li class="list-group-item">Router cost <span class="badge router-cost"></span></li>
                        <li class="list-group-item">Adapter cost <span class="badge adapter-cost"></span></li>
                        <li class="list-group-item">Additional Service <span class="badge additional-service-cost"></span></li>
                        <li class="list-group-item">Static IP <span class="badge static-ip-cost"></span></li>
                        <li class="list-group-item">Tax Fees (QST 9.975%) <span class="badge qst-cost"></span></li>
                        <li class="list-group-item">Tax Fees (GST 5%) <span class="badge gst-cost"></span></li>
                        <li class="list-group-item">Total <span class="badge total"></span></li>
                    </ul>
                </div>
                <br/>
                <br/>

                <div>
                    <div class="form-group">
                        <label>Payment Type:</label>
                        <select name="card_type" class="form-control">
                            <option value="MasterCard">MasterCard</option>
                            <option value="Visa Credit">Visa Credit</option>
                            <option value="Debit MasterCard">Debit MasterCard</option>
                            <option value="Visa Debit">Visa Debit</option>
                            <option value="cache_on_delivery">Cache on delivery</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Card Number:</label>
                        <input type="text" name="card_number" class="form-control" placeholder="Card Number"/>
                    </div>
                    <div class="form-group">
                        <label>Card Holder's Name:</label>
                        <input type="text" name="card_holders_name" class="form-control" placeholder="Card Holder's Name"/>
                    </div>
                    <div class="form-group">
                        <label>Card Expiry (MMYY):</label>
                        <input type="text" name="card_expiry" class="form-control" placeholder="MMYY"/>
                    </div>
                    <div class="form-group">
                        <label>Card CVV:</label>
                        <input type="text" name="card_cvv" class="form-control" placeholder="Card CVV"/>
                    </div>
                </div>
                <br/>
                <br/>
                <input type="submit" class="btn btn-primary btn-block btn-lg checkout-button"  value="Checkout!">
                <br/>
            </div>

        </div>
    </div>

</form>

<script type="text/javascript">

</script>
<?php
include_once "../footer.php";
?>
