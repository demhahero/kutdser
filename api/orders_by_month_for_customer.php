<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if( 
	(isset($_GET['month']) && ctype_digit($_GET['month']) && ((int)$_GET['month'] >=1 && (int)$_GET['month'] <=12 )) 
	&& 
	(isset($_GET['year']) && ctype_digit($_GET['year']))
	&& 
	(isset($_GET['customer_id']) && ctype_digit($_GET['customer_id']))
	){
	
include_once "dbconfig.php";
	

$customer_id=$_GET['customer_id'];
$year=$_GET['year'];
$month=$_GET['month'];

$orders = $dbTools->order_requests_query_api($customer_id,$year,$month);

$json = json_encode($orders);
echo "{\"orders\" :" ,$json , ",\"error\" :null}";
}
else{
	echo "{\"orders\" :null,\"error\" :\"invalid params\"}";
}

?>