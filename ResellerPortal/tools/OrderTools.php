<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderTools
 *
 * @author breeder1
 */
class OrderTools {

    private $order_id;
    private $objDBTools;
    private $customer;
    private $product;
    private $reseller;
    private $creation_date;
    private $status;
    //Order Options
    private $plan;
    private $modem;
    private $router;
    private $cable_subscriber;
    private $current_cable_provider;
    private $cancellation_date;
    private $installation_date_1;
    private $installation_time_1;
    private $installation_date_2;
    private $installation_time_2;
    private $installation_date_3;
    private $installation_time_3;
    private $modem_serial_number;
    private $modem_mac_address;
    private $modem_modem_type;
    private $additional_service;
    private $static_ip;
    private $modem_id;
    private $product_price;
    private $additional_service_price;
    private $static_ip_price;
    private $setup_price;
    private $modem_price;
    private $router_price;
    private $remaining_days_price;
    private $total_price;
    private $qst_tax;
    private $gst_tax;
    private $completion;
    private $adapter_price;
    private $current_phone_number;
    private $phone_province;
    private $adapter;
    private $modem_inventory_mac;
    private $termination_date;
    private $requests;
    private $actual_installation_date;
    private $actual_installation_time_from;
    private $actual_installation_time_to;
    private $remaining_days_from;
    private $remaining_days_to;
    private $product_id;
    private $product_title;
    private $product_category;
    private $product_subscription_type;
    private $admin_id;
    private $update_date;
    private $vl_number;

    public function __construct($order_id, $objDBTools, $depth = 0, $path = null) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $next_path = "";
        if ($path != null) {
            $next_path = array_shift($path);
        }

        $this->order_id = $order_id;
        $this->objDBTools = $objDBTools;

        $this->termination_date = null;

        //get Order
        $order_result = $this->objDBTools->query("select * from `orders` where `order_id`='" . $this->order_id . "'");
        $order_row = $this->objDBTools->fetch_assoc($order_result);

        $this->creation_date = $order_row["creation_date"];
        $this->status = $order_row["status"];
        $this->product_id = $order_row["product_id"];
        $this->product_title = $order_row["product_title"];
        $this->admin_id = $order_row["admin_id"];
        $this->update_date = $order_row["update_date"];
        $this->vl_number = $order_row["vl_number"];

        $this->getOrderOptions();

