<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if(isset($_POST["order_expiration_notify_id"]))
{
	include_once "dbconfig.php";
	$query = "UPDATE `order_expiration_notify`
	 SET `seen`='yes'
	 WHERE order_expiration_notify_id=".$_POST["order_expiration_notify_id"];
	$order_expiration = $dbTools->query($query);

	if ($order_expiration) {
			echo "{\"inserted\" :true,\"error\" :\"null\"}";
	} else {
		//print_r($dbTools->getConnection());
			echo "{\"inserted\" :\"false\",\"error\" :\"".$dbTools->getConnection()->error."\"}";
	}
}
else{
	include_once "dbconfig.php";

	$fields = array(
	    "order_id" => "order_id",
			"order_expiration_notify_id"=>"order_expiration_notify_id",
	    "expiration_date" => "expiration_date",

	    "product_title" => "product_title",
	    "product_category" => "product_category",
	    "product_subscription_type" => "product_subscription_type",
	    "displayed_order_id" => "order_id",

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



	$orders = $dbTools->get_orders_expire("SELECT order_expiration_notify_id,
		`order_expiration_notify`.order_id,`order_expiration_notify`.expiration_date,
		`orders`.reseller_id,`orders`.customer_id,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name'
		,orders.product_title,orders.product_category,orders.product_subscription_type

		FROM `order_expiration_notify`

		inner JOIN `orders` on `order_expiration_notify`.`order_id`= `orders`.`order_id`
		inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
		INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
		WHERE seen='no'

		ORDER BY `order_expiration_notify`.`expiration_date` ASC"
	        , $fields
	        , $child,$childFields
	        , $child2,$child2Fields
	        , null,null);
	//or (month(orders.creation_date)+1=".$month." and day(orders.creation_date)>1)
	$json = json_encode($orders);
	echo "{\"orders\" :" ,$json , ",\"error\" :null}";


}

?>
