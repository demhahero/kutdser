<?php

# These values are used to identify and validate the account that you are using. They are mandatory.
$gateway = 'GlobalOnePay';   # This is the WorldNet payments gateway that you should use, assigned to the site by WorldNet.
$terminalId = '9530001';   # This is the Terminal ID assigned to the site by WorldNet.
$currency = 'CAD';    # This is the 3 digit ISO currency code for the above Terminal ID.
$secret = 'Ali@1982';    # This shared secret is used when generating the hash validation strings. 
# It must be set exactly as it is in the WorldNet Self Care system.
$testAccount = false;

# These are used only in the case where the response hash is incorrect, which should
# never happen in the live environment unless someone is attempting fraud.
$adminEmail = 'info@amprotelecom.com';
$adminPhone = '5147438684';
?>

