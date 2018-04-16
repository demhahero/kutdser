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
    private $db_name = 'copy_router'; //database name
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
