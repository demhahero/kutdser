<?php

class GlobalOnePaymentXMLTools {

    private $gateway;
    private $terminalId;
    private $currency;
    private $secret;
    private $testAccount;
    private $adminEmail;
    private $adminPhone;

    function __construct() {
        # These values are used to identify and validate the account that you are using. They are mandatory.
        $this->gateway = 'globalone';   # This is the WorldNet payments gateway that you should use, assigned to the site by WorldNet.
        $this->terminalId = '9530001';  # This is the Terminal ID assigned to the site by WorldNet.
        $this->currency = 'CAD';   # This is the 3 digit ISO currency code for the above Terminal ID.
        $this->secret = 'Ali@1982';   # This shared secret is used when generating the hash validation strings.
        # It must be set exactly as it is in the WorldNet Self Care system.
        $this->testAccount = false;

        # These are used only in the case where the response hash is incorrect, which should
        # never happen in the live environment unless someone is attempting fraud.
        $this->adminEmail = 'info@amprotelecom.com';
        $this->adminPhone = '5147438684';
    }

    function payment($cardNumber, $cardType, $cardExpiry, $cardHolderName, $cvv, $orderId, $amount) {

        require_once('gateway_tps_xml.php');

        # These values are specific to the cardholder.
        //$cardNumber = '5526123000333124';  # This is the full PAN (card number) of the credit card OR the SecureCard Card Reference if using a SecureCard. It must be digits only (i.e. no spaces or other characters).
        //$cardType = 'MasterCard';   # See our Integrator Guide for a list of valid Card Type parameters
        //$cardExpiry = '1021';  # (if not using SecureCard) The 4 digit expiry date (MMYY).
        //$cardHolderName = 'Ali Al-Saffar';  # (if not using SecureCard) The full cardholders name, as it is displayed on the credit card.
        //$cvv = '580';   # (optional) 3 digit (4 for AMEX cards) security digit on the back of the card.
        $issueNo = '';   # (optional) Issue number for Switch and Solo cards.
        $email = '';   # (optional) If this is sent then WorldNet will send a receipt to this e-mail address.
        $mobileNumber = "";  # (optional) Cardholders mobile phone number for sending of a receipt. Digits only, Include international prefix.
        # These values are specific to the transaction.
        //$orderId = 'testSHOP005';   # This should be unique per transaction (12 character max).
        //$amount = '0.01';   # This should include the decimal point.
        $isMailOrder = false;  # If true the transaction will be processed as a Mail Order transaction. This is only for use with Mail Order enabled Terminal IDs.
        # These fields are for AVS (Address Verification Check). This is only appropriate in the UK and the US.
        $address1 = '';   # (optional) This is the first line of the cardholders address.
        $address2 = '';   # (optional) This is the second line of the cardholders address.
        $postcode = '';   # (optional) This is the cardholders post code.
        $country = '';   # (optional) This is the cardholders country name.
        $phone = '';   # (optional) This is the cardholders home phone number.
        # eDCC fields. Populate these if you have retreived a rate for the transaction, offered it to the cardholder and they have accepted that rate.
        $cardCurrency = '';  # (optional) This is the three character ISO currency code returned in the rate request.
        $cardAmount = '';  # (optional) This is the foreign currency transaction amount returned in the rate request.
        $conversionRate = '';  # (optional) This is the currency conversion rate returned in the rate request.
        # 3D Secure reference. Only include if you have verified 3D Secure throuugh the WorldNet MPI and received an MPIREF back.
        $mpiref = '';   # This should be blank unless instructed otherwise by WorldNet.
        $deviceId = '';   # This should be blank unless instructed otherwise by WorldNet.

        $autoready = '';  # (optional) (Y/N) Whether or not this transaction should be marked with a status of "ready" as apposed to "pending".
        $multicur = false;  # This should be false unless instructed otherwise by WorldNet.

        $description = '';  # (optional) This can is a description for the transaction that will be available in the merchant notification e-mail and in the Self Care system.
        $autoReady = '';  # (optional) Y or N. Automatically set the transaction to a status of Ready in the batch. If not present the terminal default will be used.
        # Set up the authorisation object
        $auth = new XmlAuthRequest($this->terminalId, $orderId, $this->currency, $amount, $cardNumber, $cardType);
        if ($cardType != "SECURECARD")
            $auth->SetNonSecureCardCardInfo($cardExpiry, $cardHolderName);
        if ($cvv != "")
            $auth->SetCvv($cvv);
        if ($cardCurrency != "" && $cardAmount != "" && $conversionRate != "")
            $auth->SetForeignCurrencyInformation($cardCurrency, $cardAmount, $conversionRate);
        if ($email != "")
            $auth->SetEmail($email);
        if ($mobileNumber != "")
            $auth->SetMobileNumber($mobileNumber);
        if ($description != "")
            $auth->SetDescription($description);

        if ($issueNo != "")
            $auth->SetIssueNo($issueNo);
        if ($address1 != "" && $address2 != "" && $postcode != "")
            $auth->SetAvs($address1, $address2, $postcode);
        if ($country != "")
            $auth->SetCountry($country);
        if ($phone != "")
            $auth->SetPhone($phone);

        if ($mpiref != "")
            $auth->SetMpiRef($mpiref);
        if ($deviceId != "")
            $auth->SetDeviceId($deviceId);

        if ($multicur)
            $auth->SetMultiCur();
        if ($autoready)
            $auth->SetAutoReady($autoready);
        if ($isMailOrder)
            $auth->SetMotoTrans();

        # Perform the online authorisation and read in the result
        $response = $auth->ProcessRequestToGateway($this->secret, $this->testAccount, $this->gateway);

        //print_r($response);

        $expectedResponseHash = md5($this->terminalId . $response->UniqueRef() . ($multicur == true ? $this->currency : '') . $amount . $response->DateTime() . $response->ResponseCode() . $response->ResponseText() . $this->secret);


        if ($response->IsError())
            return 'AN ERROR OCCURED! You transaction was not processed. Error details: ' . $response->ErrorString();
        elseif ($response->ResponseText() == "APPROVAL") { //$expectedResponseHash == $response->Hash()
            switch ($response->ResponseCode()) {
                case "A" : # -- If using local database, update order as Authorised.
                    //echo 'Payment Processed successfully. Thanks you for your order.';
                    $uniqueRef = $response->UniqueRef();
                    $responseText = $response->ResponseText();
                    $approvalCode = $response->ApprovalCode();
                    $avsResponse = $response->AvsResponse();
                    $cvvResponse = $response->CvvResponse();
                    return true;
                    break;
                case "R" :
                case "D" :
                case "C" :
                case "S" :

                default : # -- If using local database, update order as declined/failed --
                    return 'PAYMENT DECLINED! Please try again with another card. Bank response: ' . $response->ResponseText();
            }
        } else {
            $uniqueReference = $response->UniqueRef();
            return 'PAYMENT FAILED: INVALID RESPONSE HASH. Please contact <a href="mailto:' . $this->adminEmail . '">' . $this->adminEmail . '</a> or call ' . $this->adminPhone . ' to clarify if you will get charged for this order.';
            if (isset($uniqueReference))
                return 'Please quote WorldNet Terminal ID: ' . $this->terminalId . ', and Unique Reference: ' . $uniqueReference . ' when mailing or calling.';
        }
    }

