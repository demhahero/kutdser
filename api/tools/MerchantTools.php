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
class MerchantTools {
    
    private $objDBTools;
    private $merchantref;
    private $customer;
    private $type;
    private $order;
    private $is_credit;
    
    public function __construct($merchantref, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;
        
        $this->merchantref = $merchantref;
        $this->objDBTools = $objDBTools;
        
        $merchant_result = $this->objDBTools->query("select * from `merchantrefs` where `merchantref`='" . $this->merchantref . "'");
        $merchant_row = $this->objDBTools->fetch_assoc($merchant_result);
        $this->type = $merchant_row["type"];
        $this->is_credit = $merchant_row["is_credit"];
    }
    
    public function getMerchantRef(){
        return $this->merchantref;
    }
}
