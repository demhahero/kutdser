<?php

require('gateway_tps_xml.php');

# These values are used to identify and validate the account that you are using. They are mandatory.
$gateway = 'GlobalOnePay';   # This is the WorldNet payments gateway that you should use, assigned to the site by WorldNet. (WORLDNET/CASHFLOWS)
$terminalId = '9530001';  # This is the Terminal ID assigned to the site by WorldNet.
$currency = 'CAD';   # This is the 3 digit ISO currency code for the above Terminal ID.
$secret = 'Ali@1982';   # This shared secret is used when generating the hash validation strings. 
# It must be set exactly as it is in the WorldNet SelfCare system.
$testAccount = false;
$testMode = false;
# These are used only in the case where the response hash is incorrect, which should
# never happen in the live environment unless someone is attempting fraud.
$adminEmail = 'info@amprotelecom.com';
$adminPhone = '5147438684';

# These values are specific to the cardholder.
$subscriptionMerchantRef = 'test-1';  # Unique merchant identifier for the subscription. Length is limited to 48 chars.
$storedSubscriptionMerchantRef = 'test1'; # Merchant reference for the Stored Subscription under which this Subscription is to be created.
$secureCardMerchantRef = 'test-1';  # Merchant reference for the SecureCard entry that you want to use to set up the subscription.
$subscriptionStartDate = '10-10-2018';  # Date on which the subscription should start (setup payment is processed immediately, and does not obey this). Format: DD-MM-YYYY.
# These are all optiona fields
$endDate = '';    # (optional) set an end date for the subscription.  Format: DD-MM-YYYY.
$eDCCDecision = '';   # (optional) if eDCC was offered and accepted, you should set this to 'Y'.

$recurringAmount = '';   #
$initialAmount = '';   #
$periodType = '';
# Set up the stored subscription addition object
$subreg = new XmlSubscriptionRegRequest($subscriptionMerchantRef, $terminalId, $storedSubscriptionMerchantRef, $secureCardMerchantRef, $subscriptionStartDate);
if ($name != "" || $description != "" || $periodType != "" || $length != "" || $type != "" || $onUpdate != "" || $onDelete != "")
    $subreg->SetNewStoredSubscriptionValues($name, $description, $periodType, $length, $currency, $recurringAmount, $initialAmount, $type, $onUpdate, $onDelete);
else if ($recurringAmount != "" || $initialAmount != "")
    $subreg->SetSubscriptionAmounts($recurringAmount, $initialAmount);
if ($endDate != "")
    $subreg->SetEndDate($endDate);
if ($eDCCDecision != "")
    $subreg->EDCCDecision($eDCCDecision);

$response = $subreg->ProcessRequestToGateway($secret, $testMode, $gateway);

if ($response->IsError())
    echo 'AN ERROR OCCURED, Subscription not created. Error details: ' . $response->ErrorString();
else {
    $expectedResponseHash = md5($terminalId . $response->MerchantReference() . $response->DateTime() . $secret);
    $merchantReference = $response->MerchantReference();
    if ($expectedResponseHash != $response->Hash()) {
        echo 'SUBSCRIPTION REGISTRATION FAILED: INVALID RESPONSE HASH. Please contact <a href="mailto:' . $adminEmail . '">' . $adminEmail . '</a> or call ' . $adminPhone . ' to clarify if your card will be billed.';
        if (isset($merchantReference))
            echo 'Please quote WorldNet Terminal ID: ' . $terminalId . ', and Subscription Merchant Reference: ' . $response->MerchantReference() . ' when mailling or calling.';
    } else
        echo "Subscription successfully setup and setup payment processed succesfully.";
}