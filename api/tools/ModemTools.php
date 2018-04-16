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
class ModemTools {

    private $objDBTools;
    private $modem_id;
    private $mac_address;
    private $type;
    private $reseller;
    private $customer;
    private $serial_number;
    private $is_ours;

    public function __construct($modem_id, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $this->modem_id = $modem_id;
        $this->objDBTools = $objDBTools;

        $modem_result = $this->objDBTools->query("select * from `modems` where `modem_id`='" . $this->modem_id . "'");
        $modem_row = $this->objDBTools->fetch_assoc($modem_result);
        $this->type = $modem_row["type"];
        $this->mac_address = $modem_row["mac_address"];
        $this->serial_number = $modem_row["serial_number"];
        $this->is_ours = $modem_row["is_ours"];

        $customer_sql = "select * from `customers` where `customer_id`='" . $modem_row["customer_id"] . "'";
        $customer_result = $this->objDBTools->query($customer_sql);
        $customer_row = $this->objDBTools->fetch_assoc($customer_result);
        $this->customer = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);

        //Get Reseller
        $customer_sql = "select * from `customers` where `customer_id`='" . $modem_row["reseller_id"] . "'";
        $customer_result = $this->objDBTools->query($customer_sql);
        $customer_row = $this->objDBTools->fetch_assoc($customer_result);
        $this->reseller = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);
    }

    public function getModemID() {
        return $this->modem_id;
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
    
    public function getSerialNumber() {
        return $this->serial_number;
    }
    
    public function setSerialNumber($serial_number) {
        $this->serial_number = $serial_number;
    }
    
    public function getMACAddress() {
        return $this->mac_address;
    }
    
    public function setMACAddress($mac_address) {
        $this->mac_address = $mac_address;
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
        $result_modem = $this->objDBTools->query("update `modems` set "
                . "`mac_address`='" . $this->mac_address . "', "
                . "`serial_number`='" . $this->serial_number . "', "
                . "`type`='" . $this->type . "', "
                . "`is_ours`='" . $this->is_ours . "', "
                . "`customer_id`='" . $this->customer->getCustomerID() . "', "
                . "`reseller_id`='" . $this->reseller->getCustomerID() . "' "
                . "where `modem_id`='" . $this->modem_id . "'");
        
        return $result_modem;
    }
    
    public function doInsert(){
        $result_modem = $this->objDBTools->query("insert into `modems` ("
                . "`mac_address`, "
                . "`serial_number`, "
                . "`type`, "
                . "`is_ours`, "
                . "`customer_id`, "
                . "`reseller_id` "
                . ") values ( "
                . "'" . $this->mac_address . "', "
                . "'" . $this->serial_number . "', "
                . "'" . $this->type . "', "
                . "'" . $this->is_ours . "', "
                . "'" . $this->customer->getCustomerID() . "', "
                . "'" . $this->reseller->getCustomerID() . "' "
                . ")");
        
        return $result_modem;
    }
    
    public function doDelete() {
        $result = $this->objDBTools->query("delete from `modems` "
                . "where `modem_id`='" . $this->modem_id . "'");

        return $result;
    }

}
