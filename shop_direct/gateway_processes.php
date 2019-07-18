<?php
include 'GlobalOnePaymentXMLTools.php';

$mGlobalOnePaymentXMLTools = new GlobalOnePaymentXMLTools();

if ($_GET["do"] == "register") {
    echo $mGlobalOnePaymentXMLTools->secureCardRegister("CARD_" . $_POST["merchant_reference"], $_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"]);
} 
else if ($_GET["do"] == "payment") {
    echo $mGlobalOnePaymentXMLTools->payment($_POST["card_number"], $_POST["card_type"], $_POST["card_expiry"], $_POST["card_holders_name"], $_POST["card_cvv"], "P_" . $_POST["merchant_reference"], $_POST["amount"]);
} 
else if ($_GET["do"] == "subscription") {
    echo $mGlobalOnePaymentXMLTools->subscriptionRegister("SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["merchant_reference"], $_POST["subscription_start_date"], $_POST["recurring_amount"], $_POST["initial_amount"], $_POST["period_type"]);
} 
else if ($_GET["do"] == "subscriptionWithMerchantref") {
    echo $mGlobalOnePaymentXMLTools->subscriptionRegister("SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["existed_merchant_reference"], $_POST["subscription_start_date"], $_POST["recurring_amount"], $_POST["initial_amount"], $_POST["period_type"]);
} 
else if ($_GET["do"] == "updateSubscription") {
    echo $mGlobalOnePaymentXMLTools->updateSubscription("SS_" . $_POST["merchant_reference"], "SS_" . $_POST["merchant_reference"], "CARD_" . $_POST["merchant_reference"], $_POST["recurring_amount"]);
}
?>
