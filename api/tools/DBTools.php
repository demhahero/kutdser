<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "CustomerTools.php";
include "OrderTools.php";
include "ProductTools.php";
include "MerchantTools.php";
include "UpcomingCustomerTools.php";
include "AdminTools.php";
include "RequestTools.php";
include "ModemTools.php";
include "RouterTools.php";
/**
 * Description of DBTools
 *
 * @author breeder1
 */
class DBTools {

    private $db_host = "localhost";
    private $db_username = "root";
    private $db_password = "";
    private $db_name = 'router'; //database name
    private $conn_routers;
    private $query_result;

    public function __construct() {
        $this->conn_routers = new mysqli($this->db_host, $this->db_username, $this->db_password, $this->db_name);
        if ($this->conn_routers->connect_error) {
            die("Connection failed: " . $this->conn_routers->connect_error);
        }
    }

    public function query($queryString) {
        return $this->query_result = $this->conn_routers->query($queryString);
    }

    public function fetch_assoc($query_result) {
        return $query_result->fetch_assoc();
    }

    public function objCustomerTools($customer_id, $depth = 3, $path = null) {
        return new CustomerTools($customer_id, $this, $depth, $path);
    }

    public function objProductTools($product_id, $depth = 3, $path = null) {
        return new ProductTools($product_id, $this, $depth, $path);
    }

    public function objOrderTools($order_id, $depth = 3, $path = null) {
        return new OrderTools($order_id, $this, $depth, $path);
    }
    
    public function objModemTools($modem_id, $depth = 3, $path = null) {
        return new ModemTools($modem_id, $this, $depth, $path);
    }
    
    public function objRouterTools($router_id, $depth = 3, $path = null) {
        return new RouterTools($router_id, $this, $depth, $path);
    }
    
    public function objUpcomingCustomerTools($upcoming_customer_id=null, $depth = 3, $path = null) {
        return new UpcomingCustomerTools($upcoming_customer_id, $this, $depth, $path);
    }
    
    public function objAdminTools($admin_id, $depth = 3, $path = null) {
        return new AdminTools($admin_id, $this, $depth, $path);
    }
    
    public function objRequestTools($request_id = null, $depth = 3, $path = null) {
        return new RequestTools($request_id, $this, $depth, $path);
    }

