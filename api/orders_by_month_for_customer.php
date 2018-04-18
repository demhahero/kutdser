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

$child3Fields=array(
    "action" => "action",
    "action_value" => "action_value",
	"action_on_date" => "action_on_date",
	"creation_date" => "request_creation_date",
	"product_price" => "request_product_price",
	"verdict" => "verdict",
	"verdict_date" => "verdict_date"
);


$child="customer";
$child2="reseller";
$child3="request";

$customer_id=$_GET['customer_id'];
$year=$_GET['year'];
$month=$_GET['month'];

$orders = $dbTools->order_month_query_api("SELECT 
	`orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,`orders`.customer_id,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name',orders.product_title,orders.product_category,orders.product_subscription_type,`orders`.`product_id`, 
	`order_options`.`modem_mac_address` 
	,`order_options`.`product_price`,`order_options`.`additional_service_price`,`order_options`.`setup_price`,`order_options`.`modem_price`
	,`order_options`.`router_price`,`order_options`.`remaining_days_price`,`order_options`.`total_price`,`order_options`.`qst_tax`,`order_options`.`gst_tax`
	,requests.action,requests.action_value,requests.action_on_date,requests.creation_date as 'request_creation_date',requests.product_price as 'request_product_price',
	IF((year(requests.creation_date)=".$year." and month(requests.creation_date)=".$month."), verdict, NULL) as verdict  
	
	,requests.verdict_date
	
	FROM `orders` 
	inner JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id` 
	inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id` 
	INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id` 
    LEFT JOIN requests on requests.order_id=orders.order_id
	WHERE orders.customer_id=".$customer_id." and (year(orders.creation_date)=".$year." and month(orders.creation_date)=".$month.") 
	 
	ORDER BY `orders`.`creation_date` ASC"
        , $fields
        , $child,$childFields
        , $child2,$child2Fields
        , $child3,$child3Fields);
//WHERE orders.customer_id=1302 and (year(orders.creation_date)=2018 and month(orders.creation_date)=3)
$json = json_encode($orders);
echo "{\"orders\" :" ,$json , ",\"error\" :null}";
}
else{
	echo "{\"orders\" :null,\"error\" :\"invalid params\"}";
}

?>