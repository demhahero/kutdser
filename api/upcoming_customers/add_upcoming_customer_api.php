<?php

if (isset($_POST["full_name"])) {

    include_once "../dbconfig.php";
    $full_name=$_POST["full_name"];
    $email=$_POST["email"];
    $phone=$_POST["phone"];
    $address=$_POST["address"];
    $note=$_POST["note"];

    $query = "INSERT INTO `upcoming_customers`(
            `full_name`,
            `email`,
            `phone`,
            `address`,
            `creation_date`,
            `admin_id`,
            `note`) VALUES (?,?,?,?,?,?,?)";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $now=new DateTime();
    $now_formated=$now->format('Y-m-d');
    $stmt1->bind_param('sssssss',
                      $full_name,
                      $email,
                      $phone,
                      $address,
                      $now_formated,
                      $admin_id,
                      $note);


    $stmt1->execute();

    $upcoming_customer = $stmt1->get_result();
    if ($stmt1->errno==0) {
        echo "{\"inserted\" :true,\"error\" :\"null\"}";
    } else {

        echo "{\"inserted\" :\"false\",\"error\" :\"failed to insert value\"}";
    }
}
else{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
