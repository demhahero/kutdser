<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if( 
	(isset($_GET['month']) && ctype_digit($_GET['month']) && ((int)$_GET['month'] >=1 && (int)$_GET['month'] <=12 )) 
	&& 
	(isset($_GET['year']) && ctype_digit($_GET['year']))
	&& 
	(isset($_GET['reseller_id']) && ctype_digit($_GET['reseller_id']))
	){
	
include_once "dbconfig.php";
	
$fields = array(
    "order_id" => "order_id",
    "creation_date" => "creation_date",
    "status" => "status",
    "modem_mac_address" => "modem_mac_address",
    "product_title" => "product_title",
    "product_category" => "product_category",
    "product_subscription_type" => "product_subscription_type",
    "displayed_order_id" => "order_id",
	
	"product_price" => "product_price",
	"additional_service_price" => "additional_service_price",
	"setup_price" => "setup_price",
	"modem_price" => "modem_price",
	"router_price" => "router_price",
	"remaining_days_price" => "remaining_days_price",
	"total_price" => "total_price",
	"qst_tax" => "qst_tax",
	"gst_tax" => "gst_tax",
    );
$childFields=array(
    "customer_id" => "customer_id",
    "full_name" => "customer_name",
);
$child2Fields=array(
    "customer_id" => "reseller_id",
    "full_name" => "reseller_name",
);


$child="customer";
$child2="reseller";
$child3="product";

$reseller_id=$_GET['reseller_id'];
$year=$_GET['year'];
$month=$_GET['month'];

$orders = $dbTools->order_query_api("SELECT 
	`orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name',orders.product_title,orders.product_category,orders.product_subscription_type,`orders`.`product_id`, 
	`order_options`.`modem_mac_address` 
	,`order_options`.`product_price`,`order_options`.`additional_service_price`,`order_options`.`setup_price`,`order_options`.`modem_price`
	,`order_options`.`router_price`,`order_options`.`remaining_days_price`,`order_options`.`total_price`,`order_options`.`qst_tax`,`order_options`.`gst_tax`
	
	FROM `orders` 
	inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
	inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
	INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` 
	WHERE orders.reseller_id=".$reseller_id." and (year(orders.creation_date)=".$year." and month(orders.creation_date)=".$month.")
	 
	ORDER BY `orders`.`creation_date` ASC"
        , $fields
        , $child,$childFields
        , $child2,$child2Fields
        , null,null);
//or (month(orders.creation_date)+1=".$month." and day(orders.creation_date)>1)
$json = json_encode($orders);
echo "{\"orders\" :" ,$json , ",\"error\" :null}";
}
else{
	echo "{\"orders\" :null,\"error\" :\"invalid params\"}";
}

?>