    public function order_query($queryString, $depth = 3, $path = null) {
        $orders = array();
        $order_result = $this->query($queryString);
        while ($order_row = $this->fetch_assoc($order_result)) {
            $orders[] = new OrderTools($order_row["order_id"], $this, $depth, $path);
        }
        return $orders;
    }
    public function order_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null) {
        $orders = array();
        
        $this->query("SET CHARACTER SET utf8");
        
        $order_result = $this->query($queryString);
        while ($order_row = $this->fetch_assoc($order_result)) {
            $order = array();
            foreach ($fields as $key => $value)
            {
                if($key == "displayed_order_id")
                if ((int) $order_row["order_id"] > 10380)
                    $order_row[$value] = (((0x0000FFFF & (int) $order_row["order_id"]) << 16) + ((0xFFFF0000 & (int) $order_row["order_id"]) >> 16));

                $order[$key] = $order_row[$value];
            }
            if ($child != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($childFields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $order_row[$childValue];
                    
                }
                array_push($orderChildArray,$orderChild);
                $order[$child] = $orderChildArray;
            }
            if ($child2 != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $order_row[$childValue];
                    
                }
                array_push($orderChildArray,$orderChild);
                $order[$child2] = $orderChildArray;
            }
            if ($child3 != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($child3Fields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $this->conn_routers->real_escape_string($order_row[$childValue]);
                    
                }
                array_push($orderChildArray,$orderChild);
                $order[$child3] = $orderChildArray;
            }
            array_push($orders,$order);
        }
        return $orders;
    }
	public function orders_by_month($customer_id,$year,$month) {
		
        
        $this->query("SET CHARACTER SET utf8");
		
		$ordersResult = $this->query("SELECT * from orders inner join order_options on orders.order_id= order_options.order_id where 
		customer_id=".$customer_id);
	
		$orders=array();
		
		while ($order_row = $this->fetch_assoc($ordersResult)) {
			$monthsInfo=array();
			
			
			$orderChild = array();
			$orderChild["order_id"]=$order_row["order_id"];
			$orderChild["creation_date"] = $order_row["creation_date"];
			$orderChild["total_price"]=$order_row["total_price"];
			$orderChild["product_price"]=$order_row["product_price"];
			$orderChild["additional_service_price"]=$order_row["additional_service_price"];
			$orderChild["setup_price"]=$order_row["setup_price"];
			$orderChild["modem_price"]=$order_row["modem_price"];
		
			$orderChild["router_price"]=$order_row["router_price"];
			$orderChild["remaining_days_price"]=$order_row["remaining_days_price"];
			$orderChild["qst_tax"]=$order_row["qst_tax"];
			$orderChild["gst_tax"]=$order_row["gst_tax"];
			$orderChild["adapter_price"]=$order_row["adapter_price"];
			
			$orderChild["plan"]=$order_row["plan"];
			$orderChild["modem"]=$order_row["modem"];
			$orderChild["router"]=$order_row["router"];
			$orderChild["cable_subscriber"]=$order_row["cable_subscriber"];
			$orderChild["current_cable_provider"]=$order_row["current_cable_provider"];
			$orderChild["cancellation_date"]=$order_row["cancellation_date"];
			$orderChild["installation_date_1"]=$order_row["installation_date_1"];
			$orderChild["actual_installation_date"]=$order_row["actual_installation_date"];
		
			$orderChild["product_title"]=$order_row["product_title"];
			$orderChild["product_category"]=$order_row["product_category"];
			$orderChild["product_subscription_type"]=$order_row["product_subscription_type"];
			
			if($order_row["product_category"]==="phone"){
				$orderChild["start_active_date"]=$order_row["creation_date"];
			}
			else if($order_row["product_category"]==="internet"){
				if($order_row["cable_subscriber"]==="yes"){
					$orderChild["start_active_date"]=$order_row["cancellation_date"];
				}
				else {
					$orderChild["start_active_date"]=$order_row["installation_date_1"];
				}
			}
			$start_active_date = new DateTime($orderChild["start_active_date"]);
			if(((int)$start_active_date->format('d'))>1)
			{
				$recurring_date = new DateTime($start_active_date->format('y')."-".$start_active_date->format('m')."-01 00:00:00");
				$interval = new DateInterval('P2M');
				$recurring_date->add($interval);
				$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
			}
			else{
				$recurring_date=new DateTime($orderChild["start_active_date"]);
				$interval = new DateInterval('P1M');
				$recurring_date->add($interval);
				$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
			}
			
///////////////// get month info from order			
			$monthInfo=array();
			$monthInfo["total_price"]=$order_row["total_price"];
			$monthInfo["product_price"]=$order_row["product_price"];
			$monthInfo["additional_service_price"]=$order_row["additional_service_price"];
			$monthInfo["setup_price"]=$order_row["setup_price"];
			$monthInfo["modem_price"]=$order_row["modem_price"];
			$monthInfo["router_price"]=$order_row["router_price"];
			$monthInfo["plan"]=$order_row["plan"];
			$monthInfo["modem"]=$order_row["modem"];
			$monthInfo["router"]=$order_row["router"];
			$monthInfo["remaining_days_price"]=$order_row["remaining_days_price"];
			$monthInfo["qst_tax"]=$order_row["qst_tax"];
			$monthInfo["gst_tax"]=$order_row["gst_tax"];
			$monthInfo["adapter_price"]=$order_row["adapter_price"];
			$monthInfo["product_title"]=$order_row["product_title"];
			$monthInfo["days"]=$recurring_date->diff($start_active_date)->days;
/////////////////// end get month infor from order
			

////////////////// check if there is any request before the selected date, if yes get it's infor instead of order info
			$date = new DateTime($year."-".$month."-01 00:00:00");
			$monthDays= (int) $date->format( 't' );
			
			//if($requestResult->num_rows===0)
			//{
				$requestResult = $this->query("SELECT * from requests where 
				order_id=".$order_row["order_id"]." 
				and (year(action_on_date) <".$year."
				or (year(action_on_date) =".$year." and month(action_on_date) <".$month." ))
				and verdict = 'approve' order by action_on_date DESC LIMIT 1");
			//}
			$requests=array();
			$hasRequest=false;
			while ($request_row = $this->fetch_assoc($requestResult)) {
				$hasRequest=true;
				$requestChild = array();
				$requestChild["creation_date"] = $request_row["creation_date"];
				$requestChild["action_on_date"] = $request_row["action_on_date"];
				$requestChild["verdict_date"] = $request_row["verdict_date"];
				$requestChild["verdict"] = $request_row["verdict"];
				$requestChild["product_price"]=$request_row["product_price"];
				$requestChild["action"]=$request_row["action"];
			
				$requestChild["product_title"]=$request_row["product_title"];
				$requestChild["product_category"]=$request_row["product_category"];
				$requestChild["product_subscription_type"]=$request_row["product_subscription_type"];
				
				
////////////////// update month info 
				
				$monthInfo["product_price"]=$request_row["product_price"];
				$monthInfo["additional_service_price"]="0";
				$monthInfo["setup_price"]="0";
				
				if($monthInfo["router"]!=="rent" )
					$monthInfo["router_price"]="0";
				if($monthInfo["modem"]!=="rent" )
					$monthInfo["modem_price"]="0";
				$monthInfo["remaining_days_price"]="0";
				$monthInfo["qst_tax"]="0";
				$monthInfo["gst_tax"]="0";
				$monthInfo["adapter_price"]="0";
				$monthInfo["total_price"]=(float)$monthInfo["product_price"]+(float)$monthInfo["modem_price"]+(float)$monthInfo["router_price"];
				$monthInfo["product_title"]=$request_row["product_title"];
				$monthInfo["days"]=$monthDays;
////////////////// end update month info
				array_push($requests,$requestChild);
			}
			
			
///////////////// check if there is request in the same month as the requested date
			
			$requestResult = $this->query("SELECT * from requests where 	
			order_id=".$order_row["order_id"]." 
			and (year(action_on_date) =".$year." and month(action_on_date) =".$month." )
			and verdict = 'approve' order by action_on_date");
			while ($request_row = $this->fetch_assoc($requestResult)) {
				$requestChild = array();
				$requestChild["creation_date"] = $request_row["creation_date"];
				$requestChild["action_on_date"] = $request_row["action_on_date"];
				$requestChild["verdict_date"] = $request_row["verdict_date"];
				$requestChild["verdict"] = $request_row["verdict"];
				$requestChild["product_price"]=$request_row["product_price"];
				$requestChild["action"]=$request_row["action"];
			
				$requestChild["product_title"]=$request_row["product_title"];
				$requestChild["product_category"]=$request_row["product_category"];
				$requestChild["product_subscription_type"]=$request_row["product_subscription_type"];
				
				
				
////////////////// update month info 
				$this_action_on_date = new DateTime($request_row["action_on_date"]);
				$recurring_date = new DateTime($orderChild["recurring_date"]);
	/////////////////////////// check if this request is made after the 1st day in month or made before the first recurring_date

//// this condition take care of the folloing scenarios :
////// 1- request made 	is made after the 1st day in month and has previous request 
////// 2- request might happened in any day of the month but it happened before the first recurring_date 
////// so in both scenario we have to calculate and split the price in to two periods 
////// assuming only one request or order in month, and assuming if order made after the 1st day then the remaining days price in already count and product price is for full month
				
				
				if( ( (int)$this_action_on_date->format('d')>1 && $hasRequest)
					|| 
					(
						(int)$recurring_date->format('Y') > (int)$year 
						|| 
						((int)$recurring_date->format('Y') === (int)$year && (int)$recurring_date->format('m') > (int)$month)  
						
					) 
				)
				
				{
					$this_request_days=$monthDays-(int)$this_action_on_date->format('d');
					$previous_days=$monthDays-$this_request_days;
					
					$this_product_price= (((float)$request_row["product_price"])/$monthDays)*$this_request_days;
					$previous_product_price= (((float)$monthInfo["product_price"])/$monthDays)*$previous_days;
					
					$monthInfo["product_price_previous"]=$monthInfo["product_price"];
					
					$monthInfo["product_price"]=$previous_product_price;
					$monthInfo["product_price_2"]=$this_product_price;
					
					$monthInfo["days"]=$previous_days;
					$monthInfo["days_2"]=$this_request_days;
					
					
					$monthInfo["product_title_2"]=$request_row["product_title"];
					
					$monthInfo["additional_service_price"]="0";
					$monthInfo["setup_price"]="0";
				
					if($monthInfo["router"]!=="rent" )
						$monthInfo["router_price"]="0";
					if($monthInfo["modem"]!=="rent" )
						$monthInfo["modem_price"]="0";
					$monthInfo["remaining_days_price"]="0";
					$monthInfo["qst_tax"]="0";
					$monthInfo["gst_tax"]="0";
					$monthInfo["adapter_price"]="0";
					$monthInfo["total_price"]=(float)$monthInfo["product_price"]+(float)$monthInfo["product_price_2"]+(float)$monthInfo["modem_price"]+(float)$monthInfo["router_price"];
					
				}
				else{
					
					$monthInfo["product_price"]=$request_row["product_price"];
				
					$monthInfo["product_title"]=$request_row["product_title"];
					$monthInfo["days"]=$monthDays;
					
										$monthInfo["product_title_2"]=$request_row["product_title"];
					
					$monthInfo["additional_service_price"]="0";
					$monthInfo["setup_price"]="0";
				
					if($monthInfo["router"]!=="rent" )
						$monthInfo["router_price"]="0";
					if($monthInfo["modem"]!=="rent" )
						$monthInfo["modem_price"]="0";
					$monthInfo["remaining_days_price"]="0";
					$monthInfo["qst_tax"]="0";
					$monthInfo["gst_tax"]="0";
					$monthInfo["adapter_price"]="0";
					$monthInfo["total_price"]=(float)$monthInfo["product_price"]+(float)$monthInfo["modem_price"]+(float)$monthInfo["router_price"];
				}
				
				
				
				
////////////////// end update month info


				array_push($requests,$requestChild);
			}
			
			array_push($monthsInfo,$monthInfo);
			$orderChild["requests"]=$requests;
			$orderChild["monthInfo"]=$monthsInfo;
			
			array_push($orders,$orderChild);
		}
	return $orders;
	}
	public function order_requests_query_api($customer_id,$year,$month) {
        $orders = array();
        
        $this->query("SET CHARACTER SET utf8");
        
        
        $result=array();
$customers = $this->query("SELECT customers.customer_id,resellers.customer_id as 'reseller_id', resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name'
 from customers
 INNER JOIN `customers` resellers on resellers.`customer_id` = `customers`.`reseller_id` 
 where 	customers.customer_id=".$customer_id);
 
	while ($customer_row = $this->fetch_assoc($customers)) {
		$customer=array();
		$customer["customer_id"]=$customer_row["customer_id"];
		$customer["full_name"]=$customer_row["customer_name"];
		
		$result["customer"]=$customer;
		
		$reseller=array();
		$reseller["customer_id"]=$customer_row["reseller_id"];
		$reseller["full_name"]=$customer_row["reseller_name"];
		$result["reseller"]=$reseller;
		
		
		
	}
	
		$ordersResult = $this->query("SELECT * from orders inner join order_options on orders.order_id= order_options.order_id where 
	customer_id=".$customer_id."
	and (year(creation_date) =".$year." and month(creation_date) =".$month." )");
	
	$orders=array();
	
	while ($order_row = $this->fetch_assoc($ordersResult)) {
		//print_r($order_row);
		$order=array();
		$orders["order_id"]=$order_row["order_id"];
		$invoice = array();
		$orderChild = array();
		$orderChild["date"] = $order_row["creation_date"];
		$orderChild["total_price"]=$order_row["total_price"];
		$orderChild["product_price"]=$order_row["product_price"];
		$orderChild["additional_service_price"]=$order_row["additional_service_price"];
		$orderChild["setup_price"]=$order_row["setup_price"];
		$orderChild["modem_price"]=$order_row["modem_price"];
		
		$orderChild["router_price"]=$order_row["router_price"];
		$orderChild["remaining_days_price"]=$order_row["remaining_days_price"];
		$orderChild["qst_tax"]=$order_row["qst_tax"];
		$orderChild["gst_tax"]=$order_row["gst_tax"];
		$orderChild["adapter_price"]=$order_row["adapter_price"];
		
		$orderChild["product_title"]=$order_row["product_title"];
		$orderChild["product_category"]=$order_row["product_category"];
		$orderChild["product_subscription_type"]=$order_row["product_subscription_type"];
		
		$orderChild["action"]="order";
		array_push($invoice,$orderChild);
		
		////echo "same order month</br>";
		// check if there are any request also in the same month
		$requestResult = $this->query("SELECT * from requests where 
		order_id=".$order_row["order_id"]." 
		and (year(creation_date) =".$year." and month(creation_date) =".$month." )
		and verdict = 'approve' order by creation_date");
		
		
		while ($request_row = $this->fetch_assoc($requestResult)) {
			////echo "same request month</br>";
			$orderChild = array();
			$orderChild["date"] = $request_row["creation_date"];
			$orderChild["product_price"]=$request_row["product_price"];
			$orderChild["action"]=$request_row["action"];
			
			$orderChild["product_title"]=$request_row["product_title"];
			$orderChild["product_category"]=$request_row["product_category"];
			$orderChild["product_subscription_type"]=$request_row["product_subscription_type"];
			array_push($invoice,$orderChild);
		}
		//print_r($invoice);
		////echo sizeof($invoice)."</br>";
		
		if(sizeof($invoice)===0)
		{
			$productPrices = array();
			$orderChild = array();
				$orderChild["beginDate"] = "";
				$orderChild["endDate"]="";
				$orderChild["price"]="0";
				
				$orderChild["product_title"]="";
				$orderChild["product_category"]="";
				$orderChild["product_subscription_type"]="";
				
				array_push($productPrices,$orderChild);
			$order["invoic"]=$productPrices;
			//echo "inside1";
			//echo "zero </br>";
		}
		else if(sizeof($invoice)===1)
		{
			//echo "inside2";
			//echo $invoice[0]["product_price"];
			$beginDate = new DateTime($invoice[0]["date"]);
			$date = new DateTime($beginDate->format('y')."-".$beginDate->format('m')."-01 00:00:00");
			if((int)$beginDate->format('d')===1)
			{
				$interval = new DateInterval('P1M');
				$date->add($interval);
			}
			else
			{
				$interval = new DateInterval('P2M');
				$date->add($interval);
			}
			$productPrices = array();
			$orderChild = array();
				$orderChild["beginDate"] = $beginDate->format('Y-m-d');
				$orderChild["endDate"]=$date->format('Y-m-d');
				$orderChild["price"]=$invoice[0]["total_price"];
				$orderChild["total_price"]=$order_row["total_price"];
				$orderChild["product_price"]=$order_row["product_price"];
				$orderChild["additional_service_price"]=$order_row["additional_service_price"];
				$orderChild["setup_price"]=$order_row["setup_price"];
				$orderChild["modem_price"]=$order_row["modem_price"];
		
				$orderChild["router_price"]=$order_row["router_price"];
				$orderChild["remaining_days_price"]=$order_row["remaining_days_price"];
				$orderChild["qst_tax"]=$order_row["qst_tax"];
				$orderChild["gst_tax"]=$order_row["gst_tax"];
				$orderChild["adapter_price"]=$order_row["adapter_price"];
				
				$orderChild["product_title"]=$order_row["product_title"];
				$orderChild["product_category"]=$order_row["product_category"];
				$orderChild["product_subscription_type"]=$order_row["product_subscription_type"];
				array_push($productPrices,$orderChild);
			//print_r($productPrices);
			//array_push($result,$productPrices);
			$order["invoice"]=$productPrices;
			//echo "</br>";
		}
		else{
			//echo "inside3";
			$date = new DateTime($year."-".$month."-01 00:00:00");
			$monthDays= (int) $date->format( 't' );
			//echo "monthDays :".$monthDays."</br>";
			
			$lastdaydate = new DateTime($year."-".$month."-".$monthDays." 00:00:00");
			
			$orderChild = array();
			$orderChild["date"] = $lastdaydate->format('Y-m-d');
			$orderChild["product_price"]=0;
			$orderChild["action"]="end";
			array_push($invoice,$orderChild);
			
			
			$productPrices = array();
			
			for($i=1;$i<sizeof($invoice);$i++){
				
				$beginDate = new DateTime($invoice[$i-1]["date"]);
				$endDate = new DateTime($invoice[$i]["date"]);
				//echo "beginDate->format('t') :".$beginDate->format('d')."</br>";
				$periodInDays=(int)$endDate->format('d')-(int)$beginDate->format('d');
				if($i===1)
					$periodInDays++;
				//echo "periodInDays :".$periodInDays."</br>";
				$pricePerDay= (float)$invoice[$i-1]["product_price"]/$monthDays;
				//echo "pricePerDay :".$pricePerDay."</br>";
				$periodPrice=$periodInDays*$pricePerDay;
				//echo "periodPrice :".$periodPrice."</br>";
				if($invoice[$i-1]["action"]==="cancel"){
					$periodPrice=0;
				}
				$orderChild = array();
				$orderChild["beginDate"] = $invoice[$i-1]["date"];
				$orderChild["endDate"]=$invoice[$i]["date"];
				$orderChild["price"]=$periodPrice;
				if($i===1){
					$orderChild["additional_service_price"]=$order_row["additional_service_price"];
				$orderChild["setup_price"]=$order_row["setup_price"];
				$orderChild["modem_price"]=$order_row["modem_price"];
		
				$orderChild["router_price"]=$order_row["router_price"];
				$orderChild["remaining_days_price"]=$order_row["remaining_days_price"];
				$orderChild["qst_tax"]=$order_row["qst_tax"];
				$orderChild["gst_tax"]=$order_row["gst_tax"];
				$orderChild["adapter_price"]=$order_row["adapter_price"];
				}
				
				$orderChild["product_title"]=$invoice[$i-1]["product_title"];
				$orderChild["product_category"]=$invoice[$i-1]["product_category"];
				$orderChild["product_subscription_type"]=$invoice[$i-1]["product_subscription_type"];
			
				array_push($productPrices,$orderChild);
			}
			/*
			$orderChild = array();
			$date = new DateTime($invoice[sizeof($invoice)-1]["date"]);
			$interval = new DateInterval('P1M');

			$date->add($interval);
			$endDate=$date->format('Y-m-d');
			$periodPrice=$invoice[sizeof($invoice)-1]["product_price"];
				if($invoice[sizeof($invoice)-1]["action"]==="cancel"){
					$periodPrice=0;
					$endDate="0000-00-00 00:00:00";
				}
				
				$orderChild["beginDate"] = $invoice[sizeof($invoice)-1]["date"];
				$orderChild["endDate"]=$endDate;
				$orderChild["price"]=$periodPrice;
				array_push($productPrices,$orderChild);
				*/
			//print_r($productPrices);
			//array_push($result,$productPrices);
			$order["invoice"]=$productPrices;
			//echo "</br>";
		}
		array_push($orders,$order);
	}
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////// request not in the same month as order

//echo " </br></br></br></br></br></br></br></br>";
		$orderResult = $this->query("SELECT * from orders inner join order_options on orders.order_id= order_options.order_id where 
	customer_id=".$customer_id."
	and (year(creation_date) <".$year."
	or (year(creation_date) =".$year." and month(creation_date) <".$month." ))");
	
	
	while ($order_row = $this->fetch_assoc($orderResult)) {
		//print_r($order_row);
		$order=array();
		$order["order_id"]=$order_row["order_id"];
		$orderDate = new DateTime($order_row["creation_date"]);
		
		
		$invoice = array();
		$orderChild = array();
		
		
		if((((int)$orderDate->format('m'))%12+1)===$month && (int)$orderDate->format('d') >1)/// if order done in the previous month and not in 1st day then price is zero
		{
			$orderChild["product_price"]="0";
			
		}
		else
		{
			$orderChild["product_price"]=$order_row["product_price"];
		}
		
		$beginDate = new DateTime($year."-".$month."-01 00:00:00");
		$orderChild["date"] = $beginDate->format('Y-m-d');
			
		$orderChild["action"]="order";
		
		$orderChild["product_title"]=$order_row["product_title"];
		$orderChild["product_category"]=$order_row["product_category"];
		$orderChild["product_subscription_type"]=$order_row["product_subscription_type"];
			
		array_push($invoice,$orderChild);
		
		////echo "same order month</br>";
		// check if there are any request also before the requested month if exist then set it's price at init value instead of order price
		$requestResult = $this->query("SELECT * from requests where 
		order_id=".$order_row["order_id"]." 
		and (year(creation_date) <".$year."
		or (year(creation_date) =".$year." and month(creation_date) <".$month." ))
		and verdict = 'approve' order by creation_date DESC LIMIT 1");
		
		
		while ($request_row = $this->fetch_assoc($requestResult)) {
			////echo "same request month</br>";
			$beginDate = new DateTime($year."-".$month."-01 00:00:00");
			$invoice = array();
			$orderChild = array();
			$orderChild["date"] = $beginDate->format('Y-m-d');
			$orderChild["product_price"]=$request_row["product_price"];
			$orderChild["action"]=$request_row["action"];
			
			$orderChild["product_title"]=$request_row["product_title"];
			$orderChild["product_category"]=$request_row["product_category"];
			$orderChild["product_subscription_type"]=$request_row["product_subscription_type"];
		
			array_push($invoice,$orderChild);
		}
		
		// now  check if there are any request also in the same month as the requested month
		$requestResult = $this->query("SELECT * from requests where 
		order_id=".$order_row["order_id"]." 
		and (year(creation_date) =".$year." and month(creation_date) =".$month." )
		and verdict = 'approve' order by creation_date");
		
		
		while ($request_row = $this->fetch_assoc($requestResult)) {
			////echo "same request month</br>";
			$orderChild = array();
			$orderChild["date"] = $request_row["creation_date"];
			$orderChild["product_price"]=$request_row["product_price"];
			$orderChild["action"]=$request_row["action"];
			
			$orderChild["product_title"]=$request_row["product_title"];
			$orderChild["product_category"]=$request_row["product_category"];
			$orderChild["product_subscription_type"]=$request_row["product_subscription_type"];
			
			array_push($invoice,$orderChild);
		}
		
		//print_r($invoice);
		////echo sizeof($invoice)."</br>";
		
		if(sizeof($invoice)===0)
		{
			$productPrices = array();
			$orderChild = array();
				$orderChild["beginDate"] = "";
				$orderChild["endDate"]="";
				$orderChild["price"]="0";
				
				$orderChild["product_title"]="";
				$orderChild["product_category"]="";
				$orderChild["product_subscription_type"]="";
				
				array_push($productPrices,$orderChild);
			//array_push($result,productPrices);
			$order["invoice"]=$productPrices;
			//echo "zero </br>";
		}
		else if(sizeof($invoice)===1)
		{
			//echo "inside2";
			//echo $invoice[0]["product_price"];
			$beginDate = new DateTime($invoice[0]["date"]);
			$date = new DateTime($invoice[0]["date"]);
			
				$interval = new DateInterval('P1M');
				$date->add($interval);
			
			$productPrices = array();
			$orderChild = array();
				$orderChild["beginDate"] = $beginDate->format('Y-m-d');
				$orderChild["endDate"]=$date->format('Y-m-d');
				$orderChild["price"]=$invoice[0]["product_price"];
				
				$orderChild["product_title"]=$invoice[0]["product_title"];
				$orderChild["product_category"]=$invoice[0]["product_category"];
				$orderChild["product_subscription_type"]=$invoice[0]["product_subscription_type"];
				
				array_push($productPrices,$orderChild);
			//print_r($productPrices);
			//array_push($result,$productPrices);
			$order["invoice"]=$productPrices;
			//echo "</br>";
		}
		else{
			//echo "inside3";
			$date = new DateTime($year."-".$month."-01 00:00:00");
			
			$monthDays= (int) $date->format( 't' );
			$lastdaydate = new DateTime($year."-".$month."-".$monthDays." 00:00:00");
			
			$orderChild = array();
			$orderChild["date"] = $lastdaydate->format('Y-m-d');
			$orderChild["product_price"]=0;
			$orderChild["action"]="end";
			array_push($invoice,$orderChild);
			
			
			//echo "monthDays :".$monthDays."</br>";
			$productPrices = array();
			
			for($i=1;$i<sizeof($invoice);$i++){
				
				$beginDate = new DateTime($invoice[$i-1]["date"]);
				$endDate = new DateTime($invoice[$i]["date"]);
				//echo "beginDate->format('t') :".$beginDate->format('d')."</br>";
				$periodInDays=(int)$endDate->format('d')-(int)$beginDate->format('d');
				if($i===1)
					$periodInDays++;
				//echo "periodInDays :".$periodInDays."</br>";
				$pricePerDay= (float)$invoice[$i-1]["product_price"]/$monthDays;
				//echo "pricePerDay :".$pricePerDay."</br>";
				$periodPrice=$periodInDays*$pricePerDay;
				//echo "periodPrice :".$periodPrice."</br>";
				if($invoice[$i-1]["action"]==="cancel"){
					$periodPrice=0;
				}
				$orderChild = array();
				$orderChild["beginDate"] = $invoice[$i-1]["date"];
				$orderChild["endDate"]=$invoice[$i]["date"];
				$orderChild["price"]=$periodPrice;
				
				$orderChild["product_title"]=$invoice[$i-1]["product_title"];
				$orderChild["product_category"]=$invoice[$i-1]["product_category"];
				$orderChild["product_subscription_type"]=$invoice[$i-1]["product_subscription_type"];
				
				array_push($productPrices,$orderChild);
			}
			
			//print_r($productPrices);
			//array_push($result,$productPrices);
			$order["invoice"]=$productPrices;
			//echo "</br>";
		}
		array_push($orders,$order);
	}
	$result["orders"]=$orders;
        return $result;
    }
	public function order_month_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null) {
        $orders = array();
        
        $this->query("SET CHARACTER SET utf8");
        
        $order_result = $this->query($queryString);
        while ($order_row = $this->fetch_assoc($order_result)) {
            $order = array();
            foreach ($fields as $key => $value)
            {
                if($key == "displayed_order_id")
                if ((int) $order_row["order_id"] > 10380)
                    $order_row[$value] = (((0x0000FFFF & (int) $order_row["order_id"]) << 16) + ((0xFFFF0000 & (int) $order_row["order_id"]) >> 16));

                $order[$key] = $order_row[$value];
            }
            if ($child != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($childFields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $order_row[$childValue];
                    
                }
                array_push($orderChildArray,$orderChild);
                $order[$child] = $orderChildArray;
            }
			
            if ($child2 != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $order_row[$childValue];
                    
                }
                array_push($orderChildArray,$orderChild);
                $order[$child2] = $orderChildArray;
            }
			//print_r($order_row);
			//echo "1";
			if($order_row["verdict"]==="approve")
			{//echo "2";
				$order["end_date"]=$order_row["action_on_date"];
				if($order_row["action"]!=="cancel")
					if ($child3 != null) {
						//echo "3";
						$orderChildArray = array();
						$orderChild = array();
						foreach ($child3Fields as $childKey => $childValue)
						{
							$orderChild[$childKey] = $order_row[$childValue];
                    
						}
						array_push($orderChildArray,$orderChild);
						$order[$child3] = $orderChildArray;
					}
			}
            array_push($orders,$order);
        }
        return $orders;
    }
    
    public function customer_query($queryString, $depth = 3, $path= null) {
        $customers = array();
        $customer_result = $this->query($queryString);
        while ($customer_row = $this->fetch_assoc($customer_result)) {
            $customers[] = new CustomerTools($customer_row["customer_id"], $this, $depth, $path, $path);
        }
        return $customers;
    }
    public function customer_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null) {
        $customers = array();
        
        $this->query("SET CHARACTER SET utf8");
        
        
        $customers_result = $this->query($queryString);
        while ($customer_row = $this->fetch_assoc($customers_result)) {
            if(isset($customers[$customer_row['customer_id']]))
            {
                
                if ($child2 != null) {
                    $customerChildArray = $customers[$customer_row['customer_id']][$child2];
                    $customerChild = array();
                    foreach ($child2Fields as $childKey => $childValue)
                    {
                        $customerChild[$childKey] = $customer_row[$childValue];

                    }
                    array_push($customerChildArray,$customerChild);
                    $customers[$customer_row['customer_id']][$child2] = $customerChildArray;
                }
            }
            else{
                $customers[$customer_row['customer_id']] = array();
                foreach ($fields as $key => $value)
                {
                    $customers[$customer_row['customer_id']][$key] = $customer_row[$value];
                }
                if ($child != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($childFields as $childKey => $childValue)
                    {
                        $customerChild[$childKey] = $customer_row[$childValue];

                    }
                    array_push($customerChildArray,$customerChild);
                    $customers[$customer_row['customer_id']][$child] = $customerChildArray;
                }
                if ($child2 != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($child2Fields as $childKey => $childValue)
                    {
                        $customerChild[$childKey] = $customer_row[$childValue];

                    }
                    array_push($customerChildArray,$customerChild);
                    $customers[$customer_row['customer_id']][$child2] = $customerChildArray;
                }
            }
            
            
        }
        $customersPure = array();
	foreach($customers as $row){
		array_push($customersPure,$row);
	}
        return $customersPure;
    }
    public function upcoming_customer_query($queryString, $depth = 3, $path = null) {
        $upcoming_customers = array();
        $upcoming_customer_result = $this->query($queryString);
        while ($upcoming_customer_row = $this->fetch_assoc($upcoming_customer_result)) {
            $upcoming_customers[] = new UpcomingCustomerTools($upcoming_customer_row["upcoming_customer_id"], $this, $depth, $path);
        }
        return $upcoming_customers;
    }
    
    public function request_query($queryString, $depth = 3, $path = null) {
        $requests = array();
        $request_result = $this->query($queryString);
        while ($request_row = $this->fetch_assoc($request_result)) {
            $requests[] = new RequestTools($request_row["request_id"], $this, $depth, $path);
        }
        return $requests;
    }
    public function request_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null,$child4=null,$child4Fields=null) {
        $requests = array();
        $this->query("SET CHARACTER SET utf8");
        $requests_result = $this->query($queryString);
        while ($request_row = $this->fetch_assoc($requests_result)) {
             $request = array();
            foreach ($fields as $key => $value)
            {
                $request[$key] = $request_row[$value];
            }
            if ($child != null) {
                $requestChildArray = array();
                $requestChild = array();
                foreach ($childFields as $childKey => $childValue)
                {
                    $requestChild[$childKey] = $request_row[$childValue];
                    
                }
                array_push($requestChildArray,$requestChild);
                $request[$child] = $requestChildArray;
            }
            if ($child2 != null) {
                $requestChildArray = array();
                $requestChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $requestChild[$childKey] = $request_row[$childValue];
                    
                }
                array_push($requestChildArray,$requestChild);
                $request[$child2] = $requestChildArray;
            }
            if ($child3 != null) {
                $requestChildArray = array();
                $requestChild = array();
                foreach ($child3Fields as $childKey => $childValue)
                {
                    $requestChild[$childKey] = $request_row[$childValue];
                    
                }
                array_push($requestChildArray,$requestChild);
                $request[$child3] = $requestChildArray;
            }
            if ($child4 != null) {
                $requestChildArray = array();
                $requestChild = array();
                foreach ($child4Fields as $childKey => $childValue)
                {
                    $requestChild[$childKey] = $request_row[$childValue];
                    
                }
                array_push($requestChildArray,$requestChild);
                $request[$child4] = $requestChildArray;
            }
            
            array_push($requests,$request);
        }
        return $requests;
    }
    
    public function modem_query($queryString, $depth = 3, $path = null) {
        $modems = array();
        $modem_result = $this->query($queryString);
        while ($modem_row = $this->fetch_assoc($modem_result)) {
            $modems[] = new ModemTools($modem_row["modem_id"], $this, $depth, $path);
        }
        return $modems;
    }
    public function modems_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null) {
        $modems = array();
        $this->query("SET CHARACTER SET utf8");
        $modems_result = $this->query($queryString);
        while ($modem_row = $this->fetch_assoc($modems_result)) {
            $modem = array();
            foreach ($fields as $key => $value)
            {
                $modem[$key] = $modem_row[$value];
            }
            if ($child != null) {
                $modemChildArray = array();
                $modemChild = array();
                foreach ($childFields as $childKey => $childValue)
                {
                    $modemChild[$childKey] = $modem_row[$childValue];
                    
                }
                array_push($modemChildArray,$modemChild);
                $modem[$child] = $modemChildArray;
            }
            if ($child2 != null) {
                $modemChildArray = array();
                $modemChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $modemChild[$childKey] = $modem_row[$childValue];
                    
                }
                array_push($modemChildArray,$modemChild);
                $modem[$child2] = $modemChildArray;
            }
            
            array_push($modems,$modem);
        }
        return $modems;
    }
    public function router_query($queryString, $depth = 3, $path = null) {
        $routers = array();
        $router_result = $this->query($queryString);
        while ($router_row = $this->fetch_assoc($router_result)) {
            $routers[] = new RouterTools($router_row["router_id"], $this, $depth, $path);
        }
        return $routers;
    }
   
}
