<?php

    include_once "../dbconfig.php";

    $query="SELECT * from `information`";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if($result)
    {
      $json = json_encode($result);
        echo "{\"information\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"router\" :", "{}"
        , ",\"error\":true}";
    }    
  
?>
