<?php
include_once "../header.php";
?>

<!-- Include SmartWizard CSS -->
<link href="../dist/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<!-- Optional SmartWizard theme -->
<link href="<?= $site_url ?>/css/smart_wizard_theme_circles.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />

<!-- Include SmartWizard JavaScript source -->
<script type="text/javascript" src="<?= $site_url ?>/js/jquery.smartWizard.min.js"></script>
<script type="text/javascript" src="<?= $site_url ?>/js/shop_shop.js"></script>
<title>Shop</title>

<style>
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
<form class="" action="checkout.php" method="post">
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
                                    <select name="product" class="form-control">
                                        <option price='29.9' value='383' type="monthly">Internet 5 Mbps ($29.9)</option>
                                        <option price='34.9' value='335' type="monthly">Internet 10 Mbps ($34.9)</option>	
                                        <option price='39.9' value='380' type="monthly">Internet 15 Mbps ($39.9)</option>	
                                        <option price='44.9' value='381' type="monthly">Internet 20 Mbps ($44.9)</option>	
                                        <option price='49.9' value='414' type="monthly">Internet 30 Mbps ($49.9)</option>	
                                        <option price='59.9' value='416' type="monthly">Internet 60 Mbps ($59.9)</option>	
                                        <option price='79.9' value='418' type="monthly">Internet 120 Mbps ($79.9)</option>	
                                        <option price='99.9' value='419' type="monthly">Internet 200 Mbps ($99.9)</option>	
                                        <option price='159.9' value='420' type="monthly">Internet 940 Mbps ($159.9)</option>		
                                        <option price='322.92' value='687' type="yearly">Internet 5 Mbps Yearly ($322.92)</option>	
                                        <option price='376.9' value='689' type="yearly">Internet 10 Mbps Yearly ($376.9)</option>	
                                        <option price='430.9' value='690' type="yearly">Internet 15 Mbps Yearly ($430.9)</option>	
                                        <option price='484.9' value='692' type="yearly">Internet 20 Mbps Yearly ($484.9)</option>	
                                        <option price='538.9' value='694' type="yearly">Internet 30 Mbps Yearly ($538.9)</option>	
                                        <option price='646.9' value='695' type="yearly">Internet 60 Mbps Yearly ($646.9)</option>	
                                        <option price='754.9' value='696' type="yearly">Internet 120 Mbps Yearly ($754.9)</option>	
                                        <option price='970.9' value='697' type="yearly">Internet 200 Mbps Yearly ($970.9)</option>	
                                        <option price='1998.9' value='698' type="yearly">Internet 940 Mbps Yearly ($1998.9)</option>	
                                    </select>
                                </div>
                            </div>
                            </p>
                        </div>
                    </div>	
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-danger">
                                <div class="panel-heading">Check Service Availabilty</div>
                                <div class="panel-body">
                                    <a class="btn btn-info" id="check-service-availability" href="">
                                        Check Service Availabilty
                                    </a>
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
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" checked  class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[plan]" value="monthly" />Monthly Payment ($60.00 New Installation Fees <b>OR</b> $19.90 Transfer Fees for <span style="color:red;">current Cable subscriber</span>)<br/>
                                    </label><br/>	
                                    <label class="radio-inline">	
                                        <input type="radio" class="input-text plan plan-monthly-2 custom-options custom_field" data-price="" name="options[plan]" value="yearly"   />Yearly Contract, Payment Monthly (Free Installation)<br/>
                                    </label><br/>
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
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="rent" />Free Rent Modem ($59.90 deposit)
                                    </label>
                                    <br/>
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="inventory" />Reseller Inventory
                                    </label>
                                    <div class="modem-inventory-list">
                                        <select name="options[modem_id]">
                                            <?php
                                            $result_modems = $conn_routers->query("select * from `modems` where `reseller_id`='" . $reseller_id . "' and `customer_id`='0'");
                                            while ($row_modem = $result_modems->fetch_assoc()) {
                                                echo "<option value=\"" . $row_modem["modem_id"] . "\">" . $row_modem["mac_address"] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <br/>
                                    <label class="radio-inline">		
                                        <input type="radio" class="input-text modem-off modem custom-options custom_field" data-price="20" name="options[modem]" value="own_modem" />I have my own modem
                                    </label><br/><br/>
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
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text custom-options custom_field rent-router" data-price="2.90" name="options[router]" value="rent" />Rent WIFI Router MikroTik Hap Series ($2.90)<br/>
                                    </label><br/>	
                                    <label class="radio-inline">	
                                        <input type="radio" class="input-text custom-options custom_field" data-price="74.00" name="options[router]" value="buy_hap_ac_lite"   />Buy WIFI Router MikroTik Hap ac lite ($74.00)<br/>
                                    </label><br/>	
                                    <label class="radio-inline">	
                                        <input type="radio" class="input-text custom-options custom_field" data-price="39.90" name="options[router]" value="buy_hap_mini"   />Buy WIFI Router MikroTik Hap mini ($39.90)<br/>
                                    </label><br/>		
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text router-off custom-options custom_field" data-price="0" name="options[router]" value="dont_need" />I don't need a router
                                    </label>
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
                                        <select class="form-control" name="options[current_cable_provider]">

                                            <option disabled="" selected="">Select a provider</option>
                                            <option value="Acanac">Acanac</option>
                                            <option value="ACN">ACN</option>
                                            <option value="B2B2C">B2B2C</option>
                                            <option value="CIK">CIK</option>
                                            <option value="Distributel">Distributel</option>
                                            <option value="Electronibox">Electronibox</option>
                                            <option value="iTalk BB">iTalk BB</option>
                                            <option value="Rogers">Rogers</option>
                                            <option value="Shaw">Shaw</option>
                                            <option value="TekSavvy">TekSavvy</option>
                                            <option value="videotron">Videotron</option>
                                            <option value="altimatel">Altimatel</option>
                                            <option value="jamestelecom">James Telecom</option>
                                            <option value="other">Other</option>
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
                                <div class="panel-body">
                                    <input type="checkbox" class="input-text custom-options custom_field" data-price="0" name="options[additional_service]" value="yes" /> Additional service
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
                                    <select name="product" class="form-control">
                                        <option price='10' value='619' type="monthly">Canadian Phone ($10.0)</option>
                                        <option price='15' value='654' type="monthly">Canada & US Phone ($15.0)</option>
                                        <option price='100' value='653' type="yearly">Canada Phone – 1 year ($100.0)</option>	
                                        <option price='120' value='661' type="yearly">Canada & US Phone – 1 year ($120.0)</option>		
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
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text plan custom-options custom_field" data-price="" name="options[adapter]" value="my_own" />I have my own Phone Adapter<br/>
                                    </label><br/>	
                                    <label class="radio-inline">	
                                        <input type="radio" checked class="input-text plan custom-options custom_field" data-price="" name="options[adapter]" value="buy_Cisco_SPA112"   />Buy Cisco SPA112 2-Port Phone Adapter ($59.90)<br/>
                                    </label><br/>	
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
                                    <label class="radio-inline">
                                        <input type="radio" class="phone-subscriber input-text plan plan-monthly custom-options custom_field" data-price="" name="options[you_have_phone_number]" value="yes" />Transfer current number ($15)<br/>
                                    </label><br/>	
                                    <label class="current-phone-subscriber">
                                        Your current phone number:<br/>
                                        <input type="text" class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[current_phone_number]" value="" /><br/>
                                    </label><br/>
                                    <label class="radio-inline">	
                                        <input type="radio" checked class="phone-subscriber new-number input-text plan custom-options custom_field" data-price="" name="options[you_have_phone_number]" value="no"   />New phone number<br/>
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
                                        <select name="options[phone_province]" class="input-text plan plan-monthly custom-options custom_field">
                                            <option value='ON'>ONTARIO (ON)</option><option value='QC'>QUEBEC (QC)</option><option value='AB'>ALBERTA (AB)</option><option value='BC'>BRITISH COLUMBIA (BC)</option><option value='MB'>MANITOBA (MB)</option><option value='NS'>NOVA-SCOTIA (NS)</option><option value='NL'>NEWFOUNDLAND (NL)</option>                </select>
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
                        $reseller = $dbTools->objCustomerTools($reseller_id, 2);
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