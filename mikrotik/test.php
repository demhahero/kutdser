<?php
//include_once "header.php";
echo $hashed_password = password_hash("jean2381$$2ewe", PASSWORD_DEFAULT);


    $c = curl_init('http://38.104.226.51/ahmed/subscribers_list.php?modems=\'F81D0F563D79\'');
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt(... other options you want...)

    $html = curl_exec($c);
     $json = json_decode($html);
     
     print_r($json);