<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "../header.php";
include_once "../dbconfig.php";
$order_id=isset($_GET['order_id'])? $_GET['order_id'] :0;
$orderDetails=null;
$customers=null;
$resellers=null;
$products=null;
$admins=null;
$connection->query("SET CHARACTER SET utf8");
////////////////////////// get all order details
$sql = "SELECT * FROM `orders` INNER JOIN `order_options` on `orders`.`order_id`=`order_options`.`order_id` where `orders`.`order_id`=".$order_id;
$result = $connection->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    $orderDetails=$row;
  }
if($orderDetails)
{
//////////// get list of all customers with fullname and id
$sql = "SELECT `customer_id`,`full_name` FROM `customers` WHERE `is_reseller`=0";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
  $customers=[];
    while ($row = $result->fetch_assoc())
    {
      array_push($customers,$row);
    }
  }
/////////////////////// get list of all resellers full name and id
$sql = "SELECT `customer_id`,`full_name` FROM `customers` WHERE `is_reseller`=1";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
  $resellers=[];
    while ($row = $result->fetch_assoc())
    {
      array_push($resellers,$row);
    }
  }
/////////////////////// get list of all product  name and id
$sql = "SELECT `product_id`,`title`,`price`,`category`,`subscription_type` FROM `products` ";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
  $products=[];
    while ($row = $result->fetch_assoc())
    {
      array_push($products,$row);
    }

  }
/////////////////////// get list of all admins  name and id
$sql = "SELECT `admin_id`,`username` FROM `admins` ";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
  $admins=[];
    while ($row = $result->fetch_assoc())
    {
      array_push($admins,$row);
    }

  }
}
?>
<script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/options.js></script>

<script src=https://www.amprotelecom.com/wp-content/plugins/woocommerce-custom-options-lite/assets/js/bootstrap-datepicker.min.js></script>
<link rel="stylesheet" href="http://localhost/kutdser/ResellerPortal/css/bootstrap-datepicker3.css">

<script type="text/javascript" src="<?= $site_url ?>/js/shop_shop.js"></script>
<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 60px;
        height: 60px;
        margin:0 auto;
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
</style>
<title>Edit Order</title>
<div class="page-header">
    <h4>Edit Order</h4>
