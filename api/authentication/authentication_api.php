<?php
session_start();
if(isset($_POST["action"]))
{
  if($_POST["action"]==="login" && isset($_POST["username"]) && isset($_POST["password"]))
  {
    include_once $_SERVER['DOCUMENT_ROOT']."/kutdser/mikrotik/db_credentials.php";
    include "../tools/DBTools.php";
    $dbTools = new DBTools($servername,$dbusername,$dbpassword,$dbname);

    $username = stripslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = stripslashes(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
    $query="SELECT * FROM `admins` WHERE `username`=?";
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
            $query_update="UPDATE `admins` SET `session_id`=? WHERE `username`=?";

            $stmt = $dbTools->getConnection()->prepare($query_update);

            $stmt = $dbTools->getConnection()->prepare($query_update);
            /* BK: always check whether the prepare() succeeded */
            if ($stmt === false) {
              trigger_error($this->mysqli->error, E_USER_ERROR);
              exit();
            }

            /* Bind our params */
            /* BK: variables must be bound in the same order as the params in your SQL.
             * Some people prefer PDO because it supports named parameter. */
             $stmt->bind_param('ss',
                               $session_id,
                               $username
                               ); // 's' specifies the variable type => 'string'


            /* Execute the prepared Statement */
            $status = $stmt->execute();
            /* BK: always check whether the execute() succeeded */
            if ($status === false) {
              trigger_error($stmt->error, E_USER_ERROR);
              exit();
            }

            if($status)
              {
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
