<?php

class DBTools {

    private $db_host = "";

    private $db_username = "";
    private $db_password = "";
    private $db_name = ""; //database name

    private $conn_routers;
    private $query_result;

    public function __construct($servername,$dbusername,$dbpassword,$dbname) {
      $this->db_host=$servername;
      $this->db_username=$dbusername;
      $this->db_password=$dbpassword;
      $this->db_name=$dbname;
        $this->conn_routers = new mysqli($this->db_host, $this->db_username, $this->db_password, $this->db_name);
        if ($this->conn_routers->connect_error) {
            die("Connection failed: " . $this->conn_routers->connect_error);
        }
    }
    /////required///
    public function getConnection() {
            $this->conn_routers->query("SET CHARACTER SET utf8");
            return $this->conn_routers;
        }
    /////required///
    public function query($queryString) {
        $this->conn_routers->query("SET CHARACTER SET utf8");
        return $this->query_result = $this->conn_routers->query($queryString);
    }
    public function fetch_assoc($query_result) {
        return $query_result->fetch_assoc();
    }

    public function customer_log_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null) {
        $customer_logs = array();
        $this->query("SET CHARACTER SET utf8");
        $customer_log_result = $this->query($queryString);
        while ($customer_log_row = $this->fetch_assoc($customer_log_result)) {
            $customer_log = array();
            foreach ($fields as $key => $value)
            {
                $customer_log[$key] = $customer_log_row[$value];
            }
            if ($child != null) {
                $customer_logChildArray = array();
                $customer_logChild = array();
                foreach ($childFields as $childKey => $childValue)
                {
                    $customer_logChild[$childKey] = $customer_log_row[$childValue];
                }
                array_push($customer_logChildArray,$customer_logChild);
                $customer_log[$child] = $customer_logChildArray;
            }
            if ($child2 != null) {
                $customer_logChildArray = array();
                $customer_logChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $customer_logChild[$childKey] = $customer_log_row[$childValue];
                }
                array_push($customer_logChildArray,$customer_logChild);
                $customer_log[$child2] = $customer_logChildArray;
            }
            if ($child3 != null) {
                $customer_logChildArray = array();
                $customer_logChild = array();
                foreach ($child3Fields as $childKey => $childValue)
                {
                    $customer_logChild[$childKey] = $this->conn_routers->real_escape_string($customer_log_row[$childValue]);
                }
                array_push($customer_logChildArray,$customer_logChild);
                $customer_log[$child3] = $customer_logChildArray;
            }
            array_push($customer_logs,$customer_log);
        }
        return $customer_logs;
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
                    $orderChild[$childKey] = $this->conn_routers->real_escape_string($order_row[$childValue]);
                }
                array_push($orderChildArray,$orderChild);
                $order[$child] = $orderChildArray;
            }
            if ($child2 != null) {
                $orderChildArray = array();
                $orderChild = array();
                foreach ($child2Fields as $childKey => $childValue)
                {
                    $orderChild[$childKey] = $this->conn_routers->real_escape_string($order_row[$childValue]);
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
    //////////////////////////////////
    ////////// function to get the range of start recurring_date of the year that the requested month for yearly customers in
    /////////////////////////////////
    private function getRecurringDateForDate($start_active_date,$recurring_date,$postDate)
      {
        //echo "</br> Post: ".$postDate->format('Y-m-d');
        //echo "</br> Start: ".$start_active_date->format('Y-m-d');
        //echo "</br> End: ".$recurring_date->format('Y-m-d');
        $count=1;
        $returnedDate=array();
        if($postDate >= $start_active_date && $postDate <$recurring_date)
        {
          $returnedDate["count"]=$count;
        	$returnedDate["start_date"]=$start_active_date;
        	$returnedDate["end_date"]  =$recurring_date;
        	return $returnedDate;
        }
        $startDate=new DateTime($recurring_date->format('Y')."-".$recurring_date->format('m')."-".$recurring_date->format('d'));
        $endDate=new DateTime($recurring_date->format('Y')."-".$recurring_date->format('m')."-".$recurring_date->format('d'));
        $interval = new DateInterval('P1Y');
        $endDate->add($interval);
        while(true){
        	//echo "</br> Post: ".$postDate->format('Y-m-d');
        	//echo "</br> Start: ".$startDate->format('Y-m-d');
        	//echo "</br> End: ".$endDate->format('Y-m-d');
          $count=$count+1;
        if($postDate >= $startDate && $postDate<$endDate)
        {
          $returnedDate["count"]=$count;
        	$returnedDate["start_date"]=$startDate;
        	$returnedDate["end_date"]  =$endDate;
        	return $returnedDate;
        }
        $startDate=new DateTime($endDate->format('Y')."-".$endDate->format('m')."-".$endDate->format('d'));
        $interval = new DateInterval('P1Y');
        $endDate->add($interval);
        }
    }
    //////////////////////////////////
    ////////////// function to fill order info in array and return it
    ////////////////////////////////////
    private function fillOrderInfo($order_row,$year,$month){
    $tempDate = new DateTime($year."-".$month."-01");
    $postDate = new DateTime($year."-".$month."-".$tempDate->format( 't' ));
    $orderChild = array();
    $orderChild["displayed_order_id"]=$order_row["order_id"];
    if ((int) $order_row["order_id"] > 10380)
    {
      $order_row["displayed_order_id"] = (((0x0000FFFF & (int) $order_row["order_id"]) << 16) + ((0xFFFF0000 & (int) $order_row["order_id"]) >> 16));
        $orderChild["displayed_order_id"]=$order_row["displayed_order_id"];
    }
    $orderChild["order_id"]=$order_row["order_id"];
    $orderChild["join_type"]=$order_row["join_type"];
    $orderChild["reseller_name"]=$order_row["reseller_name"];
    $orderChild["customer_name"]=$order_row["customer_name"];
    $orderChild["creation_date"] = $order_row["creation_date"];
    $orderChild["total_price"]=is_numeric($order_row["total_price"])?$order_row["total_price"]:0;
    $orderChild["product_price"]=is_numeric($order_row["product_price"])?$order_row["product_price"]:0;
    $orderChild["additional_service_price"]=is_numeric($order_row["additional_service_price"])?$order_row["additional_service_price"]:0;
    $orderChild["setup_price"]=is_numeric($order_row["setup_price"])?$order_row["setup_price"]:0;
    $orderChild["modem_price"]=is_numeric($order_row["modem_price"])?$order_row["modem_price"]:0;
    $orderChild["router_price"]=is_numeric($order_row["router_price"])?$order_row["router_price"]:0;
    $orderChild["static_ip_price"]=is_numeric($order_row["static_ip_price"])?$order_row["static_ip_price"]:0;
    $orderChild["remaining_days_price"]=is_numeric($order_row["remaining_days_price"])?$order_row["remaining_days_price"]:0;
    $orderChild["qst_tax"]=is_numeric($order_row["qst_tax"])?$order_row["qst_tax"]:0;
    $orderChild["gst_tax"]=is_numeric($order_row["gst_tax"])?$order_row["gst_tax"]:0;
    $orderChild["adapter_price"]=is_numeric($order_row["adapter_price"])?$order_row["adapter_price"]:0;
    $orderChild["plan"]=$order_row["plan"];
    $orderChild["modem"]=$order_row["modem"];
    $orderChild["router"]=$order_row["router"];
    $orderChild["static_ip"]=$order_row["static_ip"];
    $orderChild["cable_subscriber"]=$order_row["cable_subscriber"];
    $orderChild["current_cable_provider"]=$order_row["current_cable_provider"];
    $orderChild["cancellation_date"]=$order_row["cancellation_date"];
    $orderChild["installation_date_1"]=$order_row["installation_date_1"];
    $orderChild["actual_installation_date"]=$order_row["actual_installation_date"];
    $orderChild["product_title"]=$order_row["product_title"];
    $orderChild["product_category"]=$order_row["product_category"];
    $orderChild["payment_method"]="Cash on Delivery";
    if(strpos($order_row["merchantref"], 'cache_on_delivery') === false)
    {
    $orderChild["payment_method"]="VISA";
    }
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
    else // if not internet nor phone then ignore for now
    {
    return null;
    }
    $start_active_date = new DateTime($orderChild["start_active_date"]);
    if(
    ((int)$postDate->format('Y')<(int)$start_active_date->format('Y'))
    ||
    ((int)$postDate->format('m')<(int)$start_active_date->format('m') && (int)$postDate->format('Y')===(int)$start_active_date->format('Y'))
    )
    return null;
    $recurring_date=null;
    if(((int)$start_active_date->format('d'))>1)
    {
    $recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
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
    return $orderChild;
    }
    ////////////////////////////////
    ////////// function to get order price with all detials
    ////////////////////////////////
    private function fill_order_price_details_2($orderChild,$year,$month)
    {
      ///////////////// get month info from order
    		$monthInfo=array();
        $start_active_date = new DateTime($orderChild["start_active_date"]);
        $recurring_date = new DateTime($orderChild["recurring_date"]);
        $tempDate = new DateTime($year."-".$month."-01");
        $postDate = new DateTime($year."-".$month."-".$tempDate->format( 't' ));
    		$remaining_days=(int)$start_active_date->format('t')-(int)$start_active_date->format('d')+1;
    		$monthDays=(int)$start_active_date->format('t');
    		$oneDayPrice=(float)$orderChild["product_price"]/(int)$monthDays;
    		$remaining_days_price=$oneDayPrice*$remaining_days;
    		$product_price=(float)$orderChild["product_price"];
    		$additional_service_price=(float)$orderChild["additional_service_price"];
    		$setup_price=(float)$orderChild["setup_price"];
    		$modem_price=(float)$orderChild["modem_price"];
    		$router_price=(float)$orderChild["router_price"];
        $static_ip_price=(float)$orderChild["static_ip_price"];
    		$adapter_price=(float)$orderChild["adapter_price"];
    		/*
    		echo "</br> remaining_days_price: ".$remaining_days_price;
    		echo "</br> product_price: ".$product_price;
    		echo "</br> additional_service_price: ".$additional_service_price;
    		echo "</br> setup_price: ".$setup_price;
    		echo "</br> modem_price: ".$modem_price;
    		echo "</br> router_price: ".$router_price;
    		echo "</br> adapter_price: ".$adapter_price;
    		*/
    		$days=$recurring_date->diff($start_active_date)->days;
        //commission base amount
        $totalPriceWoR=$remaining_days_price+$product_price;
        // subtotal
    		$totalPriceWoT=$totalPriceWoR+$additional_service_price+$setup_price+$modem_price+$router_price+$adapter_price+$static_ip_price;
        $qst_tax=$totalPriceWoT*0.09975;
        $gst_tax=$totalPriceWoT*0.05;
        $monthInfo["additional_service_price"]=$orderChild["additional_service_price"];
        $monthInfo["setup_price"]=$orderChild["setup_price"];
        $monthInfo["modem_price"]=$orderChild["modem_price"];
        $monthInfo["router_price"]=$orderChild["router_price"];
        $monthInfo["plan"]=$orderChild["plan"];
        $monthInfo["modem"]=$orderChild["modem"];
        $monthInfo["router"]=$orderChild["router"];
        $monthInfo["static_ip"]=$orderChild["static_ip"];
        $monthInfo["static_ip_price"]=$orderChild["static_ip_price"];
        $monthInfo["remaining_days_price"]=round($remaining_days_price,2, PHP_ROUND_HALF_UP);
        $monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
        $monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
        $monthInfo["adapter_price"]=$orderChild["adapter_price"];
        $monthInfo["product_title"]=$orderChild["product_title"];
        $monthInfo["days"]=$days;
        $monthInfo["action"]="order";
            $action="recurring";
            if($orderChild["router"]==='rent' || $orderChild["router"]==='rent_hap_lite')
            {
              $action=$action.", Router Rent";
               $monthInfo["router_price"]=$router_price;
             }
            else {
              $monthInfo["router_price"]=0;
            }
            if($orderChild["static_ip"]==="yes")
            {
              $action=$action.", Static IP";
               $monthInfo["static_ip_price"]=$static_ip_price;
             }
            else {
              $monthInfo["static_ip_price"]=0;
            }
            $totalPriceWoR=$product_price;
            $recurring_price=$product_price+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
            $totalPriceWoT=$product_price+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $qst_tax=$totalPriceWoT*0.09975;
            $gst_tax=$totalPriceWoT*0.05;
            $tempPostDate = new DateTime($year."-".$month."-01");
            $days=(int)$tempPostDate->format( 't' );
            $monthInfo["additional_service_price"]=0;
      			$monthInfo["setup_price"]=0;
      			$monthInfo["modem_price"]=0;
      			$monthInfo["plan"]=$orderChild["plan"];
      			$monthInfo["modem"]=$orderChild["modem"];
      			$monthInfo["router"]=$orderChild["router"];
            $monthInfo["static_ip"]=$orderChild["static_ip"];
      			$monthInfo["remaining_days_price"]=0;
      			$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
      			$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
      			$monthInfo["adapter_price"]=$orderChild["adapter_price"];
      			$monthInfo["product_title"]=$orderChild["product_title"];
      			$monthInfo["days"]=$days;
            $monthInfo["action"]=$action;
    		$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
    		$totalPriceWT7=$totalPriceWT;
        $monthInfo["total_price_with_out_router"]=round($totalPriceWoR,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    		$monthInfo["product_price"]=round($orderChild["product_price"],2, PHP_ROUND_HALF_UP);
        /////////////////// end get month infor from order
        ////////////////// check if there is any request before the selected date, if yes get it's info instead of order info
        $change_speed_fee=0;// check if request before one month and after 1st day, if yes set this value to 7$
    		$date = new DateTime($year."-".$month."-01 00:00:00");
    		$monthDays= (int) $date->format( 't' );
    		//if($requestResult->num_rows===0)
    		//{
    			$requestResult = $this->query("SELECT * from requests where
            `action` in ('terminate','change_speed','moving','swap_modem') and
    			order_id=".$orderChild["order_id"]."
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
          $change_speed_date=new DateTime($request_row["action_on_date"]);
    			$interval = new DateInterval('P1M');
    			$change_speed_date->add($interval);
          $action="recurring";
            if(((int)$change_speed_date->format('Y')===(int)$year)
           &&(int)$change_speed_date->format('m')===(int)$month)
           {
             $action=$action.", change speed";
             $change_speed_fee=7;
           }
    			$requestChild["action_on_date"] = $request_row["action_on_date"];
    			$requestChild["verdict_date"] = $request_row["verdict_date"];
    			$requestChild["verdict"] = $request_row["verdict"];
    			$requestChild["product_price"]=$request_row["product_price"];
    			$requestChild["action"]=$request_row["action"];
    			$requestChild["product_title"]=$request_row["product_title"];
    			$requestChild["product_category"]=$request_row["product_category"];
    			$requestChild["product_subscription_type"]=$request_row["product_subscription_type"];
          ////////////////// update month info
    			if($request_row["action"]==="terminate")
    			{
            $monthInfo["recurring_price"]=0;
            $orderChild["recurring_date"]="0000-00-00";
            $monthInfo["total_price_with_out_router"]=0;
    				$monthInfo["total_price_with_out_tax"]=0;
    				$monthInfo["total_price_with_tax"]=0;
    				$monthInfo["total_price_with_tax_p7"]=0;
    				$monthInfo["product_price"]=0;
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				$monthInfo["router_price"]=0;
            $monthInfo["static_ip_price"]=0;
    				$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=0;
    				$monthInfo["gst_tax"]=0;
    				$monthInfo["adapter_price"]=0;
    				$monthInfo["total_price"]=0;
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$monthDays;
    				$monthInfo["action"]="terminated";
    			}
    			else
    			{
    				$totalPriceWoT=(float)$request_row["product_price"]+$change_speed_fee;
            $recurring_price=$totalPriceWoT;
            $static_ip_price_temp=0;
            if($request_row["static_ip"]==="yes"){
              $static_ip_price_temp=$request_row["static_ip_price"];
            }
            $recurring_price=$totalPriceWoT+(float)$request_row["router_price"]+$static_ip_price_temp;
            $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
    				$qst_tax=$totalPriceWoT*0.09975;
    				$gst_tax=$totalPriceWoT*0.05;
    				$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$change_speed_fee;
            $monthInfo["total_price_with_out_router"]=round((float)$request_row["product_price"],2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_price"]=(float)$request_row["product_price"];
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
    					$monthInfo["router_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
            if($monthInfo["static_ip"]!=="yes" )
    					$monthInfo["static_ip_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$monthDays;
            $monthInfo["action"]=$action;
    			}
          ////////////////// end update month info
    			array_push($requests,$requestChild);
    		}
        ///////////////// check if there is request in the same month as the requested date
    		$requestResult = $this->query("SELECT * from requests where
          `action` in ('terminate','change_speed','moving','swap_modem') and
    		order_id=".$orderChild["order_id"]."
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
          			/*if($request_row["action"]==="terminate")
    			{
    				$monthInfo["total_price_with_out_tax"]=0;
    				$monthInfo["total_price_with_tax"]=0;
    				$monthInfo["total_price_with_tax_p7"]=0;
    				$monthInfo["product_price"]=0;
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				$monthInfo["router_price"]=0;
    				$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=0;
    				$monthInfo["gst_tax"]=0;
    				$monthInfo["adapter_price"]=0;
    				$monthInfo["total_price"]=0;
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$monthDays;
    				$monthInfo["action"]="terminated";
    			}
    			else
    			{*/
          $start_month_between_start_and_recurring = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
          $interval = new DateInterval('P1M');
          $start_month_between_start_and_recurring->add($interval);
          $start_day_of_post_month = new DateTime($postDate->format('Y')."-".$postDate->format('m')."-01 00:00:00");
          /*
          print_r($requests);
          echo "size of requests".sizeof($requests)."</br>";
          echo "hasRequest: ".((sizeof($requests)>0)?"true":"fals")."</br>";
          echo "start_active_date: ".$start_active_date->format('Y-m-d')."</br>";
          echo "start_day_of_post_month: ".$start_day_of_post_month->format('Y-m-d')."</br>";
          echo "start_month_between_start_and_recurring: ".$start_month_between_start_and_recurring->format('Y-m-d')."</br>";
          */
    			if( ( (int)$this_action_on_date->format('d')>1 && sizeof($requests)>0)
    				||
    				((int)$this_action_on_date->format('d')>1 &&(
    					(int)$recurring_date->format('Y') < (int)$year
    					||
    					((int)$recurring_date->format('Y') === (int)$year && (int)$recurring_date->format('m') <= (int)$month)
              )
    				)
            ||/// check if post month greater than start_active_date month and before recurring_date month so show zeros
            ((int)$start_active_date->format('d')>1 //and there is remaining_days
            && (// and posted date between start active date and recurring Date
                $start_day_of_post_month->getTimestamp() > $start_active_date->getTimestamp() &&
                $start_day_of_post_month->getTimestamp() <= $start_month_between_start_and_recurring->getTimestamp())
                )
    			)
    			{/*
            / if there is request in the middle of the month
            then we have to split our price calculation as follow:
            1- calculate the paid prices for product and it's tax.
            2- calculate the days used for the current product.
            3- calculate prices for product and tax for the used days.
            4- caculate the days for the remining days that will be used using the new speed.
            5- calculate prices for product and tax for the remaining days.
            6- calculate the difference in product prices.
            7- calualte the difference in tax prices.
            */
            //echo "hi";
            $actionTax=$change_speed_fee;//change speed fee
    				$this_request_days=$monthDays-(int)$this_action_on_date->format('d')+1;
    				$previous_days=$monthDays-$this_request_days;
            $this_product_price= (((float)$request_row["product_price"])/$monthDays)*$this_request_days;
            if($request_row["action"]==="terminate")
            {
              $actionTax+=82;//termination fee
              $this_product_price= 0;
              $orderChild["recurring_date"]="0000-00-00";
              $recurring_price=0;
              $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
            }
    				$previous_product_price= (((float)$monthInfo["product_price"])/$monthDays)*$previous_days;
            $paid_qst_tax=abs((float)$monthInfo["product_price"])*0.09975;
            $paid_gst_tax=abs((float)$monthInfo["product_price"])*0.05;
            $paid_Tax=$paid_qst_tax+$paid_gst_tax;
            $previous_qst_tax=abs($previous_product_price)*0.09975;
            $previous_gst_tax=abs($previous_product_price)*0.05;
            $previous_Tax=$previous_qst_tax+$previous_gst_tax;
    				$priceDifference=(float)$monthInfo["product_price"]-($this_product_price+$previous_product_price);//-(float)$monthInfo["product_price"];
            $total_paid=($this_product_price+$previous_product_price);
            //$priceDifference=((float)$monthInfo["product_price"]-$previous_product_price)+((float)$request_row["product_price"]-$this_product_price);//-(float)$monthInfo["product_price"];
            $monthInfo["product_price_difference"]=$priceDifference;
    				$monthInfo["product_price_previous"]=$monthInfo["product_price"];
            $monthInfo["product_price_current"]=(float)$request_row["product_price"];
            //$totalPriceWoT=(float)$monthInfo["product_price"]+$priceDifference;
    				$monthInfo["product_price"]=$previous_product_price;
    				$monthInfo["product_price_2"]=$this_product_price;
    				$monthInfo["days"]=$previous_days;
    				$monthInfo["days_2"]=$this_request_days;
            //echo $monthInfo["product_price"]."-".$priceDifference;
            ///commission base amount: product + remaining days
    				$totalPriceWoT=$total_paid;
            // subtotal : commission+all addition prices+fees
            if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
            {
    					$monthInfo["router_price"]=0;
            }
            else {
              $request_row["action"]=$request_row["action"].", Router Rent";
            }
            if($monthInfo["static_ip"]!=="yes" )
            {
    					$monthInfo["static_ip_price"]=0;
            }
            else {
              $request_row["action"]=$request_row["action"].", Static Ip";
            }
            $recurring_price=(float)$request_row["product_price"]+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
            $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $total_qst_tax=abs((float)$subtotal)*0.09975;
            $total_gst_tax=abs((float)$subtotal)*0.05;
            $total_tax=$total_qst_tax +$total_gst_tax;
    				$totalPriceWT=$subtotal+$total_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$actionTax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_title_2"]=$request_row["product_title"];
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($previous_qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($previous_gst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["qst_tax_2"]=round($total_qst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["gst_tax_2"]=round($total_gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
    			}
    			else{
            $actionTax=$change_speed_fee+7;
            $this_product_price=(float)$request_row["product_price"];
            if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
    					$monthInfo["router_price"]=0;
            if($monthInfo["static_ip"]!=="yes")
    					$monthInfo["static_ip_price"]=0;
            if($request_row["action"]==="terminate")
            {
              $actionTax=$change_speed_fee+82;//termination fee
              $this_product_price= 0;
              $monthInfo["router_price"]=0;
              $monthInfo["static_ip_price"]=0;
              $orderChild["recurring_date"]="0000-00-00";
              $recurring_price=0;
              $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
            }
            $action=$request_row["action"];
            if((int)$monthInfo["router_price"]>0)
            {
              $action=$action.", Router rent";
            }
            if((int)$monthInfo["static_ip_price"]>0)
            {
              $action=$action.", Static IP";
            }
    				$totalPriceWoT=$this_product_price;
            $recurring_price=$totalPriceWoT+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
            $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
            $qst_tax=$subtotal*0.09975;
    				$gst_tax=$subtotal*0.05;
    				$totalPriceWT=$subtotal+$qst_tax+$gst_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$actionTax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_price"]=(float)$request_row["product_price"];
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$monthDays;
    				$monthInfo["product_title_2"]=$request_row["product_title"];
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
    			}
          $monthInfo["action"]=$action;
    			//}
          ////////////////// end update month info
    			array_push($requests,$requestChild);
    		}
        $start_month_between_start_and_recurring = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
        $interval = new DateInterval('P1M');
        $start_month_between_start_and_recurring->add($interval);
        $start_day_of_post_month = new DateTime($postDate->format('Y')."-".$postDate->format('m')."-01 00:00:00");
        /// check if post month greater than start_active_date month and before recurring_date month so show zeros
        if(sizeof($requests)===0 // if no requests
        && (int)$start_active_date->format('d')>1 //and there is remaining_days
        && (// and posted date between start active date and recurring Date
            $start_day_of_post_month->getTimestamp() > $start_active_date->getTimestamp() &&
            $start_day_of_post_month->getTimestamp() <= $start_month_between_start_and_recurring->getTimestamp())
            )
            {
              /// then show zeros values
              $recurring_price=(float)$monthInfo["product_price"];
              if($monthInfo["router"]==="rent" || $monthInfo["router"]==="rent_hap_lite")
              {
                $recurring_price+=(float)$monthInfo["router_price"];
              }
              if($monthInfo["static_ip"]==="yes")
              {
                $recurring_price+=(float)$monthInfo["static_ip_price"];
              }
              $monthInfo["recurring_price"]=round($recurring_price+$recurring_price*0.09975+$recurring_price*0.05,2, PHP_ROUND_HALF_UP);
              $monthInfo["total_price_with_out_router"]=0;
    					$monthInfo["total_price_with_out_tax"]=0;
    					$monthInfo["total_price_with_tax"]=0;
    					$monthInfo["total_price_with_tax_p7"]=0;
    					$monthInfo["product_price"]=0;
    					$monthInfo["additional_service_price"]=0;
    					$monthInfo["setup_price"]=0;
    					$monthInfo["router_price"]=0;
              $monthInfo["static_ip_price"]=0;
    					$monthInfo["modem_price"]=0;
    					$monthInfo["remaining_days_price"]=0;
    					$monthInfo["qst_tax"]=0;
    					$monthInfo["gst_tax"]=0;
    					$monthInfo["adapter_price"]=0;
    					$monthInfo["total_price"]=0;
    					$monthInfo["days"]=$start_month_between_start_and_recurring->format('t');
    					$monthInfo["action"]="month after start active date";
            }
        return $monthInfo;
    }
    ///////////////////////////////end
    ///////////////////////////////
    ////////// function to get order recurring price details
    ///////////////////////////////
    private function fill_order_price_details($orderChild,$year,$month){
        $monthInfo=array();
        $start_active_date = new DateTime($orderChild["start_active_date"]);
        $recurring_date = new DateTime($orderChild["recurring_date"]);
        $remaining_days=(int)$start_active_date->format('t')-(int)$start_active_date->format('d')+1;
        $monthDays=(int)$start_active_date->format('t');
        $oneDayPrice=(float)$orderChild["product_price"]/(int)$monthDays;
        $remaining_days_price=$oneDayPrice*$remaining_days;
        $product_price=(float)$orderChild["product_price"];
        $additional_service_price=(float)$orderChild["additional_service_price"];
        $setup_price=(float)$orderChild["setup_price"];
        $modem_price=(float)$orderChild["modem_price"];
        $router_price=(float)$orderChild["router_price"];
        $static_ip_price=(float)$orderChild["static_ip_price"];
        $adapter_price=(float)$orderChild["adapter_price"];
        $days=$recurring_date->diff($start_active_date)->days;
        //commission base amount
        $totalPriceWoR=$remaining_days_price+$product_price;
        // subtotal
        $totalPriceWoT=$totalPriceWoR+$additional_service_price+$setup_price+$modem_price+$router_price+$adapter_price+$static_ip_price;
        $qst_tax=$totalPriceWoT*0.09975;
        $gst_tax=$totalPriceWoT*0.05;
        $monthInfo["additional_service_price"]=$orderChild["additional_service_price"];
        $monthInfo["setup_price"]=$orderChild["setup_price"];
        $monthInfo["modem_price"]=$orderChild["modem_price"];
        $monthInfo["router_price"]=$orderChild["router_price"];
        $monthInfo["plan"]=$orderChild["plan"];
        $monthInfo["modem"]=$orderChild["modem"];
        $monthInfo["router"]=$orderChild["router"];
        $monthInfo["static_ip"]=$orderChild["static_ip"];
        $monthInfo["static_ip_price"]=$orderChild["static_ip_price"];
        $monthInfo["remaining_days_price"]=round($remaining_days_price,2, PHP_ROUND_HALF_UP);
        $monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
        $monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
        $monthInfo["adapter_price"]=$orderChild["adapter_price"];
        $monthInfo["product_title"]=$orderChild["product_title"];
        $monthInfo["days"]=$days;
        $monthInfo["action"]="order";
        //echo $recurring_date->format('Y')."<".$year."</br>";
        //echo $recurring_date->format('m')."<".$month."</br>";
        if($recurring_date->format('Y')<$year
        || ($recurring_date->format('Y')===$year && $recurring_date->format('m')<=$month)
        )
        {
          $action="recurring";
          if($orderChild["router"]==='rent' ||  $orderChild["router"]==='rent_hap_lite')
          {
            $action=$action.", Router Rent";
             $monthInfo["router_price"]=$router_price;
           }
          else {
            $monthInfo["router_price"]=0;
          }
          if($orderChild["static_ip"]==="yes" )
          {
            $action=$action.", Static IP";
             $monthInfo["static_ip_price"]=$static_ip_price;
           }
          else {
            $monthInfo["static_ip_price"]=0;
          }
          $totalPriceWoR=$product_price;
          $totalPriceWoT=$product_price+(float)$monthInfo["router_price"]+(float)$monthInfo["static_ip_price"];
          $qst_tax=$totalPriceWoT*0.09975;
          $gst_tax=$totalPriceWoT*0.05;
          $tempPostDate = new DateTime($year."-".$month."-01");
          $days=(int)$tempPostDate->format( 't' );
          $monthInfo["additional_service_price"]=0;
          $monthInfo["setup_price"]=0;
          $monthInfo["modem_price"]=0;
          $monthInfo["plan"]=$orderChild["plan"];
          $monthInfo["modem"]=$orderChild["modem"];
          $monthInfo["router"]=$orderChild["router"];
          $monthInfo["static_ip"]=$orderChild["static_ip"];
          $monthInfo["static_ip_price"]=$orderChild["static_ip_price"];
          $monthInfo["remaining_days_price"]=0;
          $monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
          $monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
          $monthInfo["adapter_price"]=$orderChild["adapter_price"];
          $monthInfo["product_title"]=$orderChild["product_title"];
          $monthInfo["days"]=$days;
          $monthInfo["action"]=$action;
        }
        $totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
        $totalPriceWT7=$totalPriceWT;
        $monthInfo["total_price_with_out_router"]=round($totalPriceWoR,2, PHP_ROUND_HALF_UP);
        $monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
        $monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
        $monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
        $monthInfo["product_price"]=round($orderChild["product_price"],2, PHP_ROUND_HALF_UP);
        return $monthInfo;
    /////////////////// end get month infor from order
    }
    ///////////////////////////////
    ////////// function to get all customers that have more than one order and get thier recurring date and price details
    ///////////////////////////////
    public function customers_need_merge_monthly($year,$month) {
      $this->query("SET CHARACTER SET utf8");
    $query="SELECT orders.order_id as id,
      orders.*,order_options.*,merchantrefs.*
      ,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name'
      from orders
      INNER JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
      INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
      INNER join order_options on orders.order_id= order_options.order_id
      LEFT join merchantrefs on orders.order_id= merchantrefs.order_id
      where product_subscription_type='monthly' and
    orders.customer_id in (SELECT customer_id FROM `orders` group by customer_id HAVING count(customer_id)>1 ) ORDER BY `orders`.customer_id";
    //echo $query;
    $ordersResult = $this->query($query);
    $order_rows=array();
    while ($order_row = $this->fetch_assoc($ordersResult)) {
    $order_row['order_id']=$order_row['id'];
      array_push($order_rows,$order_row);
    }
    $customers=array();
    for($i=0;$i<sizeof($order_rows)-1;$i=$i+2){
    $customer=array();
    $orderOne=$this->fillOrderInfo($order_rows[$i],$year,$month);
    $orderTwo=$this->fillOrderInfo($order_rows[$i+1],$year,$month);
    $currentDate = new DateTime($year."-".$month."-01");
    $firstRecurring = new DateTime($year."-".$month."-01");
    $interval = new DateInterval('P1M');
    $firstRecurring->add($interval);
    $compareOrderRecurringDate=new DateTime($orderTwo["recurring_date"]);
    $orderOneStartActiveDate = new DateTime($orderOne["recurring_date"]);
    if($orderOneStartActiveDate>$compareOrderRecurringDate)
      $compareOrderRecurringDate=$orderOneStartActiveDate;
    //echo $compareOrderRecurringDate->format('Y-m-d')."!==".$firstRecurring->format('Y-m-d')."</br>";
    if($compareOrderRecurringDate->format('Y-m-d')!==$firstRecurring->format('Y-m-d'))
      continue;
    if(!$orderOne || !$orderTwo)
    {
      continue;
    }
    //while ($order_row = $this->fetch_assoc($ordersResult)) {
    //$monthsInfoOne=$this->fill_order_price_details($orderOne,$year,$month);
    //$monthsInfoTwo=$this->fill_order_price_details($orderTwo,$year,$month);
    $monthsInfoOne=$this->fill_order_price_details_2($orderOne,$year,$month);
    $monthsInfoTwo=$this->fill_order_price_details_2($orderTwo,$year,$month);
    $orders=array();
    $orderOne["monthInfo"]=$monthsInfoOne;
    $orderTwo["monthInfo"]=$monthsInfoTwo;
    array_push($orders,$orderOne);
    array_push($orders,$orderTwo);
    $customer["customer_name"]=$order_rows[$i]["customer_name"];
    $customer["reseller_name"]=$order_rows[$i]["reseller_name"];
    $customer["merchantref"]=$order_rows[$i]["merchantref"];
    $customer["orders"]=$orders;
    array_push($customers,$customer);
    //$tempDate = new DateTime($year."-".$month."-01");
    //$postDate = new DateTime($year."-".$month."-".$tempDate->format( 't' ));
    }
    return $customers;
    }
    ///////////////////////////////
    ///////// function to get orders with next expiration date
    ///////////////////////////////
    public function order_expiration_count(){
    $query="SELECT count(*) as 'expire_count' FROM `order_expiration_notify` WHERE `seen` = 'no'";
    $ordersResult = $this->query($query);
    return $this->fetch_assoc($ordersResult)["expire_count"];
    }
    public function get_orders_expire($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null) {
    $orders = array();
    $this->query("SET CHARACTER SET utf8");
    $order_result = $this->query($queryString);
    while ($order_row = $this->fetch_assoc($order_result)) {
        $order = array();
        $currentDate=new DateTime();
        $expireDate=new DateTime($order_row["expiration_date"]);
        $remaining_days=$expireDate->diff($currentDate)->days;
        $order["remaining_days"]=$remaining_days;
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
    public function order_expiration_date(){
            $this->query("SET CHARACTER SET utf8");
        $query="SELECT
            orders.*,order_options.* from orders
          INNER join order_options on orders.order_id= order_options.order_id";
            //echo $query;
    		$ordersResult = $this->query($query);
    		$orders=array();
    		while ($order_row = $this->fetch_assoc($ordersResult)) {
          $postDate=new DateTime();
          //$interval = new DateInterval('P1Y');
      		//$postDate->add($interval);
          	$orderChild = array();
            $order_row["displayed_order_id"]=$order_row["order_id"];
            if ((int) $order_row["order_id"] > 10380)
                $order_row["displayed_order_id"] = (((0x0000FFFF & (int) $order_row["order_id"]) << 16) + ((0xFFFF0000 & (int) $order_row["order_id"]) >> 16));
                $orderChild["displayed_order_id"]=$order_row["displayed_order_id"];
                $orderChild["order_id"]=$order_row["order_id"];
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
          			else // if not internet nor phone then ignore for now
          			{
          				continue;
          			}
          			$start_active_date = new DateTime($orderChild["start_active_date"]);
          ///////////////////////////////////////////////////////////////////////////////////// if requsted date is before the order then skip this order
          			if($postDate<$start_active_date)
          				continue;
          			if(((int)$start_active_date->format('d'))>1)
          			{
          				$recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
          				$interval = new DateInterval('P1M');
          				$recurring_date->add($interval);
          				$interval = new DateInterval('P1Y');
          				$recurring_date->add($interval);
          				$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
          			}
          			else{
          				$recurring_date=new DateTime($orderChild["start_active_date"]);
          				$interval = new DateInterval('P1Y');
          				$recurring_date->add($interval);
          				$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
          			}
                $recurring_date_next=new DateTime($orderChild["recurring_date"]);
              $startEndRecurringDate=$this->getRecurringDateForDate($start_active_date,$recurring_date,$postDate);
              $orderChild["start_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');
              $orderChild["current_date"]=$postDate->format('Y-m-d');
              $orderChild["end_date"]=$startEndRecurringDate["end_date"]->format('Y-m-d');
              $orderChild["year_no"]=$startEndRecurringDate["count"];
              $orderChild["days_diff"]=$postDate->diff($startEndRecurringDate["end_date"])->days;
              if($postDate->diff($startEndRecurringDate["end_date"])->days<75){
                    array_push($orders,$orderChild);
                }
    		}
        return $orders;
    }
    ///////////////////////////////
    ///////// function to get orders with Yearly subscibtion and requests for cutomer in specific month with all month invoice
    ///////////////////////////////
    public function orders_by_month_yearly($customer_id,$year,$month) {
        $this->query("SET CHARACTER SET utf8");
    $query="SELECT orders.order_id as id,
        orders.*,order_options.*,merchantrefs.*
        ,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name'
        from orders
        INNER JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
        INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
        INNER join order_options on orders.order_id= order_options.order_id
        LEFT join merchantrefs on orders.order_id= merchantrefs.order_id
        where product_subscription_type='yearly' and
    		orders.customer_id=".$customer_id;
        //echo $query;
    $ordersResult = $this->query($query);
    $orders=array();
    while ($order_row = $this->fetch_assoc($ordersResult)) {
    	$monthsInfo=array();
    	$tempPostDate = new DateTime($year."-".$month."-01");
    	$postDate = new DateTime($year."-".$month."-".$tempPostDate->format( 't' ));
    	$orderChild = array();
      $order_row["order_id"]=$order_row["id"];
      $orderChild["displayed_order_id"]=$order_row["order_id"];
      $order_id=$order_row["order_id"];
      if ((int) $order_id > 10380)
          $orderChild["displayed_order_id"] = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
    	$orderChild["order_id"]=$order_row["order_id"];
      /// discount fields
      $orderChild["discount"]=$order_row["discount"];
      $orderChild["discount_duration"]=$order_row["discount_duration"];
      $orderChild["free_router"]=$order_row["free_router"];
      $orderChild["free_modem"]=$order_row["free_modem"];
      $orderChild["free_adapter"]=$order_row["free_adapter"];
      $orderChild["free_installation"]=$order_row["free_installation"];
      $orderChild["free_transfer"]=$order_row["free_transfer"];
      ///////////
      ///////////
      ////reseller commission percentage
      $orderChild["reseller_commission_percentage"]=(int)$order_row["reseller_commission_percentage"];
      ///////////
      $orderChild["join_type"]=$order_row["join_type"];
      $orderChild["reseller_name"]=$order_row["reseller_name"];
      $orderChild["customer_name"]=$order_row["customer_name"];
    	$orderChild["creation_date"] = $order_row["creation_date"];
    	$orderChild["total_price"]=is_numeric($order_row["total_price"])?$order_row["total_price"]:0;
    	$orderChild["product_price"]=is_numeric($order_row["product_price"])?$order_row["product_price"]:0;
    	$orderChild["additional_service_price"]=is_numeric($order_row["additional_service_price"])?$order_row["additional_service_price"]:0;
    	$orderChild["setup_price"]=is_numeric($order_row["setup_price"])?$order_row["setup_price"]:0;
    	$orderChild["modem_price"]=is_numeric($order_row["modem_price"])?$order_row["modem_price"]:0;
    	$orderChild["router_price"]=is_numeric($order_row["router_price"])?$order_row["router_price"]:0;
      $orderChild["static_ip_price"]=is_numeric($order_row["static_ip_price"])?$order_row["static_ip_price"]:0;
    	$orderChild["remaining_days_price"]=is_numeric($order_row["remaining_days_price"])?$order_row["remaining_days_price"]:0;
    	$orderChild["qst_tax"]=is_numeric($order_row["qst_tax"])?$order_row["qst_tax"]:0;
    	$orderChild["gst_tax"]=is_numeric($order_row["gst_tax"])?$order_row["gst_tax"]:0;
    	$orderChild["adapter_price"]=is_numeric($order_row["adapter_price"])?$order_row["adapter_price"]:0;
    	$orderChild["plan"]=$order_row["plan"];
    	$orderChild["modem"]=$order_row["modem"];
    	$orderChild["router"]=$order_row["router"];
      $orderChild["static_ip"]=$order_row["static_ip"];
    	$orderChild["cable_subscriber"]=$order_row["cable_subscriber"];
    	$orderChild["current_cable_provider"]=$order_row["current_cable_provider"];
    	$orderChild["cancellation_date"]=$order_row["cancellation_date"];
    	$orderChild["installation_date_1"]=$order_row["installation_date_1"];
    	$orderChild["actual_installation_date"]=$order_row["actual_installation_date"];
    	$orderChild["product_title"]=$order_row["product_title"];
    	$orderChild["product_category"]=$order_row["product_category"];
    	$orderChild["product_subscription_type"]=$order_row["product_subscription_type"];
      $orderChild["payment_method"]="Cash on Delivery";
      if(strpos($order_row["merchantref"], 'cache_on_delivery') === false)
      {
        $orderChild["payment_method"]="VISA";
      }
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
    	else // if not internet nor phone then ignore for now
    	{
    		continue;
    	}
    	$start_active_date = new DateTime($orderChild["start_active_date"]);
        ///////////////////////////////////////////////////////////////////////////////////// if requsted date is before the order then skip this order
    		if(
    			((int)$postDate->format('Y')<(int)$start_active_date->format('Y'))
    			||
    			((int)$postDate->format('m')<(int)$start_active_date->format('m') && (int)$postDate->format('Y')===(int)$start_active_date->format('Y'))
    		)
    			continue;
    		if(((int)$start_active_date->format('d'))>1)
    		{
    			$recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
    			$interval = new DateInterval('P1M');
    			$recurring_date->add($interval);
    			$interval = new DateInterval('P1Y');
    			$recurring_date->add($interval);
    			$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
    		}
    		else{
    			$recurring_date=new DateTime($orderChild["start_active_date"]);
    			$interval = new DateInterval('P1Y');
    			$recurring_date->add($interval);
    			$orderChild["recurring_date"]=$recurring_date->format('Y-m-d');
    		}
    		$monthInfo=array();
    		$monthInfo["product_category"]=$order_row["product_category"];
    		$monthInfo["product_subscription_type"]=$order_row["product_subscription_type"];
    		$startEndRecurringDate=$this->getRecurringDateForDate($start_active_date,$recurring_date,$postDate);
    		if($startEndRecurringDate["start_date"]!== $start_active_date)
    		{
    			$product_price=(float)$orderChild["product_price"];
    			$totalPriceWoT=$product_price;
    			$qst_tax=$totalPriceWoT*0.09975;
    			$gst_tax=$totalPriceWoT*0.05;
    			$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
          $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["product_price"]=$orderChild["product_price"];
    			$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			$monthInfo["modem_price"]=$orderChild["modem_price"];
    			$monthInfo["router_price"]=$orderChild["router_price"];
    			$monthInfo["plan"]=$orderChild["plan"];
    			$monthInfo["modem"]=$orderChild["modem"];
    			$monthInfo["router"]=$order_row["router"];
          $monthInfo["static_ip"]=$order_row["static_ip"];
          $monthInfo["static_ip_price"]=$order_row["static_ip_price"];
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=$qst_tax;
    			$monthInfo["gst_tax"]=$gst_tax;
    			$monthInfo["adapter_price"]="0";
    			$monthInfo["product_title"]=$order_row["product_title"];
    			$monthInfo["days"]=$startEndRecurringDate["start_date"]->diff($startEndRecurringDate["end_date"])->days;
    			$monthInfo["from"]="recurring";
    			$monthInfo["action"]="recurring";
    			$monthInfo["action_on_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');

          //////////////////////// check if there is terminate request before active date
          $requestTerminateQuery="SELECT * from requests where
          `action` in ('terminate') and
          order_id=".$orderChild["order_id"]."
          and date(action_on_date) <='".$start_active_date->format('Y-m-d')."'
          and verdict = 'approve' order by action_on_date DESC LIMIT 1";
          $requestTerminateResult = $this->query($requestTerminateQuery);


          while ($request_terminate_row = $this->fetch_assoc($requestTerminateResult)) {

              ////////////////// update month info
              if($request_terminate_row["action"]==="terminate")
              {
                $fees_charged=$request_terminate_row["fees_charged"];
                $orderChild["recurring_date"]="0000-00-00";
                $monthInfo["total_price_with_out_router"]=0;
                $monthInfo["total_price_with_out_tax"]=0;
                $monthInfo["total_price_with_tax"]=$fees_charged;
                $monthInfo["total_price_with_tax_p7"]=$fees_charged;
                $monthInfo["product_price"]=0;
                $monthInfo["additional_service_price"]=0;
                $monthInfo["setup_price"]=0;
                $monthInfo["router_price"]=0;
                $monthInfo["static_ip_price"]=0;
                $monthInfo["modem_price"]=0;
                $monthInfo["remaining_days_price"]=0;
                $monthInfo["qst_tax"]=0;
                $monthInfo["gst_tax"]=0;
                $monthInfo["adapter_price"]=0;
                $monthInfo["total_price"]=0;
                $monthInfo["action"]="terminated";
              }
              array_push($monthsInfo,$monthInfo);
              $orderChild["requests"]=[];
              $orderChild["monthInfo"]=$monthsInfo;
              array_push($orders,$orderChild);

             return $orders;
            }



    			/// if there is request after order and before the recurring date get it's value as init value for recurring date
    			$requestResult = $this->query("SELECT * from requests where
            `action` in ('terminate','change_speed','moving','swap_modem') and
    			order_id=".$order_row["order_id"]."
    			and (year(action_on_date) <".$startEndRecurringDate["start_date"]->format('Y')."
    			or (year(action_on_date) =".$startEndRecurringDate["start_date"]->format('Y')." and month(action_on_date) <".$startEndRecurringDate["start_date"]->format('m')." ))
    			and verdict = 'approve' order by action_on_date DESC LIMIT 1");
    			while ($request_row = $this->fetch_assoc($requestResult)) {
            ////////////////// update month info
    				if($request_row["action"]==="terminate")
    				{
              $monthInfo["total_price_with_out_router"]=0;
    					$monthInfo["total_price_with_out_tax"]=0;
    					$monthInfo["total_price_with_tax"]=0;
    					$monthInfo["total_price_with_tax_p7"]=0;
    					$monthInfo["product_price"]=0;
    					$monthInfo["additional_service_price"]=0;
    					$monthInfo["setup_price"]=0;
    					$monthInfo["router_price"]=0;
              $monthInfo["static_ip_price"]=0;
    					$monthInfo["modem_price"]=0;
    					$monthInfo["remaining_days_price"]=0;
    					$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    					$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    					$monthInfo["adapter_price"]=0;
    					$monthInfo["total_price"]=0;
    					$monthInfo["product_title"]=$request_row["product_title"];
    					$monthInfo["days"]=$startEndRecurringDate["start_date"]->diff($startEndRecurringDate["end_date"])->days;
    					$monthInfo["action"]="recurring";
    					$monthInfo["from"]="recurring";
    					$monthInfo["action_on_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');
    				}
    				else
    				{
    				$product_price=(float)$request_row["product_price"];
    				$totalPriceWoT=$product_price;
    				$qst_tax=$totalPriceWoT*0.09975;
    				$gst_tax=$totalPriceWoT*0.05;
    				$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_price"]=round($request_row["product_price"],2, PHP_ROUND_HALF_UP);
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				$monthInfo["plan"]=$order_row["plan"];
    				$monthInfo["modem"]=$order_row["modem"];
    				$monthInfo["router"]=$order_row["router"];
            $monthInfo["static_ip"]=$order_row["static_ip"];
    				$monthInfo["router_price"]=0;
            $monthInfo["static_ip_price"]=0;
    				$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$startEndRecurringDate["start_date"]->diff($startEndRecurringDate["end_date"])->days;
    				$monthInfo["action"]="recurring";
    				$monthInfo["from"]="recurring";
    				$monthInfo["action_on_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');
    				}
    ////////////////// end update month info
    			}
    		}
    		else
    		{
          ///////////////// get month info from order
    		$remaining_days=(int)$start_active_date->format('t')-(int)$start_active_date->format('d')+1;
    		$startSubscriptionYearlyDate=new DateTime($recurring_date->format('Y')."-".$recurring_date->format('m')."-".$recurring_date->format('d'));
    		$interval = new DateInterval('P1Y');
    		$startSubscriptionYearlyDate->sub($interval);
    		$yearDays=$recurring_date->diff($startSubscriptionYearlyDate)->days;
        $oneDayPrice=(float)$orderChild["product_price"]/(int)$yearDays;
        $oneDayPriceRouter=(float)$orderChild["router_price"]/(int)$yearDays;
        $oneDayPriceStaticIP=(float)$orderChild["static_ip_price"]/(int)$yearDays;
        $oneDayPriceAdditionalPrice=(float)$orderChild["additional_service_price"]/(int)$yearDays;
        $remaining_days_price=$oneDayPrice*$remaining_days;
        $remaining_days_price_router=$oneDayPriceRouter*$remaining_days;
        $remaining_days_price_static_ip=$oneDayPriceStaticIP*$remaining_days;
        $remaining_days_price_additional_price=$oneDayPriceAdditionalPrice*$remaining_days;
        $remaining_days_price_all=$remaining_days_price+$remaining_days_price_router+$remaining_days_price_additional_price+$remaining_days_price_static_ip;
    		$product_price=(float)$orderChild["product_price"];
    		$additional_service_price=(float)$orderChild["additional_service_price"];
    		$setup_price=(float)$orderChild["setup_price"];
    		$modem_price=(float)$orderChild["modem_price"];
    		$router_price=(float)$orderChild["router_price"];
        $static_ip_price=(float)$orderChild["static_ip_price"];
    		$adapter_price=(float)$orderChild["adapter_price"];
    		/*
    		echo "</br> remaining_days_price: ".$remaining_days_price;
    		echo "</br> product_price: ".$product_price;
    		echo "</br> additional_service_price: ".$additional_service_price;
    		echo "</br> setup_price: ".$setup_price;
    		echo "</br> modem_price: ".$modem_price;
    		echo "</br> router_price: ".$router_price;
    		echo "</br> adapter_price: ".$adapter_price;
    		*/
        $totalPriceWoR=$remaining_days_price_all+$product_price;
    		$totalPriceWoT=$totalPriceWoR+$additional_service_price+$setup_price+$modem_price+$router_price+$adapter_price+$static_ip_price;
    		$qst_tax=$totalPriceWoT*0.09975;
    		$gst_tax=$totalPriceWoT*0.05;
    		$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
    		$totalPriceWT7=$totalPriceWT;
        $monthInfo["total_price_with_out_router"]=round($totalPriceWoR,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    		$monthInfo["product_price"]=(float)$orderChild["product_price"];
    		$monthInfo["additional_service_price"]=$orderChild["additional_service_price"];
    		$monthInfo["setup_price"]=$orderChild["setup_price"];
    		$monthInfo["modem_price"]=$orderChild["modem_price"];
    		$monthInfo["router_price"]=$orderChild["router_price"];
        $monthInfo["static_ip_price"]=$orderChild["static_ip_price"];
    		$monthInfo["plan"]=$order_row["plan"];
    		$monthInfo["modem"]=$order_row["modem"];
    		$monthInfo["static_ip"]=$order_row["static_ip"];
    		$monthInfo["remaining_days_price"]=round($remaining_days_price_all,2, PHP_ROUND_HALF_UP);
    		$monthInfo["remaining_days"]=$remaining_days;
    		$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    		$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    		$monthInfo["adapter_price"]=$orderChild["adapter_price"];
    		$monthInfo["product_title"]=$order_row["product_title"];
    		$monthInfo["days"]=$recurring_date->diff($start_active_date)->days;
    		$monthInfo["from"]="order";
    		$monthInfo["action"]="order";
    		$monthInfo["action_on_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');
        /////////////////// end get month infor from order
    		}
        ////////////////// check if there is any request before the selected date and after recurring or start active date, if yes get it's info incase the ordered date has also request in the same month to use this infor in calculation, if there is no request at the same month then neglect this request info
    		$date = new DateTime($year."-".$month."-01 00:00:00");
    		$monthDays= (int) $startEndRecurringDate["start_date"]->diff($startEndRecurringDate["end_date"])->days;
    		//if($requestResult->num_rows===0)
    		//{
    			$requestResult = $this->query("SELECT * from requests where
            `action` in ('terminate','change_speed','moving','swap_modem') and
    			order_id=".$order_row["order_id"]."
    			and
    			(year(action_on_date) <".$year."
    			or (year(action_on_date) =".$year." and month(action_on_date) <".$month." ))
    			and
    			(year(action_on_date) >".$startEndRecurringDate["start_date"]->format('Y')."
    			or (year(action_on_date) =".$startEndRecurringDate["start_date"]->format('Y')." and month(action_on_date) >".$startEndRecurringDate["start_date"]->format('m')." ))
    			and verdict = 'approve' order by action_on_date ASC");
    		//}
    		$requests=array();
    		$hasRequest=false;
    		$temp=array();
    						$temp["action_on_date"]=$monthInfo["action_on_date"];
    						$temp["action"]=$monthInfo["action"];
                $temp["fees_charged"]=0;
    						$temp["product_price"]=$monthInfo["product_price"];
    						$temp["product_title"]=$monthInfo["product_title"];
    						$temp["product_category"]=$monthInfo["product_category"];
    						$temp["product_subscription_type"]=$monthInfo["product_subscription_type"];
    						array_push($requests,$temp);
    		while ($request_row = $this->fetch_assoc($requestResult)) {
    			$hasRequest=true;
    			$tempRequestChild = array();
    			//$requestChild = array();
    			$tempRequestChild["creation_date"] = $request_row["creation_date"];
    			$tempRequestChild["action_on_date"] = $request_row["action_on_date"];
    			$tempRequestChild["verdict_date"] = $request_row["verdict_date"];
    			$tempRequestChild["verdict"] = $request_row["verdict"];
    			$tempRequestChild["product_price"]=$request_row["product_price"];
    			$tempRequestChild["action"]=$request_row["action"];
    			$tempRequestChild["product_title"]=$request_row["product_title"];
    			$tempRequestChild["product_category"]=$request_row["product_category"];
    			$tempRequestChild["product_subscription_type"]=$request_row["product_subscription_type"];
          $tempRequestChild["fees_charged"]=$request_row["fees_charged"];
    			/*
          ////////////////// update month info
    			if($request_row["action"]==="terminate")
    			{
    			$monthInfo["product_price"]="0";
    			$monthInfo["additional_service_price"]="0";
    			$monthInfo["setup_price"]="0";
    			$monthInfo["router_price"]="0";
    			$monthInfo["modem_price"]="0";
    			$monthInfo["remaining_days_price"]="0";
    			$monthInfo["qst_tax"]="0";
    			$monthInfo["gst_tax"]="0";
    			$monthInfo["adapter_price"]="0";
    			$monthInfo["total_price"]="0";
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]="0";
    			$monthInfo["action"]="terminated";
    			$monthInfo["from"]="request";
    			}
    			else
    			{
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
    			$monthInfo["from"]="request";
    			}
        ////////////////// end update month info
        */
    			array_push($requests,$tempRequestChild);
    		}
        ///////////////// check if there is request in the same month as the requested date
    		$requestResult = $this->query("SELECT * from requests where
          `action` in ('terminate','change_speed','moving','swap_modem') and
    		order_id=".$order_row["order_id"]."
    		and (year(action_on_date) =".$year." and month(action_on_date) =".$month." )
    		and verdict = 'approve' order by action_on_date");
    		$hasRequestInSameMonth=false;
    		while ($request_row = $this->fetch_assoc($requestResult)) {
    			$hasRequestInSameMonth=true;
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
          $requestChild["fees_charged"]=$request_row["fees_charged"];
          ////////////////// update month info
    			$this_action_on_date = new DateTime($request_row["action_on_date"]);
    			$recurring_date = new DateTime($orderChild["recurring_date"]);
    /////////////////////////// check if this request is made after the 1st day in month or made before the first recurring_date
        //// this condition take care of the folloing scenarios :
        ////// 1- request made 	is made after the 1st day in month and has previous request
        ////// 2- request might happened in any day of the month but it happened before the first recurring_date
        ////// so in both scenario we have to calculate and split the price in to two periods
        ////// assuming only one request or order in month, and assuming if order made after the 1st day then the remaining days price in already count and product price is for full month
    			/*if($request_row["action"]==="terminate")
    			{
    			$monthInfo["product_price"]="0";
    			$monthInfo["additional_service_price"]="0";
    			$monthInfo["setup_price"]="0";
    			$monthInfo["router_price"]="0";
    			$monthInfo["modem_price"]="0";
    			$monthInfo["remaining_days_price"]="0";
    			$monthInfo["qst_tax"]="0";
    			$monthInfo["gst_tax"]="0";
    			$monthInfo["adapter_price"]="0";
    			$monthInfo["total_price"]="0";
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]=$monthDays;
    			$monthInfo["action"]="terminated";
    			$monthInfo["from"]="request";
    			}
    			else*/
    			{
    			/*
    			if( ( (int)$this_action_on_date->format('d')>1 && $hasRequest)
    				||
    				(
    					(int)$recurring_date->format('Y') > (int)$year
    					||
    					((int)$recurring_date->format('Y') === (int)$year && (int)$recurring_date->format('m') > (int)$month)
    				)
    			)
    			{*/
    				$productPrices = array();
    						$temp=array();
    						$temp["action_on_date"]=$requestChild["action_on_date"];
    						$temp["action"]=$requestChild["action"];
    						$temp["product_price"]=$requestChild["product_price"];
    						if($requestChild["action"]==="terminate")
    						$temp["product_price"]=0;
                $temp["fees_charged"]=$requestChild["fees_charged"];
    						$temp["product_title"]=$requestChild["product_title"];
    						$temp["product_category"]=$requestChild["product_category"];
    						$temp["product_subscription_type"]=$requestChild["product_subscription_type"];
    						array_push($requests,$temp);
    						$temp=array();
    						$temp["action_on_date"]=$startEndRecurringDate["end_date"]->format('Y-m-d');
    						$temp["action"]="recurring";
                $temp["fees_charged"]=$requestChild["fees_charged"];
    						$temp["product_price"]=$requestChild["product_price"];
    						$temp["product_title"]=$requestChild["product_title"];
    						$temp["product_category"]=$requestChild["product_category"];
    						$temp["product_subscription_type"]=$requestChild["product_subscription_type"];
    						array_push($requests,$temp);
    						//print_r($requests);
    					for($i=1;$i<sizeof($requests);$i++){
    						$beginDate = new DateTime($requests[$i-1]["action_on_date"]);
    						$endDate = new DateTime($requests[$i]["action_on_date"]);
    						//echo "beginDate->format('t') :".$beginDate->format('d')."</br>";
    						$periodInDaysPrevious=(int)$endDate->diff($beginDate)->days;
    						$periodInDaysCurrent=(int)$startEndRecurringDate["end_date"]->diff($endDate)->days;
    						//if($i===1)
    							//$periodInDays++;
    						//echo "periodInDays :".$periodInDays."</br>";
    						$pricePerDayPrevious= (float)$requests[$i-1]["product_price"]/$monthDays;
    						$pricePerDayCurrent= (float)$requests[$i]["product_price"]/$monthDays;
    						//echo "pricePerDay :".$pricePerDay."</br>";
    						$periodPricePrevious=$periodInDaysPrevious*$pricePerDayPrevious;
    						$periodPriceCurrent=$periodInDaysCurrent*$pricePerDayCurrent;
    						//echo "periodPrice :".$periodPrice."</br>";
    						if($requests[$i]["action"]==="terminate"){
    							$periodPriceCurrent=0;
    						}
    						$orderChildTemp = array();
    						$orderChildTemp["beginDate"] = $requests[$i-1]["action_on_date"];
    						$orderChildTemp["endDate"]=$requests[$i]["action_on_date"];
    						$orderChildTemp["daysPrevious"]=$periodInDaysPrevious;
    						$orderChildTemp["daysCurrent"]=$periodInDaysCurrent;
    						$orderChildTemp["pricePrevious"]=$periodPricePrevious;
    						$orderChildTemp["priceCurrent"]=$periodPriceCurrent;
    						$orderChildTemp["pricePreviousActual"]=$requests[$i-1]["product_price"];
    						$orderChildTemp["priceCurrentActual"]=$requests[$i]["product_price"];
    						if($i+1===sizeof($requests))
    							$orderChildTemp["priceDifference"]=0;
    						else
    							$orderChildTemp["priceDifference"]=$periodPriceCurrent-((float)$requests[$i-1]["product_price"]-$periodPricePrevious);
    						$orderChildTemp["action_previous"]=$requests[$i-1]["action"];
    						$orderChildTemp["action_current"]=$requests[$i]["action"];
                $orderChildTemp["fees_charged_previous"]=$requests[$i-1]["fees_charged"];
    						$orderChildTemp["fees_charged_current"]=$requests[$i]["fees_charged"];
    						$orderChildTemp["product_title_previous"]=$requests[$i-1]["product_title"];
    						$orderChildTemp["product_title_current"]=$requests[$i]["product_title"];
    						$orderChildTemp["product_category_previous"]=$requests[$i-1]["product_category"];
    						$orderChildTemp["product_category_current"]=$requests[$i]["product_category"];
    						$orderChildTemp["product_subscription_type_previous"]=$requests[$i-1]["product_subscription_type"];
    						$orderChildTemp["product_subscription_type_current"]=$requests[$i]["product_subscription_type"];
    						array_push($productPrices,$orderChildTemp);
    					}
    				$orderChild["yearlyInvoice"]=$productPrices;
    		$totalPriceWoR=$productPrices[sizeof($productPrices)-2]["priceDifference"];
        $fees=0;
        if($productPrices[sizeof($productPrices)-1]["action_previous"]==="terminate")
        {
          $fees=$productPrices[sizeof($productPrices)-1]["fees_charged_previous"];//82;
        }
        else if($productPrices[sizeof($productPrices)-1]["action_previous"]==="moving")
        {
          $fees=$productPrices[sizeof($productPrices)-1]["fees_charged_previous"];//82;
        }
        else if($productPrices[sizeof($productPrices)-1]["action_previous"]==="change_speed")
        {
          $fees=$productPrices[sizeof($productPrices)-1]["fees_charged_previous"];//7;
        }
        $totalPriceWoT=$totalPriceWoR+$fees;
    		$qst_tax=abs($totalPriceWoT)*0.09975;
    		$gst_tax=abs($totalPriceWoT)*0.05;
    		$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
    		$totalPriceWT7=$totalPriceWT;
    		if($productPrices[sizeof($productPrices)-2]["action_current"]!=="terminate")
    		$totalPriceWT7=$totalPriceWT+7; // change speed fee
        $monthInfo["total_price_with_out_router"]=round($totalPriceWoR,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    		$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    		$monthInfo["product_price"]=round($productPrices[sizeof($productPrices)-2]["pricePreviousActual"],2, PHP_ROUND_HALF_UP);
    		$monthInfo["additional_service_price"]=0;
    		$monthInfo["setup_price"]=0;
    		$monthInfo["modem_price"]=0;
    		$monthInfo["router_price"]=0;
        $monthInfo["static_ip_price"]=0;
    		$monthInfo["remaining_days_price"]=0;
    		$monthInfo["remaining_days"]=$productPrices[sizeof($productPrices)-2]["daysCurrent"];
    		$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    		$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    		$monthInfo["adapter_price"]=0;
    		$monthInfo["product_title"]=$productPrices[sizeof($productPrices)-2]["product_title_current"];
    		$monthInfo["days"]=$productPrices[sizeof($productPrices)-2]["daysCurrent"];
    		$monthInfo["from"]="request";
    		$monthInfo["action"]=$productPrices[sizeof($productPrices)-2]["action_current"];
    				/*
    				$monthInfo["product_price_previous"]=$productPrices[sizeof($productPrices)-1]["pricePrevious"];
    				$monthInfo["product_price"]=$productPrices[sizeof($productPrices)-1]["priceCurrent"];
    				$monthInfo["product_price_difference"]=$productPrices[sizeof($productPrices)-1]["priceDifference"];
    				$monthInfo["days"]=$productPrices[sizeof($productPrices)-1]["daysCurrent"];
    				$monthInfo["days_2"]=$productPrices[sizeof($productPrices)-1]["daysPrevious"];
    				$monthInfo["product_title_2"]=$productPrices[sizeof($productPrices)-1]["product_title_current"];
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
    				$monthInfo["total_price"]=(float)$monthInfo["product_price_difference"];
    				$monthInfo["from"]="request";
    				*/
    				/*
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
    				$monthInfo["from"]="request";
    			}*/
    			}
          ////////////////// end update month info
    			//array_push($requests,$requestChild);
    		}
    		//echo "first condition".(int)$postDate->format('m')."!==".(int)$start_active_date->format('m')."&&".(int)$postDate->format('Y')."!==".(int)$start_active_date->format('Y');
    		if(
    		(!$hasRequestInSameMonth)&& // there are no requests
    		!((int)$postDate->format('m')===(int)$start_active_date->format('m') && (int)$postDate->format('Y')===(int)$start_active_date->format('Y')) &&// and it is not start_active_date same month
    		!((int)$postDate->format('m')===(int)$recurring_date->format('m') && (int)$postDate->format('Y')===(int)$recurring_date->format('Y')))// and it is not recurring_date same month
    		{ // then value should be zero for all prices at this month
          $monthInfo["total_price_with_out_router"]=0;
    			$monthInfo["total_price_with_out_tax"]=0;
    			$monthInfo["total_price_with_tax"]=0;
    			$monthInfo["total_price_with_tax_p7"]=0;
    			$monthInfo["product_price"]=0;
    			$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			$monthInfo["modem_price"]=0;
    			$monthInfo["router_price"]=0;
          $monthInfo["static_ip_price"]=0;
    			$monthInfo["plan"]=$order_row["plan"];
    			$monthInfo["modem"]=$order_row["modem"];
    			$monthInfo["router"]=$order_row["router"];
          $monthInfo["static_ip"]=$order_row["static_ip"];
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=0;
    			$monthInfo["gst_tax"]=0;
    			$monthInfo["adapter_price"]=0;
    			$monthInfo["product_title"]=$order_row["product_title"];
    			$monthInfo["days"]=$startEndRecurringDate["start_date"]->diff($startEndRecurringDate["end_date"])->days;
    			$monthInfo["from"]="recurring";
    			$monthInfo["action"]="recurring";
    		}
    		array_push($monthsInfo,$monthInfo);
    		$orderChild["requests"]=$requests;
    		$orderChild["monthInfo"]=$monthsInfo;
    		array_push($orders,$orderChild);
    	}
    return $orders;
    }
    ///////////////////////////////
    ///////// function to get orders with monthly subscibtion and requests for cutomer in specific month with all month invoice
    ///////////////////////////////
    public function orders_by_month($customer_id,$year,$month) {
        $this->query("SET CHARACTER SET utf8");
    $query="SELECT orders.order_id as id,
        orders.*,order_options.*,merchantrefs.*
        ,resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name'
        from orders
        INNER JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
        INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
        Left join order_options on orders.order_id= order_options.order_id
        LEFT join merchantrefs on orders.order_id= merchantrefs.order_id
        where product_subscription_type='monthly' and
    	orders.customer_id=".$customer_id;
      //echo $query;
    $ordersResult = $this->query($query);
    $orders=array();
    while ($order_row = $this->fetch_assoc($ordersResult)) {
    	$monthsInfo=array();
      $tempDate = new DateTime($year."-".$month."-01");
      $postDate = new DateTime($year."-".$month."-".$tempDate->format( 't' ));
    	$orderChild = array();
      $order_row["order_id"]=$order_row["id"];
      $orderChild["displayed_order_id"]=$order_row["order_id"];
      $order_id=$order_row["order_id"];
      if ((int) $order_id > 10380)
          $orderChild["displayed_order_id"] = (((0x0000FFFF & (int) $order_id) << 16) + ((0xFFFF0000 & (int) $order_id) >> 16));
    	$orderChild["order_id"]=$order_row["order_id"];
      /// discount fields
      $orderChild["discount"]=$order_row["discount"];
      $orderChild["discount_duration"]=$order_row["discount_duration"];
      $orderChild["free_router"]=$order_row["free_router"];
      $orderChild["free_modem"]=$order_row["free_modem"];
      $orderChild["free_adapter"]=$order_row["free_adapter"];
      $orderChild["free_installation"]=$order_row["free_installation"];
      $orderChild["free_transfer"]=$order_row["free_transfer"];
      ///////////
      ////reseller commission percentage
      $orderChild["reseller_commission_percentage"]=(int)$order_row["reseller_commission_percentage"];
      ///////////
      $orderChild["join_type"]=$order_row["join_type"];
      $orderChild["reseller_name"]=$order_row["reseller_name"];
      $orderChild["customer_name"]=$order_row["customer_name"];
    	$orderChild["creation_date"] = $order_row["creation_date"];
      $orderChild["total_price"]=is_numeric($order_row["total_price"])?$order_row["total_price"]:0;
    	$orderChild["product_price"]=is_numeric($order_row["product_price"])?$order_row["product_price"]:0;
    	$orderChild["additional_service_price"]=is_numeric($order_row["additional_service_price"])?$order_row["additional_service_price"]:0;
    	$orderChild["setup_price"]=is_numeric($order_row["setup_price"])?$order_row["setup_price"]:0;
    	$orderChild["modem_price"]=is_numeric($order_row["modem_price"])?$order_row["modem_price"]:0;
    	$orderChild["router_price"]=is_numeric($order_row["router_price"])?$order_row["router_price"]:0;
      $orderChild["static_ip_price"]=is_numeric($order_row["static_ip_price"])?$order_row["static_ip_price"]:0;
    	$orderChild["remaining_days_price"]=is_numeric($order_row["remaining_days_price"])?$order_row["remaining_days_price"]:0;
    	$orderChild["qst_tax"]=is_numeric($order_row["qst_tax"])?$order_row["qst_tax"]:0;
    	$orderChild["gst_tax"]=is_numeric($order_row["gst_tax"])?$order_row["gst_tax"]:0;
    	$orderChild["adapter_price"]=is_numeric($order_row["adapter_price"])?$order_row["adapter_price"]:0;
    	$orderChild["plan"]=$order_row["plan"];
    	$orderChild["modem"]=$order_row["modem"];
      $orderChild["additional_service"]=$order_row["additional_service"];
    	$orderChild["router"]=$order_row["router"];
      $orderChild["static_ip"]=$order_row["static_ip"];
    	$orderChild["cable_subscriber"]=$order_row["cable_subscriber"];
    	$orderChild["current_cable_provider"]=$order_row["current_cable_provider"];
    	$orderChild["cancellation_date"]=$order_row["cancellation_date"];
    	$orderChild["installation_date_1"]=$order_row["installation_date_1"];
    	$orderChild["actual_installation_date"]=$order_row["actual_installation_date"];
    	$orderChild["product_title"]=$order_row["product_title"];
    	$orderChild["product_category"]=$order_row["product_category"];
      $orderChild["payment_method"]="Cash on Delivery";
      if(strpos($order_row["merchantref"], 'cache_on_delivery') === false)
      {
        $orderChild["payment_method"]="VISA";
      }
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
    	else // if not internet nor phone then ignore for now
    	{
    		continue;
    	}
    	$start_active_date = new DateTime($orderChild["start_active_date"]);
      if(
    		((int)$postDate->format('Y')<(int)$start_active_date->format('Y'))
    		||
    		((int)$postDate->format('m')<(int)$start_active_date->format('m') && (int)$postDate->format('Y')===(int)$start_active_date->format('Y'))
    	)
    		continue;
      $recurring_date=null;
    	if(((int)$start_active_date->format('d'))>1)
    	{
    		$recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
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
    	$remaining_days=(int)$start_active_date->format('t')-(int)$start_active_date->format('d')+1;
    	$monthDays=(int)$start_active_date->format('t');
    	$oneDayPrice=(float)$orderChild["product_price"]/(int)$monthDays;
    	//$remaining_days_price=$oneDayPrice*$remaining_days;
      $oneDayPrice=(float)$orderChild["product_price"]/(int)$monthDays;
      $oneDayPriceRouter=(float)$orderChild["router_price"]/(int)$monthDays;
      $oneDayPriceStaticIP=(float)$orderChild["static_ip_price"]/(int)$monthDays;
      $oneDayPriceAdditionalPrice=(float)$orderChild["additional_service_price"]/(int)$monthDays;
      $remaining_days_price=$oneDayPrice*$remaining_days;
      $remaining_days_price_router=$oneDayPriceRouter*$remaining_days;
      $remaining_days_price_static_ip=$oneDayPriceStaticIP*$remaining_days;
      $remaining_days_price_additional_price=$oneDayPriceAdditionalPrice*$remaining_days;
      if((int)$start_active_date->format('d')===1)
        $remaining_days_price_all=0;
        else {
          $remaining_days_price_all=$remaining_days_price+$remaining_days_price_router+$remaining_days_price_additional_price+$remaining_days_price_static_ip;
        }
    	$product_price=(float)$orderChild["product_price"];
    	$additional_service_price=(float)$orderChild["additional_service_price"];
    	$setup_price=(float)$orderChild["setup_price"];
    	$modem_price=(float)$orderChild["modem_price"];
    	$router_price=(float)$orderChild["router_price"];
      $static_ip_price=(float)$orderChild["static_ip_price"];
    	$adapter_price=(float)$orderChild["adapter_price"];
    	/*
    	echo "</br> remaining_days_price: ".$remaining_days_price;
    	echo "</br> product_price: ".$product_price;
    	echo "</br> additional_service_price: ".$additional_service_price;
    	echo "</br> setup_price: ".$setup_price;
    	echo "</br> modem_price: ".$modem_price;
    	echo "</br> router_price: ".$router_price;
    	echo "</br> adapter_price: ".$adapter_price;
    	*/
    	$days=$recurring_date->diff($start_active_date)->days;
      //commission base amount
      $totalPriceWoR=$remaining_days_price_all+$product_price;
      // subtotal
    	$totalPriceWoT=$totalPriceWoR+$additional_service_price+$setup_price+$modem_price+$router_price+$adapter_price+$static_ip_price;
      $qst_tax=$totalPriceWoT*0.09975;
      $gst_tax=$totalPriceWoT*0.05;
      $monthInfo["additional_service_price"]=$orderChild["additional_service_price"];
      $monthInfo["setup_price"]=$orderChild["setup_price"];
      $monthInfo["modem_price"]=$orderChild["modem_price"];
      $monthInfo["router_price"]=$orderChild["router_price"];
      $monthInfo["plan"]=$orderChild["plan"];
      $monthInfo["modem"]=$orderChild["modem"];
      $monthInfo["router"]=$orderChild["router"];
      $monthInfo["static_ip"]=$orderChild["static_ip"];
      $monthInfo["static_ip_price"]=$orderChild["static_ip_price"];
      $monthInfo["remaining_days_price"]=round($remaining_days_price_all,2, PHP_ROUND_HALF_UP);
      $monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
      $monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
      $monthInfo["adapter_price"]=$orderChild["adapter_price"];
      $monthInfo["product_title"]=$orderChild["product_title"];
      $monthInfo["days"]=$days;
      $monthInfo["action"]="order";
      if($recurring_date->format('Y')<$year
        || ($recurring_date->format('Y')===$year && $recurring_date->format('m')<=$month)
        )
        {
          $action="recurring";
          if($orderChild["router"]==='rent' || $orderChild["router"]==='rent_hap_lite')
          {
            $action=$action.", Router Rent";
            $monthInfo["router_price"]=$router_price;
          }
          else
          {
            $monthInfo["router_price"]=0;
          }
          if($orderChild["static_ip"]==='yes')
          {
            $action=$action.", Static IP";
            $monthInfo["static_ip_price"]=$static_ip_price;
          }
          else
          {
            $monthInfo["static_ip_price"]=0;
          }
          if($orderChild["additional_service"]==='yes')
          {
            $action=$action.", Additional Service";
            $monthInfo["additional_service_price"]=$orderChild["additional_service_price"];
          }
          else
          {
            $monthInfo["additional_service_price"]=0;
          }
          $totalPriceWoR=$product_price;
          $totalPriceWoT=$product_price+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
          $qst_tax=$totalPriceWoT*0.09975;
          $gst_tax=$totalPriceWoT*0.05;
          $tempPostDate = new DateTime($year."-".$month."-01");
          $days=(int)$tempPostDate->format( 't' );
          //$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			$monthInfo["modem_price"]=0;
    			$monthInfo["plan"]=$orderChild["plan"];
    			$monthInfo["modem"]=$orderChild["modem"];
    			$monthInfo["router"]=$orderChild["router"];
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["adapter_price"]=$orderChild["adapter_price"];
    			$monthInfo["product_title"]=$orderChild["product_title"];
    			$monthInfo["days"]=$days;
          $monthInfo["action"]=$action;
        }
    	$totalPriceWT=$totalPriceWoT+$qst_tax+$gst_tax;
    	$totalPriceWT7=$totalPriceWT;
      $monthInfo["total_price_with_out_router"]=round($totalPriceWoR,2, PHP_ROUND_HALF_UP);
    	$monthInfo["total_price_with_out_tax"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    	$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    	$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    	$monthInfo["product_price"]=round($orderChild["product_price"],2, PHP_ROUND_HALF_UP);

      //////////////////////// check if there is terminate request before active date
      $requestTerminateQuery="SELECT * from requests where
      `action` in ('terminate') and
      order_id=".$orderChild["order_id"]."
      and date(action_on_date) <='".$start_active_date->format('Y-m-d')."'
      and verdict = 'approve' order by action_on_date DESC LIMIT 1";
      $requestTerminateResult = $this->query($requestTerminateQuery);


      while ($request_terminate_row = $this->fetch_assoc($requestTerminateResult)) {

          ////////////////// update month info
          if($request_terminate_row["action"]==="terminate")
          {
            $fees_charged=(float)$request_terminate_row["fees_charged"];
            $orderChild["recurring_date"]="0000-00-00";
            $monthInfo["total_price_with_out_router"]=0;
            $monthInfo["total_price_with_out_tax"]=0;
            $monthInfo["total_price_with_tax"]=$fees_charged;
            $monthInfo["total_price_with_tax_p7"]=$fees_charged;
            $monthInfo["product_price"]=0;
            $monthInfo["additional_service_price"]=0;
            $monthInfo["setup_price"]=0;
            $monthInfo["router_price"]=0;
            $monthInfo["static_ip_price"]=0;
            $monthInfo["modem_price"]=0;
            $monthInfo["remaining_days_price"]=0;
            $monthInfo["qst_tax"]=0;
            $monthInfo["gst_tax"]=0;
            $monthInfo["adapter_price"]=0;
            $monthInfo["total_price"]=0;
            $monthInfo["action"]="terminated";
          }
          array_push($monthsInfo,$monthInfo);
          $orderChild["requests"]=[];
          $orderChild["monthInfo"]=$monthsInfo;
          array_push($orders,$orderChild);

         return $orders;
        }

      /////////////////// end get month infor from order
      ////////////////// check if there is any request before the selected date, if yes get it's info instead of order info
      $change_speed_fee=0;// check if request before one month and after 1st day, if yes set this value to 7$
    	$date = new DateTime($year."-".$month."-01 00:00:00");
    	$monthDays= (int) $date->format( 't' );


    	//if($requestResult->num_rows===0)
    	//{
        $requestQuery="SELECT * from requests where
        `action` in ('terminate','change_speed','moving','swap_modem') and
    		order_id=".$orderChild["order_id"]."
    		and (year(action_on_date) <".$year."
    		or (year(action_on_date) =".$year." and month(action_on_date) <".$month." ))
    		and verdict = 'approve' order by action_on_date DESC LIMIT 1";
    		$requestResult = $this->query($requestQuery);
    	//}
    	$requests=array();
    	$hasRequest=false;
    	while ($request_row = $this->fetch_assoc($requestResult)) {
    		$hasRequest=true;
    		$requestChild = array();
    		$requestChild["creation_date"] = $request_row["creation_date"];
        $action="recurring";
        /*
        $change_speed_date=new DateTime($request_row["action_on_date"]);
    		$interval = new DateInterval('P1M');
    		$change_speed_date->add($interval);
          if(((int)$change_speed_date->format('Y')===(int)$year)
         &&(int)$change_speed_date->format('m')===(int)$month)
         {
           $action=$action.", change speed";
           $change_speed_fee=7;
         }
         */
    		$requestChild["action_on_date"] = $request_row["action_on_date"];
    		$requestChild["verdict_date"] = $request_row["verdict_date"];
    		$requestChild["verdict"] = $request_row["verdict"];
    		$requestChild["product_price"]=$request_row["product_price"];
    		$requestChild["action"]=$request_row["action"];
    		$requestChild["product_title"]=$request_row["product_title"];
    		$requestChild["product_category"]=$request_row["product_category"];
    		$requestChild["product_subscription_type"]=$request_row["product_subscription_type"];
        ////////////////// update month info
    		if($request_row["action"]==="terminate")
    		{
          $orderChild["recurring_date"]="0000-00-00";
          $monthInfo["total_price_with_out_router"]=0;
    			$monthInfo["total_price_with_out_tax"]=0;
    			$monthInfo["total_price_with_tax"]=0;
    			$monthInfo["total_price_with_tax_p7"]=0;
    			$monthInfo["product_price"]=0;
    			$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			$monthInfo["router_price"]=0;
          $monthInfo["static_ip_price"]=0;
    			$monthInfo["modem_price"]=0;
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=0;
    			$monthInfo["gst_tax"]=0;
    			$monthInfo["adapter_price"]=0;
    			$monthInfo["total_price"]=0;
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]=$monthDays;
    			$monthInfo["action"]="terminated";
    		}
    		else
    		{
    			$totalPriceWoT=(float)$request_row["product_price"]+$change_speed_fee;
          if($orderChild["router"]==='rent' || $orderChild["router"]==='rent_hap_lite')
          {
            $action=$action.", Router rent";
             $monthInfo["router_price"]=(float)$orderChild["router_price"];
           }
          else {
            $monthInfo["router_price"]=0;
          }
          if($orderChild["static_ip"]==='yes')
          {
            $action=$action.", Static IP";
             $monthInfo["static_ip_price"]=(float)$orderChild["static_ip_price"];
           }
          else {
            $monthInfo["static_ip_price"]=0;
          }
          if($orderChild["additional_service"]==='yes')
          {
            $action=$action.", Additional service";
             $monthInfo["additional_service_price"]=(float)$orderChild["additional_service_price"];
           }
          else {
            $monthInfo["additional_service_price"]=0;
          }
          $subtotal=$totalPriceWoT+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
    			$qst_tax=$totalPriceWoT*0.09975;
    			$gst_tax=$totalPriceWoT*0.05;
    			$totalPriceWT=$subtotal+$qst_tax+$gst_tax;
    			$totalPriceWT7=$totalPriceWT;
          $monthInfo["change_speed_fee"]=$change_speed_fee;
          $monthInfo["total_price_with_out_router"]=round((float)$request_row["product_price"],2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    			$monthInfo["product_price"]=(float)$request_row["product_price"];
    			//$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			if($monthInfo["router"]!=="rent" &&  $monthInfo["router"]!=="rent_hap_lite")
    				$monthInfo["router_price"]=0;
    			if($monthInfo["modem"]!=="rent" )
    				$monthInfo["modem_price"]=0;
          if($monthInfo["static_ip"]!=="yes" )
    				$monthInfo["static_ip_price"]=0;
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["adapter_price"]=0;
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]=$monthDays;
          $monthInfo["action"]=$action;
    		}
        ////////////////// end update month info
    		array_push($requests,$requestChild);
    	}
      ///////////////// check if there is request in the same month as the requested date
    	$requestResult = $this->query("SELECT * from requests where
        `action` in ('terminate','change_speed','moving','swap_modem') and
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
    		/*if($request_row["action"]==="terminate")
    		{
    			$monthInfo["total_price_with_out_tax"]=0;
    			$monthInfo["total_price_with_tax"]=0;
    			$monthInfo["total_price_with_tax_p7"]=0;
    			$monthInfo["product_price"]=0;
    			$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			$monthInfo["router_price"]=0;
    			$monthInfo["modem_price"]=0;
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=0;
    			$monthInfo["gst_tax"]=0;
    			$monthInfo["adapter_price"]=0;
    			$monthInfo["total_price"]=0;
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]=$monthDays;
    			$monthInfo["action"]="terminated";
    		}
    		else
    		{*/
        $start_month_between_start_and_recurring = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
        $interval = new DateInterval('P1M');
        $start_month_between_start_and_recurring->add($interval);
        $start_day_of_post_month = new DateTime($postDate->format('Y')."-".$postDate->format('m')."-01 00:00:00");
        /*
        print_r($requests);
        echo "size of requests".sizeof($requests)."</br>";
        echo "hasRequest: ".((sizeof($requests)>0)?"true":"fals")."</br>";
        echo "start_active_date: ".$start_active_date->format('Y-m-d')."</br>";
        echo "start_day_of_post_month: ".$start_day_of_post_month->format('Y-m-d')."</br>";
        echo "start_month_between_start_and_recurring: ".$start_month_between_start_and_recurring->format('Y-m-d')."</br>";
        */
    		if( ( (int)$this_action_on_date->format('d')>1 && sizeof($requests)>0)
    			||
    			((int)$this_action_on_date->format('d')>1 &&(
    				(int)$recurring_date->format('Y') < (int)$year
    				||
    				((int)$recurring_date->format('Y') === (int)$year && (int)$recurring_date->format('m') <= (int)$month)
            )
    			)
          ||/// check if post month greater than start_active_date month and before recurring_date month so show zeros
          ((int)$start_active_date->format('d')>1 //and there is remaining_days
          && (// and posted date between start active date and recurring Date
              $start_day_of_post_month->getTimestamp() > $start_active_date->getTimestamp() &&
              $start_day_of_post_month->getTimestamp() <= $start_month_between_start_and_recurring->getTimestamp())
              )
    		)
    		{/*
          / if there is request in the middle of the month
          then we have to split our price calculation as follow:
          1- calculate the paid prices for product and it's tax.
          2- calculate the days used for the current product.
          3- calculate prices for product and tax for the used days.
          4- caculate the days for the remining days that will be used using the new speed.
          5- calculate prices for product and tax for the remaining days.
          6- calculate the difference in product prices.
          7- calualte the difference in tax prices.
          */
          //echo "hi";
          $fees_charged=(float)$request_row["fees_charged"];
          $actionTax=$fees_charged;//change speed fee
    			$this_request_days=$monthDays-(int)$this_action_on_date->format('d')+1;
    			$previous_days=$monthDays-$this_request_days;
          $this_product_price= (((float)$request_row["product_price"])/$monthDays)*$this_request_days;
          if($request_row["action"]==="terminate")
          {
            $actionTax=$fees_charged;//termination fee
            //echo $this_action_on_date->format('Y-m-d')."<".$start_active_date->format('Y-m-d')."</br>";
            if($this_action_on_date<$start_active_date)
              $actionTax=0;//termination fee
            $this_product_price= 0;
            $orderChild["recurring_date"]="0000-00-00";
            $previous_product_price= (((float)$monthInfo["product_price"])/$monthDays)*$previous_days;
            $paid_qst_tax=abs((float)$monthInfo["product_price"])*0.09975;
            $paid_gst_tax=abs((float)$monthInfo["product_price"])*0.05;
            $paid_Tax=$paid_qst_tax+$paid_gst_tax;
            $previous_qst_tax=abs($previous_product_price)*0.09975;
            $previous_gst_tax=abs($previous_product_price)*0.05;
            $previous_Tax=$previous_qst_tax+$previous_gst_tax;
    				$priceDifference=(float)$monthInfo["product_price"]-($this_product_price+$previous_product_price);//-(float)$monthInfo["product_price"];
            $total_paid=($this_product_price+$previous_product_price);
            //$priceDifference=((float)$monthInfo["product_price"]-$previous_product_price)+((float)$request_row["product_price"]-$this_product_price);//-(float)$monthInfo["product_price"];
            $monthInfo["product_price_difference"]=$priceDifference;
    				$monthInfo["product_price_previous"]=$monthInfo["product_price"];
            $monthInfo["product_price_current"]=0;
            //$totalPriceWoT=(float)$monthInfo["product_price"]+$priceDifference;
    				$monthInfo["product_price"]=round($previous_product_price,2, PHP_ROUND_HALF_UP);;
    				$monthInfo["product_price_2"]=0;
    				$monthInfo["days"]=$previous_days;
    				$monthInfo["days_2"]=$this_request_days;
            //echo $monthInfo["product_price"]."-".$priceDifference;
            ///commission base amount: product + remaining days
    				$totalPriceWoT=$total_paid;
            $request_row["action"]="terminated";
            // subtotal : commission+all addition prices+fees
            if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
            {
    					$monthInfo["router_price"]=0;
            }
            else {
              $monthInfo["router_price"]= (((float)$orderChild["router_price"])/$monthDays)*$previous_days;
              $request_row["action"]="terminated, Router Rent";
            }
            if($orderChild["additional_service"]==='yes')
            {
               $monthInfo["additional_service_price"]=(((float)$orderChild["additional_service_price"])/$monthDays)*$previous_days;
               $request_row["action"]=$request_row["action"].", Additional service";
             }
            else {
              $monthInfo["additional_service_price"]=0;
            }
            if($orderChild["static_ip"]==='yes')
            {
               $monthInfo["static_ip_price"]=(((float)$orderChild["static_ip_price"])/$monthDays)*$previous_days;
               $request_row["action"]=$request_row["action"].", Static IP";
             }
            else {
              $monthInfo["static_ip_price"]=0;
            }
            $action=$request_row["action"];
            $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
            $total_qst_tax=abs((float)$subtotal)*0.09975;
            $total_gst_tax=abs((float)$subtotal)*0.05;
            $total_tax=$total_qst_tax +$total_gst_tax;
    				$totalPriceWT=$subtotal+$total_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$actionTax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_title_2"]=$monthInfo["product_title"];
    				//$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($previous_qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($previous_gst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["qst_tax_2"]=round($total_qst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["gst_tax_2"]=round($total_gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
          }
          else if($request_row["action"]==="moving" || $request_row["action"]==="swap_modem"){
            $actionTax=$fees_charged;


            if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
    					$monthInfo["router_price"]=0;
            if($monthInfo["static_ip"]!=="yes")
    					$monthInfo["static_ip_price"]=0;
            $monthInfo["product_price"]=(float)$request_row["product_price"];
            $action="recurring, ".$request_row["action"];
            if((int)$monthInfo["router_price"]>0)
            {
              $action=$action.", Router rent";
            }
            if($orderChild["additional_service"]==='yes')
            {
               $monthInfo["additional_service_price"]=(((float)$orderChild["additional_service_price"])/$monthDays)*$previous_days;
               $action=$action.", additional Service";
             }
            else {
              $monthInfo["additional_service_price"]=0;
            }
            if($orderChild["static_ip"]==='yes')
            {
               $monthInfo["static_ip_price"]=(((float)$orderChild["static_ip_price"])/$monthDays)*$previous_days;
               $action=$action.", Static IP";
             }
            else {
              $monthInfo["static_ip_price"]=0;
            }
            $d = new DateTime($recurring_date->format("Y-m-d"));
            $d->modify('first day of previous month');

            if((int)$this_action_on_date->format('d')>1
            &&((int)$d->format('m')==(int)$month)
      			)//if before first recurring by one month
            {
              $this_product_price=0;
            }
            else{
              $this_product_price=(float)$request_row["product_price"];
            }
    				$totalPriceWoT=$this_product_price;
            $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
            $qst_tax=$subtotal*0.09975;
    				$gst_tax=$subtotal*0.05;
    				$totalPriceWT=$subtotal+$qst_tax+$gst_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$actionTax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_title"]=$request_row["product_title"];
    				$monthInfo["days"]=$monthDays;
    				$monthInfo["product_title_2"]=$request_row["product_title"];
    				//$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
          }
          else{///////// if change speed
    				$previous_product_price= (((float)$monthInfo["product_price"])/$monthDays)*$previous_days;
            $paid_qst_tax=abs((float)$monthInfo["product_price"])*0.09975;
            $paid_gst_tax=abs((float)$monthInfo["product_price"])*0.05;
            $paid_Tax=$paid_qst_tax+$paid_gst_tax;
            $previous_qst_tax=abs($previous_product_price)*0.09975;
            $previous_gst_tax=abs($previous_product_price)*0.05;
            $previous_Tax=$previous_qst_tax+$previous_gst_tax;
    				$priceDifference=(float)$monthInfo["product_price"]-($this_product_price+$previous_product_price);//-(float)$monthInfo["product_price"];
            $total_paid=($this_product_price+$previous_product_price);
            //$priceDifference=((float)$monthInfo["product_price"]-$previous_product_price)+((float)$request_row["product_price"]-$this_product_price);//-(float)$monthInfo["product_price"];
            $monthInfo["product_price_difference"]=$priceDifference;
    				$monthInfo["product_price_previous"]=$monthInfo["product_price"];
            $monthInfo["product_price_current"]=(float)$request_row["product_price"];
            //$totalPriceWoT=(float)$monthInfo["product_price"]+$priceDifference;
    				$monthInfo["product_price"]=round($previous_product_price,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_price_2"]=round($this_product_price,2, PHP_ROUND_HALF_UP);
    				$monthInfo["days"]=$previous_days;
    				$monthInfo["days_2"]=$this_request_days;
            //echo $monthInfo["product_price"]."-".$priceDifference;
            ///commission base amount: product + remaining days
    				$totalPriceWoT=$total_paid;
            // subtotal : commission+all addition prices+fees
            if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
            {
    					$monthInfo["router_price"]=0;
            }
            else {
              $request_row["action"]=$request_row["action"].", Router Rent";
            }
            if($orderChild["additional_service"]==='yes')
            {
               $monthInfo["additional_service_price"]=(float)$orderChild["additional_service_price"];
               $request_row["action"]=$request_row["action"].", Additional service";
             }
            else {
              $monthInfo["additional_service_price"]=0;
            }
            if($orderChild["static_ip"]==='yes')
            {
               $monthInfo["static_ip_price"]=(float)$orderChild["static_ip_price"];
               $request_row["action"]=$request_row["action"].", Static IP";
             }
            else {
              $monthInfo["static_ip_price"]=0;
            }
            $action=$request_row["action"];
            $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
            $total_qst_tax=abs((float)$subtotal)*0.09975;
            $total_gst_tax=abs((float)$subtotal)*0.05;
            $total_tax=$total_qst_tax +$total_gst_tax;
    				$totalPriceWT=$subtotal+$total_tax;
    				$totalPriceWT7=$totalPriceWT;
            $monthInfo["change_speed_fee"]=$actionTax;
            $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    				$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    				$monthInfo["product_title_2"]=$request_row["product_title"];
    				//$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				if($monthInfo["modem"]!=="rent" )
    					$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=round($previous_qst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["gst_tax"]=round($previous_gst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["qst_tax_2"]=round($total_qst_tax,2, PHP_ROUND_HALF_UP);
            $monthInfo["gst_tax_2"]=round($total_gst_tax,2, PHP_ROUND_HALF_UP);
    				$monthInfo["adapter_price"]=0;
          }
    		}
    		else{
          $fees_charged=(float)$request_row["fees_charged"];
          $actionTax=$change_speed_fee+$fees_charged;//change speed fee

          $this_product_price=(float)$request_row["product_price"];
          if($monthInfo["router"]!=="rent" && $monthInfo["router"]!=="rent_hap_lite" )
    				$monthInfo["router_price"]=0;
          if($monthInfo["static_ip"]!=="yes")
    				$monthInfo["static_ip_price"]=0;
          $monthInfo["product_price"]=(float)$request_row["product_price"];
          if($request_row["action"]==="terminate")
          {
            $actionTax=$change_speed_fee+$fees_charged;//termination fee
            if($this_action_on_date<$start_active_date)
              $actionTax=0;//termination fee
            $this_product_price= 0;
            $monthInfo["router_price"]=0;
            $monthInfo["static_ip_price"]=0;
            $monthInfo["product_price"]=0;
            $orderChild["recurring_date"]="0000-00-00";
          }
          else if($request_row["action"]==="moving")
          {
            $actionTax=$fees_charged;
          }
          $action=$request_row["action"];
          if((int)$monthInfo["router_price"]>0)
          {
            $action=$action.", Router rent";
          }
          if($orderChild["static_ip"]==='yes')
          {
             $monthInfo["static_ip_price"]=(((float)$orderChild["static_ip_price"])/$monthDays)*$previous_days;
             $action=$action.", Static IP";
           }
          else {
            $monthInfo["static_ip_price"]=0;
          }
          if($orderChild["additional_service"]==='yes')
          {
             $monthInfo["additional_service_price"]=(((float)$orderChild["additional_service_price"])/$monthDays)*$previous_days;
             $action=$action.", additional Service";
           }
          else {
            $monthInfo["additional_service_price"]=0;
          }
    			$totalPriceWoT=$this_product_price;
          $subtotal=$totalPriceWoT+$actionTax+(float)$monthInfo["router_price"]+(float)$monthInfo["additional_service_price"]+(float)$monthInfo["static_ip_price"];
          $qst_tax=$subtotal*0.09975;
    			$gst_tax=$subtotal*0.05;
    			$totalPriceWT=$subtotal+$qst_tax+$gst_tax;
    			$totalPriceWT7=$totalPriceWT;
          $monthInfo["change_speed_fee"]=$actionTax;
          $monthInfo["total_price_with_out_router"]=round($totalPriceWoT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_out_tax"]=round($subtotal,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax"]=round($totalPriceWT,2, PHP_ROUND_HALF_UP);
    			$monthInfo["total_price_with_tax_p7"]=round($totalPriceWT7,2, PHP_ROUND_HALF_UP);
    			$monthInfo["product_title"]=$request_row["product_title"];
    			$monthInfo["days"]=$monthDays;
    			$monthInfo["product_title_2"]=$request_row["product_title"];
    			//$monthInfo["additional_service_price"]=0;
    			$monthInfo["setup_price"]=0;
    			if($monthInfo["modem"]!=="rent" )
    				$monthInfo["modem_price"]=0;
    			$monthInfo["remaining_days_price"]=0;
    			$monthInfo["qst_tax"]=round($qst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["gst_tax"]=round($gst_tax,2, PHP_ROUND_HALF_UP);
    			$monthInfo["adapter_price"]=0;
    		}
        $monthInfo["action"]=$action;
    		//}
        ////////////////// end update month info
    		array_push($requests,$requestChild);
    	}
      $start_month_between_start_and_recurring = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
      $interval = new DateInterval('P1M');
      $start_month_between_start_and_recurring->add($interval);
      $start_day_of_post_month = new DateTime($postDate->format('Y')."-".$postDate->format('m')."-01 00:00:00");
      /// check if post month greater than start_active_date month and before recurring_date month so show zeros
      if(sizeof($requests)===0 // if no requests
      && (int)$start_active_date->format('d')>1 //and there is remaining_days
      && (// and posted date between start active date and recurring Date
          $start_day_of_post_month->getTimestamp() > $start_active_date->getTimestamp() &&
          $start_day_of_post_month->getTimestamp() <= $start_month_between_start_and_recurring->getTimestamp())
          )
          {
            /// then show zeros values
            $monthInfo["total_price_with_out_router"]=0;
    				$monthInfo["total_price_with_out_tax"]=0;
    				$monthInfo["total_price_with_tax"]=0;
    				$monthInfo["total_price_with_tax_p7"]=0;
    				$monthInfo["product_price"]=0;
    				$monthInfo["additional_service_price"]=0;
    				$monthInfo["setup_price"]=0;
    				$monthInfo["router_price"]=0;
            $monthInfo["static_ip_price"]=0;
    				$monthInfo["modem_price"]=0;
    				$monthInfo["remaining_days_price"]=0;
    				$monthInfo["qst_tax"]=0;
    				$monthInfo["gst_tax"]=0;
    				$monthInfo["adapter_price"]=0;
    				$monthInfo["total_price"]=0;
    				$monthInfo["days"]=$start_month_between_start_and_recurring->format('t');
    				$monthInfo["action"]="month after start active date";
          }
    	array_push($monthsInfo,$monthInfo);
    	$orderChild["requests"]=$requests;
    	$orderChild["monthInfo"]=$monthsInfo;
    	array_push($orders,$orderChild);
    }
     return $orders;
    }
    public function request_query_api($queryString,$fields,$child=null,$childFields=null,$child2=null,$child2Fields=null,$child3=null,$child3Fields=null,$child4=null,$child4Fields=null) {
        $requests = array();
        $this->query("SET CHARACTER SET utf8");
        $requests_result = $this->query($queryString);
        while ($request_row = $this->fetch_assoc($requests_result)) {
             $request = array();
            foreach ($fields as $key => $value)
            {
              if($key==="action")
              {
                $request[$key] = $request_row[$value];
                if($request_row[$value]==="change_speed" && is_numeric($request_row["modem_id"])  && (int)$request_row["modem_id"] >0)
                {
                  $request[$key]="swap modem and change speed";
                }
              }
              else{
                $request[$key] = $request_row[$value];
              }
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
    public function tik_monitoring_query_api($queryString, $fields, $child = null, $childFields = null, $child2 = null, $child2Fields = null, $child3 = null, $child3Fields = null, $child4 = null, $child4Fields = null) {
        $customers = array();
        $this->query("SET CHARACTER SET utf8");
        $customers_result = $this->query($queryString);
        while ($customer_row = $this->fetch_assoc($customers_result)) {
            if (isset($customers[$customer_row['customer_id']])) {
                if ($child2 != null) {
                    $customerChildArray = $customers[$customer_row['customer_id']][$child2];
                    $customerChild = array();
                    foreach ($child2Fields as $childKey => $childValue) {
                        $customerChild[$childKey] = $customer_row[$childValue];
                    }
                    array_push($customerChildArray, $customerChild);
                    $customers[$customer_row['customer_id']][$child2] = $customerChildArray;
                }
            } else {
                $customers[$customer_row['customer_id']] = array();
                foreach ($fields as $key => $value) {
                    $customers[$customer_row['customer_id']][$key] = $customer_row[$value];
                }
                if ($child != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($childFields as $childKey => $childValue) {
                        $customerChild[$childKey] = $customer_row[$childValue];
                    }
                    array_push($customerChildArray, $customerChild);
                    $customers[$customer_row['customer_id']][$child] = $customerChildArray;
                }
                if ($child2 != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($child2Fields as $childKey => $childValue) {
                        $customerChild[$childKey] = $customer_row[$childValue];
                    }
                    array_push($customerChildArray, $customerChild);
                    $customers[$customer_row['customer_id']][$child2] = $customerChildArray;
                }
                if ($child3 != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($child3Fields as $childKey => $childValue) {
                        $customerChild[$childKey] = $customer_row[$childValue];
                    }
                    array_push($customerChildArray, $customerChild);
                    $customers[$customer_row['customer_id']][$child3] = $customerChildArray;
                }
                if ($child4 != null) {
                    $customerChildArray = array();
                    $customerChild = array();
                    foreach ($child4Fields as $childKey => $childValue) {
                        $customerChild[$childKey] = $customer_row[$childValue];
                    }
                    array_push($customerChildArray, $customerChild);
                    $customers[$customer_row['customer_id']][$child4] = $customerChildArray;
                }
            }
        }
        $customersPure = array();
        foreach ($customers as $row) {
            array_push($customersPure, $row);
        }
        return $customersPure;
    }
}
