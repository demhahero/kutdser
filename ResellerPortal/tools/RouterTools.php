<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MerchantTools
 *
 * @author breeder1
 */
class RouterTools {

    private $objDBTools;
    private $router_id;
    private $type;
    private $reseller;
    private $customer;
    private $serial_number;
    private $is_ours;
    private $is_sold;

    public function __construct($router_id, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $this->router_id = $router_id;
        $this->objDBTools = $objDBTools;

        $router_result = $this->objDBTools->query("select * from `routers` where `router_id`='" . $this->router_id . "'");
        $router_row = $this->objDBTools->fetch_assoc($router_result);
        $this->type = $router_row["type"];
        $this->serial_number = $router_row["serial_number"];
        $this->is_ours = $router_row["is_ours"];
        $this->is_sold = $router_row["is_sold"];

        $customer_sql = "select * from `customers` where `customer_id`='" . $router_row["customer_id"] . "'";
        $customer_result = $this->objDBTools->query($customer_sql);
        $customer_row = $this->objDBTools->fetch_assoc($customer_result);
        $this->customer = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);

        //Get Reseller
        $customer_sql = "select * from `customers` where `customer_id`='" . $router_row["reseller_id"] . "'";
        $customer_result = $this->objDBTools->query($customer_sql);
        $customer_row = $this->objDBTools->fetch_assoc($customer_result);
        $this->reseller = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);
    }

    public function getRouterID() {
        return $this->router_id;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function getIsOurs() {
        return $this->is_ours;
    }
    
    public function setIsOurs($is_ours) {
        $this->is_ours = $is_ours;
    }
    
    public function getIsSold() {
        return $this->is_sold;
    }
    
    public function setIsSold($is_sold) {
        $this->is_sold = $is_sold;
    }
    
    public function getSerialNumber() {
        return $this->serial_number;
    }
    
    public function setSerialNumber($serial_number) {
        $this->serial_number = $serial_number;
    }
    
    public function getCustomer() {
        return $this->customer;
    }
    
    public function setCustomer($customer) {
        $this->customer = $customer;
    }
    
    public function getReseller() {
        return $this->reseller;
    }
    
    public function setReseller($reseller) {
        $this->reseller = $reseller;
    }
    
    public function doUpdate(){
        $result_router = $this->objDBTools->query("update `routers` set "
                . "`is_sold`='" . $this->is_sold . "', "
                . "`serial_number`='" . $this->serial_number . "', "
                . "`type`='" . $this->type . "', "
                . "`is_ours`='" . $this->is_ours . "', "
                . "`customer_id`='" . $this->customer->getCustomerID() . "', "
                . "`reseller_id`='" . $this->reseller->getCustomerID() . "' "
                . "where `router_id`='" . $this->router_id . "'");
        
        return $result_router;
    }
    
    public function doInsert(){
        
        $result_router = $this->objDBTools->query("insert into `routers` ("
                . "`is_sold`, "
                . "`serial_number`, "
                . "`type`, "
                . "`is_ours`, "
                . "`customer_id`, "
                . "`reseller_id` "
                . ") values ( "
                . "'" . $this->is_sold . "', "
                . "'" . $this->serial_number . "', "
                . "'" . $this->type . "', "
                . "'" . $this->is_ours . "', "
                . "'" . $this->customer->getCustomerID() . "', "
                . "'" . $this->reseller->getCustomerID() . "' "
                . ")");
        
        return $result_router;
    }
    
    public function doDelete() {
        $result = $this->objDBTools->query("delete from `routers` "
                . "where `router_id`='" . $this->router_id . "'");

        return $result;
    }

}
