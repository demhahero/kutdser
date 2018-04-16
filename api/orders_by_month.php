<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if(isset($_GET['month']) && ctype_digit($_GET['month']) && ((int)$_GET['month'] >=1 && (int)$_GET['month'] <=12 )){
	
include_once "dbconfig.php";
	
$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
    "modem_mac_address" => "modem_mac_address",
    );
$childFields=array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
);
$child2Fields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);
$child3Fields=array(
    "product_id" => "product_id",
    "title" => "title",
);

$child="customer";
$child2="reseller";
$child3="product";

$month=$_GET['month'];

$orders = $dbTools->order_query_api("SELECT 
	`orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name', `products`.`title`,`orders`.`product_id`, `order_options`.`modem_mac_address` 
	FROM `orders` 
	Inner JOIN `products` on `products`.`product_id` = `orders`.`product_id` 
	inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
	inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
	INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` 
	WHERE month(orders.creation_date)=".$month." or (month(orders.creation_date)+1=".$month." and day(orders.creation_date)>1) ORDER BY `orders`.`creation_date` ASC"
        , $fields
        , $child,$childFields
        , $child2,$child2Fields
        , $child3,$child3Fields);

$json = json_encode($orders);
echo "{\"orders\" :" ,$json , ",\"error\" :null}";
}
else{
	echo "{\"orders\" :null,\"error\" :\"month value is not correct\"}";
}

?>