<?php
include_once $_SERVER['DOCUMENT_ROOT']."/kutdser/mikrotik/db_credentials.php";
include "../tools/DBTools.php";
$dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
$ip_address = get_client_ip();


$query="SELECT `customer_id` FROM `customers` WHERE `ip_address`=?";
$stmt1 = $dbTools->getConnection()->prepare($query);

$param_value=$ip_address;
$stmt1->bind_param('s',
                  $param_value
                  ); // 's' specifies the variable type => 'string'


$stmt1->execute();

$customer_result = $stmt1->get_result();
$customer_row = $dbTools->fetch_assoc($customer_result);
$customer_id=0;
if($customer_row){
  $customer_id=$customer_row["customer_id"];
}
else {
  $customer_id=0;
}
?>