        //Get customer
        if ($next_path == "" || $next_path == "customer") {
            $customer_sql = "select * from `customers` where `customer_id`='" . $order_row["customer_id"] . "'";
            $customer_result = $this->objDBTools->query($customer_sql);
            $customer_row = $this->objDBTools->fetch_assoc($customer_result);
            $this->customer = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);
        }

        //Get Reseller
        if ($next_path == "" || $next_path == "reseller") {
            $customer_sql = "select * from `customers` where `customer_id`='" . $order_row["reseller_id"] . "'";
            $customer_result = $this->objDBTools->query($customer_sql);
            $customer_row = $this->objDBTools->fetch_assoc($customer_result);
            $this->reseller = new CustomerTools($customer_row["customer_id"], $this->objDBTools, $this->depth);
        }

        //Get requests
        if ($next_path == "" || $next_path == "request") {
            $this->requests = $this->objDBTools->request_query("select * from `requests` "
                    . "where `order_id`='" . $order_id . "'", $this->depth);
            $today = new DateTime();

            if ($this->requests != null)
                foreach ($this->requests as $request) {
                    if ($request->getAction() == "cancel" && $request->getVerdict() == "approve" && $request->getActionOnDate <= $today) {
                        $this->termination_date = $request->getActionOnDate();
                    }
                    /*
                    //if Upgraded , change his product id
                    else if (($request->getAction() == "upgrade" || $request->getAction() == "downgrade") && $request->getVerdict() == "approve" && $request->getActionOnDate <= $today) {
                        $this->product_id = $request->getActionValue();
                    }
                     *
                     */
                }
        }

        //Get Product
        if ($next_path == "" || $next_path == "product") {
            $this->product = new ProductTools($this->product_id, $this->objDBTools, $this->depth);
        }
    }

    public function getRecurringPrice() {
        $recurring_price = 0;
        if ($this->getProduct()->getCategory() == "internet") {
            $recurring_price = $this->getProductPrice() + $this->getAdditionalServicePrice();
            if ($this->getRouter() == "rent") {
                $recurring_price += $this->getRouterPrice();
            }
        } else if ($this->getProduct()->getCategory() == "phone") {
            $recurring_price = $this->getProductPrice();
        }
        return $recurring_price + ($recurring_price * 0.09975) + ($recurring_price * 0.05);
    }

    public function getTerminationDate() {
        return $this->termination_date;
    }

    public function setTerminationDate($termination_date) {
        $this->termination_date = $termination_date;
    }

    public function getProductTitle() {
        return $this->product_title;
    }


    public function getUpdateDate() {
        return $this->update_date;
    }

    public function setUpdateDate($update_date) {
        $this->update_date = $update_date;
    }

    public function getAdminID() {
        return $this->admin_id;
    }

    public function setAdminID($admin_id) {
        $this->admin_id = $admin_id;
    }

    public function getVLNumber() {
        return $this->vl_number;
    }

    public function setVLNumber($vl_number) {
        $this->vl_number = $vl_number;
    }

    public function getStartDate() {
        if((int)$this->getOrderID() < 10000) // Old system
            return $start_date = clone $this->getCustomer()->getStartDate();

        if ($this->getProduct()->getCategory() == "internet") {

            if ($this->getCableSubscriber() == "yes") {
                $cancellation_date = $this->getCancellationDate();
                $start_date = new DateTime($cancellation_date);
            } else {
                $installation_date_1 = $this->getInstallationDate1();
                $start_date = new DateTime($installation_date_1);
            }
        } else if ($this->getProduct()->getCategory() == "phone") {
            $start_date = $this->getCreationDate();
        }

        return $start_date;
    }

    public function getRecurringStartDate() {
        $start_date = clone $this->getStartDate();

        $SubscriptionType = $this->getProduct()->getSubscriptionType();

        //Find out 1st recurring date
        if ($SubscriptionType == "yearly") { //If yearly payment
            if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
                $start_date->add(new DateInterval('P1Y'));
            } else { // if not 1st day, add 1 year plus one month
                $start_date->add(new DateInterval('P1Y'));
                $start_date->add(new DateInterval('P1M'));
                $start_date->modify('first day of this month');
            }
        } else { // if payment monthly
            if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
                $start_date->add(new DateInterval('P1M'));
            } else { // if not 1st day, add 2 months
                $start_date->modify('first day of this month'); // Fixed february issue
                $start_date->add(new DateInterval('P2M'));
                $start_date->modify('first day of this month');
            }
        }

        return $start_date;
    }

    public function getOrderOptions() {
        $order_options_result = $this->objDBTools->query("select * from `order_options` "
                . "where `order_id`='" . $this->order_id . "'");
        $order_options_row = $this->objDBTools->fetch_assoc($order_options_result);
        $this->plan = $order_options_row["plan"];
        $this->modem = $order_options_row["modem"];
        $this->router = $order_options_row["router"];
        $this->cable_subscriber = $order_options_row["cable_subscriber"];
        $this->current_cable_provider = $order_options_row["current_cable_provider"];
        $this->cancellation_date = $order_options_row["cancellation_date"];
        $this->installation_date_1 = $order_options_row["installation_date_1"];
        $this->installation_time_1 = $order_options_row["installation_time_1"];
        $this->installation_date_2 = $order_options_row["installation_date_2"];
        $this->installation_time_2 = $order_options_row["installation_time_2"];
        $this->installation_date_3 = $order_options_row["installation_date_3"];
        $this->installation_time_3 = $order_options_row["installation_time_3"];
        $this->modem_serial_number = $order_options_row["modem_serial_number"];
        $this->modem_mac_address = $order_options_row["modem_mac_address"];
        $this->modem_modem_type = $order_options_row["modem_modem_type"];
        $this->additional_service = $order_options_row["additional_service"];
        $this->static_ip = $order_options_row["static_ip"];
        $this->modem_id = $order_options_row["modem_id"];
        $this->product_price = $order_options_row["product_price"];
        $this->additional_service_price = $order_options_row["additional_service_price"];
        $this->static_ip_price = $order_options_row["static_ip_price"];
        $this->setup_price = $order_options_row["setup_price"];
        $this->modem_price = $order_options_row["modem_price"];
        $this->router_price = $order_options_row["router_price"];
        $this->remaining_days_price = $order_options_row["remaining_days_price"];
        $this->total_price = $order_options_row["total_price"];
        $this->qst_tax = $order_options_row["qst_tax"];
        $this->gst_tax = $order_options_row["gst_tax"];
        $this->completion = $order_options_row["completion"];
        $this->adapter_price = $order_options_row["adapter_price"];
        $this->current_phone_number = $order_options_row["current_phone_number"];
        $this->phone_province = $order_options_row["phone_province"];
        $this->adapter = $order_options_row["adapter"];

        if($order_options_row["actual_installation_date"] != "" && $order_options_row["actual_installation_date"] != "null")
            $this->actual_installation_date = new DateTime($order_options_row["actual_installation_date"]);
        else
            $this->actual_installation_date = null;

        $this->actual_installation_time_from = $order_options_row["actual_installation_time_from"];
        $this->actual_installation_time_to = $order_options_row["actual_installation_time_to"];
        //$this->termination_date = $order_options_row["termination_date"];

        if ($this->modem == "inventory") {
            $modem_result = $this->objDBTools->query("select * from `modems` where `modem_id`='" . $order_options_row["modem_id"] . "'");
            $modem_row = $this->objDBTools->fetch_assoc($modem_result);
            $this->modem_inventory_mac = $modem_row["mac_address"];
        }
    }

    public function getOrderProductId() {
        return $this->product_id;
    }

    public function getCreationDate() {
        return new DateTime($this->creation_date);
    }

    public function getStatus() {
        return $this->status;
    }

    public function getOrderID() {
        return $this->order_id;
    }

    public function getDisplayedID() {
        if((int)$this->order_id <= 10380)
            return $this->order_id;
        return (((0x0000FFFF & (int)$this->order_id) << 16) + ((0xFFFF0000 & (int)$this->order_id) >> 16));
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function getReseller() {
        return $this->reseller;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getPlan() {
        return $this->plan;
    }

    public function getCompletion() {
        return $this->completion;
    }

    public function getModem() {
        return $this->modem;
    }

    public function getModemSerialNumber() {
        return $this->modem_serial_number;
    }

    public function getModemMACAddress() {
        return $this->modem_mac_address;
    }

    public function getModemType() {
        return $this->modem_modem_type;
    }

    public function getModemInventoryMAC() {
        return $this->modem_inventory_mac;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getCableSubscriber() {
        return $this->cable_subscriber;
    }

    public function getCurrentCableProvider() {
        return $this->current_cable_provider;
    }

    public function getCancellationDate() {
        return $this->cancellation_date;
    }

    public function getInstallationDate1() {
        return $this->installation_date_1;
    }

    public function getInstallationTime1() {
        return $this->installation_time_1;
    }

    public function getInstallationDate2() {
        return $this->installation_date_2;
    }

    public function getInstallationTime2() {
        return $this->installation_time_2;
    }

    public function getInstallationDate3() {
        return $this->installation_date_3;
    }

    public function getInstallationTime3() {
        return $this->installation_time_3;
    }

    public function getAdditionalService() {
        return $this->additional_service;
    }

    public function getStaticIp() {
        return $this->static_ip;
    }

    public function getAdditionalServicePrice() {
        return (float) $this->additional_service_price;
    }

    public function getStaticIpPrice() {
        return (float) $this->static_ip_price;
    }

    public function getTotalPrice() {
        return (float) $this->total_price;
    }

    public function getGSTTax() {
        return (float) $this->gst_tax;
    }

    public function getQSTTax() {
        return (float) $this->qst_tax;
    }

    public function getAdapterPrice() {
        return (float) $this->adapter_price;
    }

    public function getSetupPrice() {
        return (float) $this->setup_price;
    }

    public function getModemPrice() {
        return (float) $this->modem_price;
    }

    public function getRouterPrice() {
        return (float) $this->router_price;
    }

    public function getRemainingDaysPrice() {
        return (float) $this->remaining_days_price;
    }

    public function getProductPrice() {
        return (float) $this->product_price;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setCompletion($completion) {
        $this->completion = $completion;
    }

    public function getActualInstallationDate() {
        return $this->actual_installation_date;
    }

    public function setActualInstallationDate($actual_installation_date) {
        $this->actual_installation_date = $actual_installation_date;
    }

    public function getActualInstallationTimeFrom() {
        return $this->actual_installation_time_from;
    }

    public function setActualInstallationTimeFrom($actual_installation_time_from) {
        $this->actual_installation_time_from = $actual_installation_time_from;
    }

    public function getActualInstallationTimeTo() {
        return $this->actual_installation_time_to;
        ;
    }

    public function setActualInstallationTimeTo($actual_installation_time_to) {
        $this->actual_installation_time_to = $actual_installation_time_to;
    }

    public function doUpdate() {
        $result_order = $this->objDBTools->query("update `orders` set "
                . "`admin_id`='" . $this->admin_id . "', "
                . "`update_date`='" . $this->update_date->format("Y-m-d H:i:s") . "', "
                . "`vl_number`='" . $this->vl_number . "', "
                . "`status`='" . $this->status . "' "
                . "where `order_id`='" . $this->order_id . "'");

        if ($this->actual_installation_date != null)
            $actual_installation_date = "'" . $this->actual_installation_date->format("Y-m-d H:i:s") . "'";
        else
            $actual_installation_date = "NULL";

        $result_order_options = $this->objDBTools->query("update `order_options` set"
                . "`completion`='" . $this->completion . "', "
                . "`actual_installation_date`= " . $actual_installation_date . " ,"
                . "`actual_installation_time_from`='" . $this->actual_installation_time_from . "', "
                . "`actual_installation_time_to`='" . $this->actual_installation_time_to . "' "
                . "where `order_id`='" . $this->order_id . "'");

        return $result_order && $result_order_options;
    }

}
