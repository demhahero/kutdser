<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestTools
 *
 * @author breeder1
 */
class RequestTools {

    private $request_id;
    private $reseller;
    private $order;
    private $creation_date;
    private $action;
    private $action_value;
    private $admin;
    private $verdict;
    private $verdict_date;
    private $product;
    private $action_on_date;
    private $note;
    private $product_price;
    private $product_title;
    private $product_category;
    private $product_subscription_type;
    private $modem_id;

    public function __construct($request_id = null, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $this->admin = null;
        $this->verdict_date = null;

        $this->request_id = $request_id;
        $this->objDBTools = $objDBTools;

        if ($this->request_id != null) {
            $request_result = $this->objDBTools->query("select * from `requests` "
                    . "where `request_id`='" . $this->request_id . "'");
            $request_row = $this->objDBTools->fetch_assoc($request_result);
            $this->creation_date = new DateTime($request_row["creation_date"]);
            $this->action = $request_row["action"];
            $this->action_value = $request_row["action_value"];
            $this->verdict = $request_row["verdict"];
            $this->verdict_date = ($request_row["verdict_date"] != "")? new DateTime($request_row["verdict_date"]) : null;
            $this->note = $request_row["note"];
            $this->product_price = $request_row["product_price"];
            $this->modem_id = $request_row["modem_id"];
            $this->action_on_date = ($request_row["action_on_date"] != "")? new DateTime($request_row["action_on_date"]) : null;
            $this->admin = new AdminTools($request_row["admin_id"], $this->objDBTools, $this->depth);
            $this->reseller = new CustomerTools($request_row["reseller_id"], $this->objDBTools, $this->depth);
            $this->order = new OrderTools($request_row["order_id"], $this->objDBTools, $this->depth);

            if($this->action != "cancel")
                //Get Product
                $this->product = new ProductTools($request_row["action_value"], $this->objDBTools, $this->depth);
            else
                 $this->product = null;
        }
    }

    public function getRequestID() {
        return $this->request_id;
    }

    public function getReseller() {
        return $this->reseller;
    }

    public function setReseller($reseller) {
        $this->reseller = $reseller;
    }

    public function getAdmin() {
        return $this->admin;
    }

    public function setAdmin($admin) {
        $this->admin = $admin;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function getVerdict() {
        return $this->verdict;
    }

    public function setVerdict($verdict) {
        $this->verdict = $verdict;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getVerdictDate() {
        return $this->verdict_date;
    }

    public function setVerdictDate($verdict_date) {
        $this->verdict_date = $verdict_date;
    }

    public function getAction() {
        $action=$this->action;
        if($this->action==="change_speed" && strlen($this->modem_id)>0)
        {
          $action="swap modem and change speed";
        }
        return $action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getActionValue() {
        return $this->action_value;
    }

    public function setActionValue($action_value) {
        $this->action_value = $action_value;
    }

    public function getProductPrice() {
        return $this->product_price;
    }

    public function setProductPrice($product_price) {
        $this->product_price = $product_price;
    }

    public function getProductTitle() {
        return $this->product_title;
    }

    public function setProductTitle($product_title) {
        $this->product_title = $product_title;
    }

    public function getProductCategory() {
        return $this->product_category;
    }

    public function setProductCategory($product_category) {
        $this->product_category = $product_category;
    }

    public function getProductSubscriptionType() {
        return $this->product_subscription_type;
    }

    public function setProductSubscriptionType($product_subscription_type) {
        $this->product_subscription_type = $product_subscription_type;
    }


    public function getActionOnDate() {
        if($this->action_on_date == "")
            return null;
        return $this->action_on_date;
    }

    public function setActionOnDate($action_on_date) {
        $this->action_on_date = $action_on_date;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getCreationDate() {
        if($this->creation_date == "")
            return null;
        return $this->creation_date;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function doInsert() {

        $admin_id = 0;
        if($this->admin != null)
            $admin_id = $this->admin->getAdminID();

        $verdictDate = "NULL";
        if($this->getVerdictDate() != null)
            $verdictDate = "'" . $this->getVerdictDate()->format("Y-m-d H:i:s")  . "'";

        $result = $this->objDBTools->query("insert into `requests` ("
                . "`reseller_id` ,"
                . "`order_id` ,"
                . "`creation_date` ,"
                . "`action` ,"
                . "`action_value` ,"
                . "`action_on_date` ,"
                . "`admin_id` ,"
                . "`note` ,"
                . "`product_price` ,"
                . "`product_title` ,"
                . "`product_category` ,"
                . "`product_subscription_type` ,"
                . "`verdict` ,"
                . "`verdict_date`"
                . ") VALUES ("
                . "'" . $this->getReseller()->getCustomerID() . "', "
                . "'" . $this->getOrder()->getOrderID() . "', "
                . "'" . $this->getCreationDate()->format("Y-m-d H:i:s") . "', "
                . "'" . $this->action . "', "
                . "'" . $this->action_value . "', "
                . "'" . $this->getActionOnDate()->format("Y-m-d H:i:s") . "', "
                . "'" . $admin_id . "', "
                . "'" . $this->getNote() . "', "
                . "'" . $this->getProductPrice() . "', "
                . "'" . $this->getProductTitle() . "', "
                . "'" . $this->getProductCategory() . "', "
                . "'" . $this->getProductSubscriptionType() . "', "
                . "'" . $this->verdict . "', "
                . "" . $verdictDate . ""
                . ")");

        return $result;
    }

    public function doUpdate() {

        $creation_date = $action_on_date = $verdict_date = "NULL";
        $creation_date = ($this->getCreationDate() != null)? "'" . $this->getCreationDate()->format("Y-m-d H:i:s")  . "'" : "NULL";
        $action_on_date = ($this->getActionOnDate() != null)? "'" . $this->getActionOnDate()->format("Y-m-d H:i:s")  . "'" : "NULL";
        $verdict_date = ($this->getVerdictDate() != null)? "'" . $this->getVerdictDate()->format("Y-m-d H:i:s")  . "'" : "NULL";

        $result = $this->objDBTools->query("update `requests` set "
                . "`reseller_id` = '" . $this->getReseller()->getCustomerID() . "',"
                . "`order_id` = '" . $this->getOrder()->getOrderID() . "',"
                . "`creation_date` = ".$creation_date.","
                . "`action` = '" . $this->action . "',"
                . "`action_value` = '" . $this->action_value . "',"
                . "`action_on_date` = ".$action_on_date.","
                . "`admin_id` = '" . $this->admin->getAdminID() . "',"
                . "`note` = '" . $this->getNote() . "',"
                . "`product_price` = '" . $this->getProductPrice() . "',"
                . "`verdict` = '" . $this->verdict . "',"
                . "`verdict_date` = ".$verdict_date.""
                . " where `request_id`='" . $this->request_id . "'");

        return $result;
    }

}