</div>
<div  class="row">
  <?php
  if($orderDetails){
  if(isset($_GET['type']) && $_GET['type']==="internet") {?>
    <div class="internet">
      <form action="<?=$api_url?>update_order_api.php" method="POST" id="updateForm">
        <input type="hidden" name="order_id" value="<?= $_GET['order_id']?>"/>
        <div class="row" style="width:100% !important;">
          <div class="col-sm-12" >
              <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
              <div class="panel panel-success">
                  <div class="panel-heading">Status</div>
                  <div class="panel-body">
                      <select name="status" class="form-control">
                          <option  value="none" >None</option>
                          <option <?= ($orderDetails['status']==="sent")?"selected":"" ?> value='sent' >Sent</option>
                          <option <?= ($orderDetails['status']==="processing")?"selected":"" ?> value='processing' >Processing</option>
                          <option <?= ($orderDetails['status']==="complete")?"selected":"" ?> value='complete'>Complete</option>

                      </select>
                  </div>
              </div>
              </p>
          </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Product</div>
                    <div class="panel-body">
                        <select name="product_id" class="form-control">
                          <option value="0" type="none" >None</option>
                          <?php
                          foreach ($products as $product) {
                            ?>
                            <option <?= ($orderDetails['product_id']===$product['product_id'])?'selected="selected"':"" ?> value="<?php echo $product['product_id'];?>" type="<?php echo $product['subscription_type'];?>"><?php echo $product['title']." (".$product['price'].")"; ?></option>
                            <?php
                          } ?>
                        </select>
                    </div>
                </div>
                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Reseller</div>
                    <div class="panel-body">
                        <select name="reseller_id" class="form-control">
                          <option value="0" >None</option>
                          <?php
                          foreach ($resellers as $reseller) {
                            ?>
                            <option <?= ($orderDetails['reseller_id']===$reseller['customer_id'])?'selected="selected"':"" ?> value="<?php echo $reseller['customer_id'];?>" ><?php echo $reseller['full_name']; ?></option>
                            <?php
                          } ?>
                        </select>
                    </div>
                </div>
                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Customer</div>
                    <div class="panel-body">
                        <select name="customer_id" class="form-control">
                          <option value="0" >None</option>
                          <?php
                          foreach ($customers as $customer) {
                            ?>
                            <option <?= ($orderDetails['customer_id']===$customer['customer_id'])?'selected="selected"':"" ?> value="<?php echo $customer['customer_id'];?>" ><?php echo $customer['full_name']; ?></option>
                            <?php
                          } ?>
                        </select>
                    </div>
                </div>
                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Extra orde recurring Status</div>
                    <div class="panel-body">
                      <input class="form-control" type="text" name="extra_order_recurring_status" value="<?PHP echo $orderDetails['extra_order_recurring_status'];?>" placeholder="pending"/>
                    </div>
                </div>
                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Admin</div>
                    <div class="panel-body">
                        <select name="admin_id" class="form-control">
                          <option value="0" >None</option>
                          <?php
                          foreach ($admins as $admin) {
                            ?>
                            <option <?= ($orderDetails['admin_id']===$admin['admin_id'])?'selected="selected"':"" ?> value="<?php echo $admin['admin_id'];?>" ><?php echo $admin['username']; ?></option>
                            <?php
                          } ?>
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
                          <input type="radio" checked  class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[plan]" value="none" /> None<br/>
                      </label><br/>
                        <label class="radio-inline">
                            <input type="radio" <?= ($orderDetails['plan']==="monthly")?'checked':"" ?>  class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[plan]" value="monthly" />Monthly Payment ($60.00 New Installation Fees <b>OR</b> $19.90 Transfer Fees for <span style="color:red;">current Cable subscriber</span>)<br/>
                        </label><br/>
                        <label class="radio-inline">

                            <input type="radio" <?= ($orderDetails['plan']==="yearly")?'checked':"" ?> class="input-text plan plan-monthly-2 custom-options custom_field" data-price="" name="options[plan]" value="yearly"   />Yearly Contract, Payment Monthly (Free Installation)<br/>
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
                          <input type="radio" checked class="input-text modem custom-options custom_field" data-price="0" name="options[modem]" value="none" />None
                      </label>
                      <br/>
                        <label class="radio-inline">
                            <input type="radio" <?= ($orderDetails['modem']==="rent")?'checked':"" ?> class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="rent" />Free Rent Modem ($59.90 deposit)
                        </label>
                        <br/>
                        <label class="radio-inline">
                            <input type="radio" <?= ($orderDetails['modem']==="inventory")?'checked':"" ?> class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="inventory" />Reseller Inventory
                        </label>
                        <div class="modem-inventory-list">
                            <select name="options[modem_id]" class="form-control">
                                <?php
                                $result_modems = $conn_routers->query("select * from `modems` where `reseller_id`='" . $orderDetails['reseller_id'] . "' and `customer_id`='0'");
                                while ($row_modem = $result_modems->fetch_assoc()) {
                                  ?>
                                    <option <?=($orderDetails['modem_id']==$row_modem["modem_id"])?'selected="selected"':""?> value="<?= $row_modem["modem_id"]?>"><?= $row_modem["mac_address"] . "[" . $row_modem["type"] . " | " . $row_modem["serial_number"] . "]"?></option>";
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <br/>
                        <label class="radio-inline">
                            <input type="radio" <?= ($orderDetails['modem']==="own_modem")?'checked':"" ?> class="input-text modem-off modem custom-options custom_field" data-price="20" name="options[modem]" value="own_modem" />I have my own modem
                        </label><br/><br/>
                        <div class="modem-info">
                            <b>Enter modem Serial Number:</b><br/><input style="width: 100%;" type="text" class="form-control" data-price="0" name="options[modem_serial_number]" value="<?= $orderDetails['modem_serial_number']?>" />
                            <br/>
                            <b>Enter modem MAC address:</b><br/><input style="width: 100%;" type="text" class="form-control" data-price="0" name="options[modem_mac_address]" value="<?= $orderDetails['modem_mac_address']?>" />
                            <br/>
                            <b>Enter modem Type:</b><br/><input style="width: 100%;" type="text" class="form-control" data-price="0" name="options[modem_modem_type]" value="<?= $orderDetails['modem_modem_type']?>" />
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
                            <input type="radio" class="input-text custom-options custom_field rent-router" data-price="2.90" name="options[router]" <?= ($orderDetails['router']==="rent")?'checked':"" ?> value="rent" />Rent WIFI Router MikroTik Hap Series ($2.90)<br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" class="input-text custom-options custom_field" data-price="74.00" name="options[router]" <?= ($orderDetails['router']==="buy_hap_ac_lite")?'checked':"" ?> value="buy_hap_ac_lite"   />Buy WIFI Router MikroTik Hap ac lite ($74.00)<br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" class="input-text custom-options custom_field" data-price="39.90" name="options[router]" <?= ($orderDetails['router']==="buy_hap_mini")?'checked':"" ?> value="buy_hap_mini"   />Buy WIFI Router MikroTik Hap mini ($39.90)<br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" class="input-text router-off custom-options custom_field" data-price="0" name="options[router]" <?= ($orderDetails['router']==="dont_need")?'checked':"" ?> value="dont_need" />I don't need a router
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
                          <input type="radio" class="subscriber subscriber-on input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" checked value="none" />None<br/>
                      </label><br/>
                        <label class="radio-inline">
                            <input type="radio" class="subscriber subscriber-on input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" <?= ($orderDetails['cable_subscriber']==="yes")?'checked':"" ?> value="yes" />Yes<br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" <?= ($orderDetails['cable_subscriber']==="no")?'checked':"" ?> value="no" />No<br/>
                        </label>
                        </br>
                        <label class="subscriber-on">
                            </br></br>
                            Enter the name of your current cable provider:</br>
                            <select class="form-control" name="options[current_cable_provider]">

                                <option selected value="none">Select a provider</option>
                                <option <?=($orderDetails['current_cable_provider']=="Acanac")?'selected="selected"':""?> value="Acanac">Acanac</option>
                                <option <?=($orderDetails['current_cable_provider']=="ِACN")?'selected="selected"':""?> value="ACN">ACN</option>
                                <option <?=($orderDetails['current_cable_provider']=="B2B2C")?'selected="selected"':""?> value="B2B2C">B2B2C</option>
                                <option <?=($orderDetails['current_cable_provider']=="CIK")?'selected="selected"':""?> value="CIK">CIK</option>
                                <option <?=($orderDetails['current_cable_provider']=="Distributel")?'selected="selected"':""?> value="Distributel">Distributel</option>
                                <option <?=($orderDetails['current_cable_provider']=="Electronibox")?'selected="selected"':""?> value="Electronibox">Electronibox</option>
                                <option <?=($orderDetails['current_cable_provider']=="iTalk BB")?'selected="selected"':""?> value="iTalk BB">iTalk BB</option>
                                <option <?=($orderDetails['current_cable_provider']=="Rogers")?'selected="selected"':""?> value="Rogers">Rogers</option>
                                <option <?=($orderDetails['current_cable_provider']=="ٍShaw")?'selected="selected"':""?> value="Shaw">Shaw</option>
                                <option <?=($orderDetails['current_cable_provider']=="TekSavvy")?'selected="selected"':""?> value="TekSavvy">TekSavvy</option>
                                <option <?=($orderDetails['current_cable_provider']=="videotron")?'selected="selected"':""?> value="videotron">Videotron</option>
                                <option <?=($orderDetails['current_cable_provider']=="altimatel")?'selected="selected"':""?> value="altimatel">Altimatel</option>
                                <option <?=($orderDetails['current_cable_provider']=="jamestelecom")?'selected="selected"':""?> value="jamestelecom">James Telecom</option>
                                <option <?=($orderDetails['current_cable_provider']=="other")?'selected="selected"':""?> value="other">Other</option>
                            </select>
                            If other:</br>
                            <input type="text" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[subscriber_other]"  /><br>
                            Please select the cancellation date for your current Internet service:</br>

                            <div class="date4">
                                <div class="input-group input-append date" id="datePicker4">
                                  <?php
                                  $cancellation_date="";
                                  if (DateTime::createFromFormat('Y-m-d G:i:s', $orderDetails['cancellation_date']) !== FALSE) {
                                    // it's a date
                                    $cancellation_date_object=new DateTime($orderDetails['cancellation_date']);
                                    $cancellation_date=$cancellation_date_object->format('m/d/Y');
                                  }

                                   ?>
                                    <input readonly="readonly" type="text" name="options[cancellation_date]" class="form-control" value="<?= $cancellation_date?> " />
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

                            <?php
                            $installation_date_1="";
                            if (DateTime::createFromFormat('Y-m-d G:i:s', $orderDetails['installation_date_1']) !== FALSE) {
                              // it's a date
                              $installation_date_1_object=new DateTime($orderDetails['installation_date_1']);
                              $installation_date_1=$installation_date_1_object->format('m/d/Y');
                            }

                             ?>
                            <div class="date5">
                                <div class="input-group input-append date" id="datePicker5">
                                    <input readonly="readonly" type="text" name="options[installation_date_1]" value="<?= $installation_date_1?>" class="form-control" />
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_1]" <?= ($orderDetails['installation_time_1']=="before 12:00 PM")?"checked":""?> value="before 12:00 PM"  />before 12:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_1]" <?= ($orderDetails['installation_time_1']=="12:00 PM - 5:00 PM")?"checked":""?> value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_1]" <?= ($orderDetails['installation_time_1']=="after 5:00 PM")?"checked":""?> value="after 5:00 PM" />after 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_1]" <?= ($orderDetails['installation_time_1']=="All Day ")?"checked":""?> value="All Day " />All Day
                            </label><br>
                            <b>2nd choice</b>
                            <?php
                            $installation_date_2="";
                            if (DateTime::createFromFormat('Y-m-d G:i:s', $orderDetails['installation_date_2']) !== FALSE) {
                              // it's a date
                              $installation_date_2_object=new DateTime($orderDetails['installation_date_2']);
                              $installation_date_2=$installation_date_2_object->format('m/d/Y');
                            }

                             ?>
                            <div class="date2">
                                <div class="input-group input-append date" id="datePicker2">
                                    <input readonly="readonly" type="text" name="options[installation_date_2]" value="<?= $installation_date_2?>" class="form-control" />
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_2]" <?= ($orderDetails['installation_time_2']=="before 12:00 PM ")?"checked":""?> value="before 12:00 PM " />before 12:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_2]" <?= ($orderDetails['installation_time_2']=="12:00 PM - 5:00 PM")?"checked":""?> value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_2]" <?= ($orderDetails['installation_time_2']=="after 5:00 PM ")?"checked":""?> value="after 5:00 PM " />after 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_2]" <?= ($orderDetails['installation_time_2']=="All Day ")?"checked":""?> value="All Day " />All Day
                            </label><br>
                            <b>3rd choice</b>
                            <?php
                            $installation_date_3="";
                            if (DateTime::createFromFormat('Y-m-d G:i:s', $orderDetails['installation_date_3']) !== FALSE) {
                              // it's a date
                              $installation_date_3_object=new DateTime($orderDetails['installation_date_3']);
                              $installation_date_3=$installation_date_3_object->format('m/d/Y');
                            }

                             ?>
                            <div class="date3">
                                <div class="input-group input-append date" id="datePicker3">
                                    <input readonly="readonly" type="text" name="options[installation_date_3]" value="<?= $installation_date_3?>" class="form-control" />
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_3]" <?= ($orderDetails['installation_time_3']=="before 12:00 PM ")?"checked":""?> value="before 12:00 PM " />before 12:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_3]" <?= ($orderDetails['installation_time_3']=="12:00 PM - 5:00 PM")?"checked":""?> value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_3]" <?= ($orderDetails['installation_time_3']=="after 5:00 PM ")?"checked":""?> value="after 5:00 PM " />after 5:00 PM
                            </label>
                            <label class="radio-inline small">
                                <input type="radio" name="options[installation_time_3]" <?= ($orderDetails['installation_time_3']=="All Day ")?"checked":""?> value="All Day " />All Day
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
                        <input type="checkbox" name="options[additional_service]" <?= ($orderDetails['additional_service']=="yes")?"checked":""?> value="yes" /> Additional service
                    </div>
                </div>
                </p>
            </div>
        </div>
        <div class="row" style="width:100% !important;">
            <div class="col-sm-6" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-primary">
                    <div class="panel-heading">Prices</div>
                    <div class="panel-body">

                      <div class="form-group">
                      <b>Product Price</b>
                        <input type="text" class="form-control" placeholder="39.9" name="options[product_price]" value="<?PHP echo $orderDetails['product_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>Additional Service Price</b>
                        <input type="text" class="form-control" placeholder="5" name="options[additional_service_price]" value="<?PHP echo $orderDetails['additional_service_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>Setup Price</b>
                        <input type="text" class="form-control" placeholder="0" name="options[setup_price]" value="<?PHP echo $orderDetails['setup_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>Modem Price</b>
                        <input type="text" class="form-control" placeholder="0" name="options[modem_price]" value="<?PHP echo $orderDetails['modem_price']; ?>" />
                      </div>

                      <div class="form-group">
                      <b>Router Price</b>
                        <input type="text" class="form-control" placeholder="0" name="options[router_price]" value="<?PHP echo $orderDetails['router_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>Remaining Days Price</b>
                        <input type="text" class="form-control" placeholder="0" name="options[remaining_days_price]" value="<?PHP echo $orderDetails['remaining_days_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>Total Price</b>
                        <input type="text" class="form-control" placeholder="0" name="options[total_price]" value="<?PHP echo $orderDetails['total_price']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>QST Tax</b>
                        <input type="text" class="form-control" placeholder="0" name="options[qst_tax]" value="<?PHP echo $orderDetails['qst_tax']; ?>" />
                      </div>
                      <div class="form-group">
                      <b>GST Tax</b>
                        <input type="text" class="form-control" placeholder="0" name="options[gst_tax]" value="<?PHP echo $orderDetails['gst_tax']; ?>" />
                      </div>

                      <div class="form-group">
                          <label for="options[adapter_price]">Adapter Price</label>
                        <input type="text" class="form-control" placeholder="0"  name="options[adapter_price]" value="<?PHP echo $orderDetails['adapter_price']; ?>" />
                      </div>
                    </div>
                </div>
                </p>
            </div>
        </div>
        <div class="row" style="width:100% !important;">
            <div class="col-sm-6" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-primary">
                    <div class="panel-heading">Installation Info</div>
                    <div class="panel-body">
                      <div class="form-group">
                          <label for="email">Completion:</label>
                          <input type="text" name="options[completion]" value="<?= $orderDetails['completion'] ?>" class="form-control" placeholder="Completion"/>
                      </div>
                      <?php
                      $actual_installation_date="";
                      if (DateTime::createFromFormat('Y-m-d G:i:s', $orderDetails['actual_installation_date']) !== FALSE) {
                        // it's a date
                        $actual_installation_date_object=new DateTime($orderDetails['actual_installation_date']);
                        $actual_installation_date=$actual_installation_date_object->format('m/d/Y');
                      }

                       ?>
                      <div class="form-group">
                          <label for="email">Actual installation date:</label>
                          <input type="text" readonly="" name="options[actual_installation_date]" value="<?= $actual_installation_date?>" class="form-control datepicker" placeholder="Actual installation date"/>
                      </div>
                      <div class="form-group">
                          <label for="email">Actual installation time from:</label>
                          <input type="text" name="options[actual_installation_time_from]" value="<?= $orderDetails['actual_installation_time_from'] ?>" class="form-control" placeholder="Actual installation time from"/>
                      </div>
                      <div class="form-group">
                          <label for="email">Actual installation time to:</label>
                          <input type="text" name="options[actual_installation_time_to]" value="<?= $orderDetails['actual_installation_time_to'] ?>" class="form-control" placeholder="Actual installation time to"/>
                      </div>
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
                            <input type="radio" name="options[adapter]" <?= ($orderDetails['adapter']=="my_own")?"checked":""?> value="my_own" />I have my own Phone Adapter<br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" name="options[adapter]" <?= ($orderDetails['adapter']=="buy_Cisco_SPA112")?"checked":""?> value="buy_Cisco_SPA112"   />Buy Cisco SPA112 2-Port Phone Adapter ($59.90)<br/>
                        </label><br/>
                    </div>
                </div>

                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_do-you-have-a-phone-number  ">
                <div class="panel panel-primary">
                    <div class="panel-heading">Do you have a phone number</div>
                    <div class="panel-body">
                        <label class="radio-inline">
                            <input type="radio" name="options[you_have_phone_number]" <?= (strlen($orderDetails['current_phone_number'])>3)?"checked":""?> value="yes" />Transfer current number ($15)<br/>
                        </label><br/>
                        <label class="current-phone-subscriber">
                            Your current phone number:<br/>
                            <input type="text" name="options[current_phone_number]" value="<?= $orderDetails['current_phone_number']?>" /><br/>
                        </label><br/>
                        <label class="radio-inline">
                            <input type="radio" name="options[you_have_phone_number]" <?= (strlen($orderDetails['current_phone_number'])<=0)?"checked":""?> value="no"   />New phone number<br/>
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
                            <select name="options[phone_province]" class="form-control">
                                <option selected value="none">None</option>
                                <option <?= ($orderDetails['phone_province']=="ON")?"selected":""?> value='ON'>ONTARIO (ON)</option>
                                <option <?= ($orderDetails['phone_province']=="QC")?"selected":""?> value='QC'>QUEBEC (QC)</option>
                                <option <?= ($orderDetails['phone_province']=="AB")?"selected":""?> value='AB'>ALBERTA (AB)</option>
                                <option <?= ($orderDetails['phone_province']=="BC")?"selected":""?> value='BC'>BRITISH COLUMBIA (BC)</option>
                                <option <?= ($orderDetails['phone_province']=="MB")?"selected":""?> value='MB'>MANITOBA (MB)</option>
                                <option <?= ($orderDetails['phone_province']=="NS")?"selected":""?> value='NS'>NOVA-SCOTIA (NS)</option>
                                <option <?= ($orderDetails['phone_province']=="NL")?"selected":""?> value='NL'>NEWFOUNDLAND (NL)</option>                </select>
                        </label><br/>

                    </div>
                </div>
                </p>
            </div>
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_province  ">
                <div class="panel panel-primary">
                    <div class="panel-heading">Note</div>
                    <div class="panel-body">
                        <label class="radio-inline">
                            Write your note if you have one:<br/>
                            <textarea class="form-control" name="options[note]"><?= $orderDetails['note']?></textarea>
                        </label>

                    </div>
                </div>
                </p>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
    <?php
  }
  else if(isset($_GET['type']) && $_GET['type']==="phone") { ?>
    <div class="phone">
      <form action="#" method="POST" id="updateForm">
        <div class="row" style="width:100% !important;">
            <div class="col-sm-12" >
                <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                <div class="panel panel-success">
                    <div class="panel-heading">Phone</div>
                    <div class="panel-body">
                        <select name="product_id" class="form-control">
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
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
    <?php
  }else {
    echo "<h3>this subscription type is not supported yet</h3>";
  }
}?>
</div>
<script>
function validateChooseProduct() {

        //If own_moden selected, you have to enter modem information
        if ($("input[name=\"options[modem]\"]:checked").val() == "own_modem") {
            if ($("input[name=\"options[modem_serial_number]\"]").val().length < 3
                    || $("input[name=\"options[modem_mac_address]\"]").val().length < 3
                    || $("input[name=\"options[modem_modem_type]\"]").val().length < 3
                    ) {
                alert("Enter modem information");
                return false;
            }
        } else if ($("input[name=\"options[modem]\"]:checked").val() == "inventory") { // if inventory selected and has no modem
            if ($("select[name=\"options[modem_id]\"] option:selected").val() == null) {
                alert("You have no modems in your inventory");
                return false;
            }
        }

        //If customer is currently a cable subscriber, he has to enter his provider name and cancellation date.
        if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "yes") {
            if (($("select[name=\"options[current_cable_provider]\"]").val().length < 3 && $("input[name=\"options[subscriber_other]\"]").val().length < 3)
                    || $("input[name=\"options[cancellation_date]\"]").val().length < 3
                    ) {
                alert("Enter current provider's name and cancellation date");
                return false;
            }
        }

        //If customer is not a cable subscriber, he has to pick dates and times for installation
        if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "no") {
            if ($("input[name=\"options[installation_date_1]\"]").val().length < 3
                    || $("input[name=\"options[installation_date_2]\"]").val().length < 3
                    || $("input[name=\"options[installation_date_3]\"]").val().length < 3
                    || $("input[name=\"options[installation_time_1]\"]:checked").val().length < 3
                    || $("input[name=\"options[installation_time_2]\"]:checked").val().length < 3
                    || $("input[name=\"options[installation_time_3]\"]:checked").val().length < 3
                    ) {
                alert("Enter three dates and times for installation");
                return false;
            }
        }
        if ($("input[name=\"options[you_have_phone_number]\"]:checked").val() == "yes"
                && $("input[name=\"options[current_phone_number]\"]").val() == "") {
            alert("Enter your current phone number");
            return false;
        }
        return true;

}