    function secureCardRegister($secureCardMerchantRef, $cardNumber, $cardType, $cardExpiry, $cardHolderName, $cvv) {
        require_once('gateway_tps_xml.php');
        # These values are specific to the cardholder.
        $dontCheckSecurity = '';  # (optional) "Y" if you do not want the CVV to be validated online.
        $issueNo = '';   # (optional) Issue number for Switch and Solo cards.
        # Set up the SecureCard addition object
        $securereg = new XmlSecureCardRegRequest($secureCardMerchantRef, $this->terminalId, $cardNumber, $cardExpiry, $cardType, $cardHolderName);

        if ($dontCheckSecurity != "")
            $securereg->SetDontCheckSecurity($dontCheckSecurity);
        if ($cvv != "")
            $securereg->SetCvv($cvv);
        if ($issueNo != "")
            $securereg->SetIssueNo($issueNo);

        $response = $securereg->ProcessRequestToGateway($this->secret, $this->testAccount, $this->gateway);

        unset($secureCardCardRef);
        if ($response->IsError()) {

            return 'AN ERROR OCCURED, Card details not registered. Error details: ' . $response->ErrorString();
        } else {
            $merchantRef = $response->MerchantReference();
            $expectedResponseHash = md5($this->terminalId . $response->MerchantReference() . $response->CardReference() . $response->DateTime() . $this->secret);
            if ($expectedResponseHash != $response->Hash()) {
                return 'SECURECARD REGISTRATION FAILED: INVALID RESPONSE HASH. Please contact <a href="mailto:' . $this->adminEmail . '">' . $this->adminEmail . '</a> or call ' . $this->adminPhone . ' to clarify if your card details were stored.';
                if (isset($merchantRef)) {
                    return 'Please quote WorldNet Terminal ID: ' . $this->terminalId . ', and SecureCard Merchant Reference: ' . $response->MerchantReference() . ' when mailling or calling.';
                }
            } else {
                return true;
            }
        }
    }

