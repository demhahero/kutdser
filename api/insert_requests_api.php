<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function checkRequest($action_on_date,$order_id,$dbTools){
	
		$getOrder=$dbTools->query("SELECT actual_installation_date from order_options where order_id=".$order_id);
	
	
	while ($order = $dbTools->fetch_assoc($getOrder)) {
		$actual_installation_date=new DateTime($order["actual_installation_date"]);
		
		if($action_on_date < $actual_installation_date)
		{
			echo "{\"canInsert\" :false,\"error\" :\"error: action_on_date can not be earlier than actual_installation_date\"}";
			return false;
		}
		break;
	}
	
	$getCanceledRequest=$dbTools->query("SELECT request_id from requests where order_id=".$order_id." and action='cancel' and verdict='approve'");
	while ($request = $dbTools->fetch_assoc($getCanceledRequest)) {
		echo "{\"canInsert\" :false,\"error\" :\"error: ordere already canceled\"}";
			return false;
	}
	
	$getRequestOnMonth=$dbTools->query("SELECT request_id from requests 
	where order_id=".$order_id." and year(action_on_date)=".$action_on_date->format('Y')." and month(action_on_date) = ".$action_on_date->format('m')." and verdict='approve'");
	while ($request = $dbTools->fetch_assoc($getRequestOnMonth)) {
		echo "{\"canInsert\" :false,\"error\" :\"error: there is request regestered in this month for this order\"}";
			return false;
	}
	
	
	return true;
}
if(isset($_GET["order_id"]) && isset($_GET["action_on_date"])){
	include_once "dbconfig.php";
	
	$action_on_dateString=$_GET["action_on_date"];
	$action_on_date=new DateTime($action_on_dateString);
	$order_id=$_GET["order_id"];
	
	if(checkRequest($action_on_date,$order_id,$dbTools))
		echo "{\"canInsert\" :true,\"error\" :\"null\"}";
	
	
	
}
else if ( isset($_POST["order_id"])){
	
include_once "dbconfig.php";

	$action_on_dateString=$_POST["action_on_date"];
	$action_on_date=new DateTime($action_on_dateString);
	$order_id=$_POST["order_id"];
	
	if(!checkRequest($action_on_date,$order_id,$dbTools))
		exit();


$PostFields = array(
    "reseller_id"=>"",
	 "order_id"=>"",
	// "creation_date"=>"",
	 "action"=>"",
	 "action_value"=>"",
	 "admin_id"=>"",
	 "verdict"=>"",
	 "verdict_date"=>"",
	 "action_on_date"=>"",
	 //"product_price"=>"",
	 "note"=>"",
	 //"product_title"=>"",
	 //"product_category"=>"",
	 //"product_subscription_type"=>"",
    );
	
$InsertFieldValues = array(
    "reseller_id"=>"",
	 "order_id"=>"",
	 "creation_date"=>"",
	 "action"=>"",
	 "action_value"=>"",
	 "admin_id"=>"",
	 "verdict"=>"",
	 "verdict_date"=>"",
	 "action_on_date"=>"",
	 "product_price"=>"",
	 "note"=>"",
	 "product_title"=>"",
	 "product_category"=>"",
	 "product_subscription_type"=>"",
    );
	
	foreach ($PostFields as $key => $value)
    {
		if(isset($_POST[$key])){
			$InsertFieldValues[$key]=$_POST[$key];
			
		}
		else{
			echo "{\"inserted\" :false,\"error\" :\"error: not all values sent in POST\"}";
			exit();
		}
	}
	
	$product=$dbTools->query("SELECT products.* FROM `orders` INNER JOIN products on products.product_id=orders.product_id where orders.order_id=".$InsertFieldValues["order_id"]);
	
	while ($product_row = $dbTools->fetch_assoc($product)) {
		$InsertFieldValues["product_title"]=$product_row["title"];
		$InsertFieldValues["product_price"]=$product_row["price"];
		$InsertFieldValues["product_category"]=$product_row["category"];
		$InsertFieldValues["product_subscription_type"]=$product_row["subscription_type"];
		
	}
	$creation_date=new DateTime();
	$InsertFieldValues["creation_date"]=$creation_date->format('Y-m-d');
	 
	
	$columns="";
	$values="";
	foreach ($InsertFieldValues as $column => $value)
    {
		$columns.=$column.",";
		$values.="N'".$value."',";
	}
	$query="INSERT INTO `requests`(".substr($columns, 0, -1).") VALUES (".substr($values, 0, -1).")";
	
	$requests = $dbTools->query($query);
	

	$json = json_encode($requests);
	echo "{\"inserted\" :" ,$json , ",\"error\" :null}";
}
