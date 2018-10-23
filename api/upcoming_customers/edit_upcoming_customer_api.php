<?php
if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_upcoming_customer_by_id" && isset($_POST["edit_id"]))
  {
    include_once "../dbconfig.php";

    $query="SELECT * FROM `upcoming_customers` WHERE `upcoming_customer_id`= ? ";


    $stmt1 = $dbTools->getConnection()->prepare($query);

    $param_value=$_POST["edit_id"];
    $stmt1->bind_param('s',
                      $param_value
                      ); // 's' specifies the variable type => 'string'


    $stmt1->execute();

    $result1 = $stmt1->get_result();
    $result = $dbTools->fetch_assoc($result1);
    if($result)
    {
      $json = json_encode($result);
        echo "{\"upcoming_customer\" :", $json
          , ",\"error\":false}";
    }
    else {
      echo "{\"upcoming_customer\" :", "{}"
        , ",\"error\":true}";
    }
  }
  else if($_POST["action"]==="edit_upcoming_customer" && isset($_POST["edit_id"]))
    {
      include_once "../dbconfig.php";
      $edit_id=$_POST["edit_id"];
      $full_name=$_POST["full_name"];
      $email=$_POST["email"];
      $phone=$_POST["phone"];
      $address=$_POST["address"];
      $note=$_POST["note"];

      $query = "UPDATE `upcoming_customers` SET
                `full_name`=?,
                `email`=?,
                `phone`=?,
                `address`=?,
                `note`=?
                WHERE `upcoming_customer_id`=?";


      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->bind_param('ssssss',
                        $full_name,
                        $email,
                        $phone,
                        $address,
                        $note,
                        $edit_id);


      $stmt1->execute();

      $upcoming_customer = $stmt1->get_result();
      if ($stmt1->errno==0) {
          echo "{\"edited\" :true,\"error\" :\"null\"}";
      } else {

          echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
      }
    }
}else
{
  echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}
?>
