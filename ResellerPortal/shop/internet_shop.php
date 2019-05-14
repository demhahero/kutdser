<?php
include_once "../header.php";
$dbToolsReseller->query("SET CHARACTER SET utf8");

$reseller = $dbToolsReseller->query("SELECT `discount_expire_date`,`has_discount`,`free_modem`,`free_router`,`free_adapter`,`free_installation`,`free_transfer`,`full_name` FROM `customers` WHERE `customer_id` = '" . $reseller_id . "'");
$reseller_row=$dbToolsReseller->fetch_assoc($reseller);

$today_date = new DateTime();
$discount_expire_date=(isset($reseller_row['discount_expire_date']) && strlen($reseller_row['discount_expire_date'])>0)?new DateTime($reseller_row['discount_expire_date']):new DateTime($today_date->format('Y-m-d'));
$discount_days_remaining=0;
if($discount_expire_date>$today_date)
$discount_days_remaining=$discount_expire_date->diff($today_date)->days;

if($discount_days_remaining>0)
{
  $has_discount= ($reseller_row['has_discount']==="yes"?TRUE:FALSE);
  $free_modem= ($reseller_row['free_modem']==="yes"?TRUE:FALSE);
  $free_router= ($reseller_row['free_router']==="yes"?TRUE:FALSE);
  $free_adapter= ($reseller_row['free_adapter']==="yes"?TRUE:FALSE);
  $free_installation= ($reseller_row['free_installation']==="yes"?TRUE:FALSE);
  $free_transfer= ($reseller_row['free_transfer']==="yes"?TRUE:FALSE);

}
else {
  $has_discount= FALSE;
  $free_modem= FALSE;
  $free_router= FALSE;
  $free_adapter= FALSE;
  $free_installation= FALSE;
  $free_transfer= FALSE;

}


