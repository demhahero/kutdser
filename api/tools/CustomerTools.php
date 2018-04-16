<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerTools
 *
 * @author breeder1
 */
class CustomerTools {

    private $customer_id;
    private $objDBTools;
    private $full_name;
    private $is_reseller;
    private $address;
    private $phone;
    private $email;
    private $orders;
    private $depth;
    private $reseller_customers;
    private $merchant;
    private $reseller;
    private $address_line_1;
    private $address_line_2;
    private $postal_code;
    private $city;
    private $ip_address;
    private $note;
    private $start_date; // old system

    public function __construct($customer_id, $objDBTools, $depth = 0, $path = null) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $next_path = "";
        if ($path != null) {
            $next_path = array_shift($path);
        }

        $this->customer_id = $customer_id;
        $this->objDBTools = $objDBTools;

        $customer_result = $this->objDBTools->query("select * from `customers` where `customer_id`='" . $this->customer_id . "'");
        $customer_row = $this->objDBTools->fetch_assoc($customer_result);
        $this->full_name = $customer_row["full_name"];
        $this->address = $customer_row["address"];
        $this->phone = $customer_row["phone"];
        $this->email = $customer_row["email"];
        $this->is_reseller = $customer_row["is_reseller"];
        $this->address_line_1 = $customer_row["address_line_1"];
        $this->address_line_2 = $customer_row["address_line_2"];
        $this->city = $customer_row["city"];
        $this->postal_code = $customer_row["postal_code"];
        $this->ip_address = $customer_row["ip_address"];
        $this->note = $customer_row["note"];
        $this->start_date = $customer_row["start_date"];

        $this->orders = array();
        if ($next_path == "" || $next_path == "order") {
            $order_result = $this->objDBTools->query("select * from `orders` where `customer_id`='" . $this->customer_id . "'");
            while ($order_row = $this->objDBTools->fetch_assoc($order_result)) {
                $this->orders[] = new OrderTools($order_row["order_id"], $this->objDBTools, $this->depth, $path);
            }
        }

        $this->reseller_customers = array();
        if ($next_path == "" || $next_path == "customer") {
            if ($this->is_reseller == "1") {
                $reseller_result = $this->objDBTools->query("select * from `customers` where `reseller_id`='" . $this->customer_id . "'");
                while ($reseller_row = $this->objDBTools->fetch_assoc($reseller_result)) {
                    $this->reseller_customers[] = new CustomerTools($reseller_row["customer_id"], $this->objDBTools, $this->depth, $path);
                }
            } else {
                $this->reseller = new CustomerTools($customer_row["reseller_id"], $this->objDBTools, $this->depth, $path);
            }
        }

        if ($next_path == "" || $next_path == "merchant") {
            $merchant_result = $this->objDBTools->query("select * from `merchantrefs` where `customer_id`='" . $this->customer_id . "' and `is_credit`='yes'");
            if ($merchant_row = $this->objDBTools->fetch_assoc($merchant_result)) {
                $this->merchant = new MerchantTools($merchant_row["merchantref"], $this->objDBTools, $this->depth, $path);
            }
        }
    }

    public function getMainOrderId() {
        $order_result = $this->objDBTools->query("select * from `merchantrefs` "
                . "where `customer_id`='" . $this->customer_id . "' and `is_credit`='yes'");
        if ($merchantref_row = $this->objDBTools->fetch_assoc($order_result)) {
            return $merchantref_row["order_id"];
        }
    }

    public function getMainOrder() {
        return new OrderTools($this->getMainOrderId());
    }

    public function getReseller() {
        return $this->reseller;
    }

    //Old system
    public function getStartDate() {
        return new DateTime($this->start_date);
    }

    public function getRecurringStartDate() {
        $order_id = $this->getMainOrderId();
        $order = $this->objDBTools->objOrderTools($order_id);
        return $order->getRecurringStartDate();
    }

    public function getRecurringOrdersByDate($date) {
        $orders = array();
        foreach ($this->getOrders() as $order) {
            if ($order->getRecurringStartDate() <= $date && ($order->getTerminationDate() > $date || $order->getTerminationDate() == null)) {
                $orders[] = $order;
            }
        }
        return $orders;
    }

    public function getTotalFeesByMonth($date) {
        foreach ($this->getOrders() as $order) {

            if ((int) $order->getOrderID() < 10000) { // Old system
                //Ensure not terminated
                if ($order->getTerminationDate() > $date || $order->getTerminationDate() == null) {
                    //if start date in this month, get invoice total
                    if ($order->getStartDate()->format("Y-m") === $date->format("Y-m")) {
                        return $order->getProduct()->getPrice();
                    }
                    // if recurring show product price only
                    else if ($order->getRecurringStartDate() <= $date) {
                        return $order->getProduct()->getPrice();
                    }
                }
            }

            //Ensure not terminated
            if ($order->getTerminationDate() > $date || $order->getTerminationDate() == null) {
                //if start date in this month, get invoice total
                if ($order->getStartDate()->format("Y-m") === $date->format("Y-m")) {
                    return $order->getTotalPrice() - $order->getGSTTax() - $order->getQSTTax();
                }
                // if recurring show product price only
                else if ($order->getRecurringStartDate() <= $date) {
                    return $order->getProductPrice();
                }
            }
        }

        return 0;
    }

    public function getCustomerID() {
        return $this->customer_id;
    }

    public function getMerchant() {
        return $this->merchant;
    }

    //monthly or yearly
    public function getSubscriptionType() {

        $mainOrderId = $this->getMainOrderId();

        $objOrderTools = new OrderTools($mainOrderId, $this->objDBTools, $this->depth);
        $product_id = $objOrderTools->getOrderProductId($mainOrderId);
        $objProductTools = new ProductTools($product_id, $this->objDBTools, $this->depth);

        return $objProductTools->getSubscriptionType();
    }

    public function getFullName() {
        return $this->full_name;
    }

    public function getAddress() {
        return $this->getAddressLine1() . " " . $this->getAddressLine2() . " " . $this->getPostalCode() . " " . $this->getCity() . " " . $this->address;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getOrders() {
        return $this->orders;
    }

    public function getResellerCustomers() {
        return $this->reseller_customers;
    }

    public function getAddressLine1() {
        return $this->address_line_1;
    }

    public function setAddressLine1($address_line_1) {
        $this->address_line_1 = $address_line_1;
    }

    public function getAddressLine2() {
        return $this->address_line_2;
    }

    public function setAddressLine2($address_line_2) {
        $this->address_line_2 = $address_line_2;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getIPAddress() {
        return $this->ip_address;
    }

    public function getPostalCode() {
        return $this->postal_code;
    }

    public function setPostalCode($postal_code) {
        $this->postal_code = $postal_code;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

}