    function subscriptionRegister($subscriptionMerchantRef, $secureCardMerchantRef, $subscriptionStartDate, $recurringAmount, $initialAmount, $periodType) {
        require_once('gateway_tps_xml.php');

        # These are all optiona fields
        $endDate = '';    # (optional) set an end date for the subscription.  Format: DD-MM-YYYY.
        $eDCCDecision = '';   # (optional) if eDCC was offered and accepted, you should set this to 'Y'.
        $storedSubscriptionMerchantRef = $subscriptionMerchantRef;
        $type = "AUTOMATIC";
        $onUpdate = "UPDATE";
        $onDelete = "CANCEL";
        $currency = "CAD";
        $length = "0";
        $description = $subscriptionMerchantRef;
        $name = $subscriptionMerchantRef;
        # Set up the stored subscription addition object
        $subreg = new XmlSubscriptionRegRequest($subscriptionMerchantRef, $this->terminalId, $storedSubscriptionMerchantRef, $secureCardMerchantRef, $subscriptionStartDate);
        if ($name != "" || $description != "" || $periodType != "" || $length != "" || $type != "" || $onUpdate != "" || $onDelete != "")
            $subreg->SetNewStoredSubscriptionValues($name, $description, $periodType, $length, $currency, $recurringAmount, $initialAmount, $type, $onUpdate, $onDelete);
        else if ($recurringAmount != "" || $initialAmount != "")
            $subreg->SetSubscriptionAmounts($recurringAmount, $initialAmount);
        if ($endDate != "")
            $subreg->SetEndDate($endDate);
        if ($eDCCDecision != "")
            $subreg->EDCCDecision($eDCCDecision);

        $response = $subreg->ProcessRequestToGateway($this->secret, $this->testAccount, $this->gateway);

        if ($response->IsError())
            return 'AN ERROR OCCURED, Subscription not created. Error details: ' . $response->ErrorString();
        else {
            $expectedResponseHash = md5($this->terminalId . $response->MerchantReference() . $response->DateTime() . $this->secret);
            $merchantReference = $response->MerchantReference();
            if ($expectedResponseHash != $response->Hash()) {
                return 'SUBSCRIPTION REGISTRATION FAILED: INVALID RESPONSE HASH. Please contact <a href="mailto:' . $this->adminEmail . '">' . $this->adminEmail . '</a> or call ' . $this->adminPhone . ' to clarify if your card will be billed.';
                if (isset($merchantReference))
                    return 'Please quote WorldNet Terminal ID: ' . $this->terminalId . ', and Subscription Merchant Reference: ' . $response->MerchantReference() . ' when mailling or calling.';
            } else
                return true;
        }
    }

    function updateSubscription($subscriptionMerchantRef, $storedSubscriptionMerchantRef, $secureCardMerchantRef, $recurringAmount) {
        require_once('gateway_tps_xml.php');

        # Set up the stored subscription update object
        $subupd = new XmlSubscriptionUpdRequest($subscriptionMerchantRef, $this->terminalId, $secureCardMerchantRef);
        if ($name != "")
            $subupd->SetSubName($name);
        if ($description != "")
            $subupd->SetDescription($description);
        if ($periodType != "")
            $subupd->SetPeriodType($periodType);
        if ($length != "")
            $subupd->SetLength($length);
        if ($recurringAmount != "")
            $subupd->SetRecurringAmount($recurringAmount);
        if ($type != "")
            $subupd->SetSubType($type);
        if ($startDate != "")
            $subupd->SetStartDate($startDate);
        if ($endDate != "")
            $subupd->SetEndDate($endDate);
        if ($eDCCDecision != "")
            $subupd->EDCCDecision($eDCCDecision);

        $response = $subupd->ProcessRequestToGateway($this->secret, $this->testAccount, $this->gateway);

        if ($response->IsError()) {

            echo 'AN ERROR OCCURED, Subscription not updated. Error details: ' . $response->ErrorString();
        } else {
            $expectedResponseHash = md5($this->terminalId . $response->MerchantReference() . $response->DateTime() . $this->secret);
            if ($expectedResponseHash != $response->Hash()) {
                echo 'SUBSCRIPTION UPDATE FAILED: INVALID RESPONSE HASH. Please contact <a href="mailto:' . $this->adminEmail . '">' . $this->adminEmail . '</a> or call ' . $this->adminPhone . ' to clarify if your card will be billed.';
                $merchantRef = $response->MerchantReference();
                if (isset($merchantRef)) {
                    echo 'Please quote WorldNet Terminal ID: ' . $this->terminalId . ', and Subscription Merchant Reference: ' . $response->MerchantReference() . ' when mailling or calling.';
                }
            } else
                echo true;
        }
    }

}