$products = $dbToolsReseller->query("SELECT * FROM `products` INNER JOIN `reseller_discounts` on `products`.`product_id`=`reseller_discounts`.`product_id` WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "' and `products`.`product_id` NOT IN ('699','700')");

if($products->num_rows ==0)
$products = $dbToolsReseller->query("SELECT * FROM `products`where `products`.`product_id` NOT IN ('699','700')");
$products_rows=[];
while($products_row=$dbToolsReseller->fetch_assoc($products))
{
  array_push($products_rows,$products_row);
}


?>

<!-- Include SmartWizard CSS -->
<link href="<?= $site_url ?>/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<!-- Optional SmartWizard theme -->
<link href="<?= $site_url ?>/css/smart_wizard_theme_circles.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
<link href="<?= $site_url ?>/css/smart_wizard_theme_dots.css" rel="stylesheet" type="text/css" />

<!-- Include SmartWizard JavaScript source -->
<script type="text/javascript" src="<?= $site_url ?>/js/jquery.smartWizard.min.js"></script>
<script type="text/javascript" src="<?= $site_url ?>/js/shop_shop_test1.js"></script>
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
<form class="" action="internet_checkout.php" method="post">
  <input type ="hidden" id="has_discount" name="has_discount" value="<?= $reseller_row['has_discount']?>"/>
  <input type ="hidden" id="free_router" name="free_router" value="<?= $reseller_row['free_router']?>"/>
  <input type ="hidden" id="free_modem"  name="free_modem" value="<?= $reseller_row['free_modem']?>"/>
  <input type ="hidden" id="free_adapter" name="free_adapter" value="<?= $reseller_row['free_adapter']?>"/>
  <input type ="hidden" id="free_installation" name="free_installation" value="<?= $reseller_row['free_installation']?>"/>
  <input type ="hidden" id="free_transfer" name="free_transfer" value="<?= $reseller_row['free_transfer']?>"/>
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
                    <div class="col-md-6 col-md-offset-3 d-inline">
                        <a href='' class="product-internet"><img src="<?= $site_url ?>/img/Internet-icon.png" class="img-thumbnail" style="width:150px"/></a>
                    </div>
                </div>

            </div>
            <div id="step-2" class="">

              <?PHP
              if($discount_days_remaining>0){?>
                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                    </button>
                    Your offer discount will ends after  <strong><?= $discount_days_remaining?></strong>
                  </div>
              <?PHP }?>
                <div class="internet">
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-success">
                                <div class="panel-heading">Speed</div>
                                <div class="panel-body">
                                    <select name="product" class="form-control">
                                      <?php foreach ($products_rows as $product):
                                          if ($product['category']==="internet"){
                                            $price=$product['price'];
                                            $title=$product['title'];

                                            if($has_discount && isset($product['discount']) && (int)$product['discount']>0)
                                            {

                                              $price=(float)$product['price']-((float)$product['price']*(((float)$product['discount']/100)));
                                              $price=round($price,2);
                                              $discount_duration=$product['discount_duration'];
                                              $discount_duration=str_replace("_"," ",$discount_duration);
                                              $discount_duration= ucfirst($discount_duration);
                                              $title=$product['title']." (".$product['price']." $) (with discount ".$product['discount']."% for ".$discount_duration.")";

                                            }

                                            ?>
                                        <option real_price='<?=$product['price']?>' data_title='<?= $product['title']?>'  price='<?= $price?>' value='<?= $product['product_id']?>' type="<?= $product['subscription_type']?>"> <?= $title." (".$price."$)"?></option>
                                      <?php }
                                      endforeach; ?>
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
                    <?php
                    if($has_discount){
                       ?>
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-success">
                                <div class="panel-heading">Discounts Note:</div>
                                <div class="panel-body">
                                    <p class="success">
                                        All discounts are available on yearly subscription plan only
                                    </p>
                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                    <?php
                    }
                       ?>
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_modem  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Plan</div>
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" checked  class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[plan]" value="monthly" />Monthly Payment ($60.00 New Installation Fees <b>OR</b> $19.90 Transfer Fees for <span style="color:red;">current Cable subscriber</span>)
                                    </label><br/>
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text plan plan-monthly-2 custom-options custom_field" data-price="" name="options[plan]" value="yearly"   />Yearly Contract, Payment Monthly (Free Installation)<br/>
                                        <?= $free_installation?" </br><span style='color:green;' class='discount_offer' >you have a limited offer, now installation fees are free for you  </span>":""?>
                                        <?= $free_transfer?" </br><span style='color:green;' class='discount_offer'>you have a limited offer, now transfer fees are free for you  </span>":""?>
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
                                        <?= $free_modem? "<span style='color:green' class='discount_offer'> you have a limited offer free modem deposit</span>":""?>
                                    </label>
                                    <br/>
                                    <?php if($reseller_id==="190"){?>
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text modem custom-options custom_field" data-price="200" name="options[modem]" value="buy" />Business Modem
                                    </label>
                                    <br/>
                                  <?php }?>
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="inventory" />Reseller Inventory
                                    </label>
                                    <div class="modem-inventory-list">
                                        <select name="options[modem_id]">
                                            <?php
                                            $result_modems = $dbToolsReseller->query("select * from `modems` where `reseller_id`='" . $reseller_id . "' and `customer_id`='0'");
                                            while ($row_modem = $result_modems->fetch_assoc()) {
                                                echo "<option value=\"" . $row_modem["modem_id"] . "\">" . $row_modem["mac_address"] . "[" . $row_modem["type"] . " | " . $row_modem["serial_number"] . "]" . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="checkbox" class="input-text modem custom-options custom_field" data-price="60" name="options[inventory_modem_price]" value="yes" /> Pay modem price ($59.90 deposit)
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
                                        <input type="radio" class="input-text custom-options custom_field rent-router" data-price="2.90" name="options[router]" value="rent" />Rent WIFI Router MikroTik Hap Series ($2.90)
                                        <?= $free_router?" <span style='color:green;' class='discount_offer'>you have a limited offer, now router rent is free for you  </span>":""?>
                                    </label><br/>
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text custom-options custom_field" data-price="4.90" name="options[router]" value="rent_hap_lite"   />Rent WIFI Router MikroTik Hap lite ($4.90)<br/>
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
                                  <label class="checkbox-inline">
                                    <input type="checkbox" class="input-text custom-options custom_field" data-price="0" name="options[additional_service]" value="yes" /> Additional service
                                  </label>
                                </div>
                            </div>
                            </p>
                        </div>
                        <?php if($reseller_id==="190" || $reseller_id==="1379"){?>
                        <div class="col-sm-6" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Static IP</div>
                                <div class="panel-body">
                                  <label class="checkbox-inline">
                                    <input type="checkbox" class="input-text custom-options custom_field" data-price="20" name="options[static_ip]" value="yes" /> Static IP
                                  </label>
                                </div>
                            </div>
                            </p>
                        </div>
                        <?PHP }?>
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
                            <?php
                            if($reseller_id=="190" || $reseller_id=="11" || $reseller_id=="12" || $reseller_id=="174" || $reseller_id=="175" || $reseller_id=="319" || $reseller_id=="1553" || $reseller_id=="1641" || $reseller_id=="1827" || $reseller_id=="1830")
                            {
                            ?>
                                <option value="cache_on_delivery">Cache on delivery</option>
                            <?php
                            }
                            ?>
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
