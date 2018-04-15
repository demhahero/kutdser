<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductTools
 *
 * @author breeder1
 */
class ProductTools {

    private $product_id;
    private $objDBTools;
    private $title;
    private $price;
    private $category; // internet or phone
    private $subscription_type; //yearly or monthly
    public function __construct($product_id, $objDBTools, $depth = 0) {
        if($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;
        $this->product_id = $product_id;
        $this->objDBTools = $objDBTools;
  
        $product_result = $this->objDBTools->query("SELECT * from `products` where `product_id`='" . $this->product_id . "'");
        $row_product = $this->objDBTools->fetch_assoc($product_result);
        $this->title = $row_product["title"];
        $this->price = $row_product["price"];
        $this->category = $row_product["category"];
        $this->subscription_type = $row_product["subscription_type"];
    }

    //Internet or Phone
    public function getCategory() {
        return $this->category;
    }

    public function getProductID() {
        return $this->product_id;
    }    
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getSubscriptionType() {
        return $this->subscription_type;
    }
}
