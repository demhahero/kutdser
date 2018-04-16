<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminTools
 *
 * @author breeder1
 */
class AdminTools {

    private $admin_id;
    private $objDBTools;
    private $username;

    public function __construct($admin_id = null, $objDBTools, $depth = 0) {
        if ($depth == 5)
            return;
        $depth++;
        $this->depth = $depth;

        $this->admin_id = $admin_id;
        $this->objDBTools = $objDBTools;

        if ($this->admin_id != null) {
            $admin_result = $this->objDBTools->query("select * from `admins` where `admin_id`='" . $this->admin_id . "'");
            $admin_row = $this->objDBTools->fetch_assoc($admin_result);
            $this->username = $admin_row["username"];
        }
    }

    public function getAdminID() {
        return $this->admin_id;
    }

    public function getUsername() {
        return $this->username;
    }

}
