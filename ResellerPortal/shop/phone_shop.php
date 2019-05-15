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
<script type="text/javascript" src="<?= $site_url ?>/js/phone_shop.js"></script>
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
<form class="" action="phone_checkout.php" method="post">
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
                        <a href=''  class="product-phone"><img src="<?= $site_url ?>/img/phone-icon.png" class="img-thumbnail" style="width:150px"/></a>
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
                <div class="phone">
                    <div class="row" style="width:100% !important;">
                        <div class="col-sm-12" >
                            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
                            <div class="panel panel-success">
                                <div class="panel-heading">Phone</div>
                                <div class="panel-body">
                                    <select name="product" class="form-control">
                                      <?php foreach ($products_rows as $product):
                                          if ($product['category']==="phone"){
                                            $price=$product['price'];
                                            $title=$product['title'];

                                            if($has_discount && isset($product['discount']) && (int)$product['discount']>0)
                                            {

                                              $price=(float)$product['price']-((float)$product['price']*(((float)$product['discount']/100)));
                                              $price=round($price,2);
                                              $discount_duration=$product['discount_duration'];
                                              $discount_duration=str_replace("_"," ",$discount_duration);
                                              $discount_duration= ucfirst($discount_duration);
                                              $title=$product['title']." (with discount ".$product['discount']."% for ".$discount_duration.")";

                                            }

                                            ?>
                                        <option real_price='<?=$product['price']?>' data_title='<?= $product['title']?>' price='<?= $price?>' value='<?= $product['product_id']?>' type="<?= $product['subscription_type']?>"> <?= $title." (".$price."$)"?></option>
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
                            <p class="rounded form-row form-row-wide custom_phone-adapter  ">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Phone Adapter</div>
                                <div class="panel-body">
                                    <label class="radio-inline">
                                        <input type="radio" class="input-text plan custom-options custom_field" data-price="" name="options[adapter]" value="my_own" />I have my own Phone Adapter<br/>
                                    </label><br/>
                                    <label class="radio-inline">
                                        <input type="radio" checked class="input-text plan custom-options custom_field" data-price="" name="options[adapter]" value="buy_Cisco_SPA112"   />Buy Cisco SPA112 2-Port Phone Adapter ($59.90)<br/>
                                        <?= $free_adapter?"<span style='color:green;' class='discount_offer'>you have limited offer free Adapter (CISCO ATA) </span>":""?><br/>
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
                        <li class="list-group-item">Adapter cost <span class="badge adapter-cost"></span></li>
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
