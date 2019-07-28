<?php
session_start();
if(isset($_POST["action"]))
{
  if($_POST["action"]==="login" && isset($_POST["username"]) && isset($_POST["password"]))
  {
    include "../db_credentials.php";
    include "../tools/DBTools.php";
    $dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);
    if(isset($_POST["reseller"]))// check if login from mikrotik or resellerPortal
    {
      $query="SELECT * FROM `customers` WHERE `username`=?";
      $query_update="UPDATE `customers` SET `session_id`=? WHERE `customer_id`=?";
    }
    elseif (isset($_POST["customer"])) {
      $query="SELECT * FROM `customers` WHERE `username`=?";
      $query_update="UPDATE `customers` SET `session_id`=? WHERE `customer_id`=?";
    }
    else{
      $query="SELECT * FROM `admins` WHERE `username`=?";
      $query_update="UPDATE `admins` SET `session_id`=? WHERE `username`=?";
    }

    $username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = stripslashes(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$username;
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $admin_result = $stmt1->get_result();
    $admin_row = $dbTools->fetch_assoc($admin_result);
    if($admin_row){
        if (password_verify($password, $admin_row['password'])) {
            $session_id = uniqid('', true);



            $stmt = $dbTools->getConnection()->prepare($query_update);
            /* BK: always check whether the prepare() succeeded */
            if ($stmt === false) {
              trigger_error($this->mysqli->error, E_USER_ERROR);
              exit();
            }

            /* Bind our params */
            /* BK: variables must be bound in the same order as the params in your SQL.
             * Some people prefer PDO because it supports named parameter. */
             if(isset($_POST["reseller"]))// check if login from mikrotik or resellerPortal
             {
               $session_id=$admin_row["session_id"];
               $stmt->bind_param('ss',
                                 $session_id,
                                 $admin_row["customer_id"]
                                 ); // 's' specifies the variable type => 'string'
             }
             if(isset($_POST["customer"]))// check if login from customer_portal
             {

               $session_id=$admin_row["session_id"];
               $stmt->bind_param('ss',
                                 $session_id,
                                 $admin_row["customer_id"]
                                 ); // 's' specifies the variable type => 'string'
             }
             else{

               $stmt->bind_param('ss',
               $session_id,
               $username
               ); // 's' specifies the variable type => 'string'
             }

            /* Execute the prepared Statement */
            $status = $stmt->execute();
            /* BK: always check whether the execute() succeeded */
            if ($status === false) {
              trigger_error($stmt->error, E_USER_ERROR);
              exit();
            }

            if($status)
              {
                // if(isset($_POST["reseller"]))// check if login from mikrotik or resellerPortal
                // {
                //   setcookie("session_id", $session_id, time() + (86400 * 30), "/");
                // }
                // else

                  $_SESSION["session_id"] = $session_id;




                  echo "{\"login\" :", "true"
                    , ",\"message\":\"login success\""
                    , ",\"error\":false}";
              }
              else {
                echo "{\"login\" :", "false"
                  , ",\"message\":\"login failed: update session failed\""
                  , ",\"error\":true}";
              }


        }
        else {
          echo "{\"login\" :", "false"
            , ",\"message\":\"login failed: wrong password\""
            , ",\"error\":true}";
        }
    }
    else {
      echo "{\"login\" :", "false"
        , ",\"message\":\"login failed: username not found\""
        , ",\"error\":true}";
    }

  }// end login
  else if($_POST["action"]==="logout") {
          $_SESSION["session_id"] = null;
          //header('Location: '.$site_url.'/login.php');
          echo "{\"logout\" :", "true"
            , ",\"message\":\"logout success\""
            , ",\"error\":false}";
  }// end logout
}
else {
  echo "{\"login\" :", "false"
    , ",\"message\":\"login failed: you are not authorized\""
    , ",\"error\":true}";
}

?>
