<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UpcomingCustomerTools
 *
 * @author breeder1
 */
class UpcomingCustomerTools {

    private $upcoming_customer_id;
    private $objDBTools;
    private $full_name;
    private $address;
    private $phone;
    private $creation_date;
    private $email;
    private $depth;
    private $admin;
    private $note;

    public function __construct($upcoming_customer_id = null, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $this->upcoming_customer_id = $upcoming_customer_id;
        $this->objDBTools = $objDBTools;

        if ($this->upcoming_customer_id != null) {
            $upcoming_customer_result = $this->objDBTools->query("select * from `upcoming_customers` "
                    . "where `upcoming_customer_id`='" . $this->upcoming_customer_id . "'");
            $upcoming_customer_row = $this->objDBTools->fetch_assoc($upcoming_customer_result);
            $this->full_name = $upcoming_customer_row["full_name"];
            $this->address = $upcoming_customer_row["address"];
            $this->phone = $upcoming_customer_row["phone"];
            $this->email = $upcoming_customer_row["email"];
            $this->note = $upcoming_customer_row["note"];
            $this->creation_date = $upcoming_customer_row["creation_date"];
            $this->admin = new AdminTools($upcoming_customer_row["admin_id"], $this->objDBTools, $this->depth);
        }
    }

    public function getUpcomingCustomerID() {
        return $this->upcoming_customer_id;
    }

    public function getFullName() {
        return $this->full_name;
    }

    public function setFullName($full_name) {
        $this->full_name = $full_name;
    }

    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function getAdmin() {
        return $this->admin;
    }

    public function setAdmin($admin) {
        $this->admin = $admin;
    }

    public function doUpdate() {
        $result = $this->objDBTools->query("update `upcoming_customers` set "
                . "`full_name`='" . $this->full_name . "', "
                . "`address`='" . $this->address . "', "
                . "`email`='" . $this->email . "', "
                . "`phone`='" . $this->phone . "', "
                . "`note`='" . $this->note . "' "
                . "where `upcoming_customer_id`='" . $this->upcoming_customer_id . "'");

        return $result;
    }

    public function doInsert() {
        $result = $this->objDBTools->query("insert into `upcoming_customers` ("
                . "`full_name` ,"
                . "`address` ,"
                . "`email` ,"
                . "`phone` ,"
                . "`admin_id` ,"
                . "`note` ,"
                . "`creation_date`"
                . ") VALUES ("
                . "'" . $this->full_name . "', "
                . "'" . $this->address . "', "
                . "'" . $this->email . "', "
                . "'" . $this->phone . "', "
                . "'" . $this->admin->getAdminID() . "', "
                . "'" . $this->note . "', "
                . "'" . $this->creation_date . "'"
                . ")");

        return $result;
    }
    
    public function doDelete() {
        $result = $this->objDBTools->query("delete from `upcoming_customers` "
                . "where `upcoming_customer_id`='" . $this->upcoming_customer_id . "'");

        return $result;
    }

}