$('#updateForm').submit(function( event ) {
  event.preventDefault();
  if(validateChooseProduct())
  {
    $('#modal2').modal('show');
    $.post( "<?=$api_url?>update_order_api.php", $( "#updateForm" ).serialize() , function (result) {
    debugger;
      $('#modal2').modal('hide');
      if(result.updated)
      {
        alert("Order updated successfully");
        window.location.replace("<?=$site_url?>/temp/orders.php")
      }
      else {
        alert(result.error);
      }
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
      $('#modal2').modal('hide');
      debugger;
       console.log(errorThrown); });;
  }

})

$("select[name=\"reseller\"]").change(function(){
  $('#modal2').modal('show');
  $.getJSON("<?=$api_url?>get_modem_by_reseller_id.php?reseller_id="+$(this).val(), function (result) {
    $.each(result, function (i, modems) {
      $('select[name=\"options[modem_id]\"]').empty()
      $.each(modems, function (i, modem) {
      //console.log(modem,modem["modem_id"],modem.modem_id,modem[0]["modem_id"]);
        $('select[name=\"options[modem_id]\"]').append($('<option>', {
            value: modem["modem_id"],
            text: modem["mac_address"] + "[" + modem["type"] + " | " + modem["serial_number"] + "]"
        }));
      });
    });
    //setTimeout(function() {
          $('#modal2').modal('hide');
        //},5000);

  }).fail(function(jqXHR, textStatus, errorThrown) {
    $('#modal2').modal('hide');

     console.log(errorThrown); });
});
</script>
<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
           <h4 class="modal-title">Please Wait</h4>
         </div>
         <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
           <div class="loader"></div>

         </div>
       </div>
     </div>
   </div>

<?php
include_once "../footer.php";
