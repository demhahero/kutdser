<?php
include_once "../dbconfig.php";

if(isset($_POST["action"]) && $_POST["action"]==="get_discount_details")
{
  $reseller_id = intval($_POST["reseller_id"]);
  $query="SELECT `discount_expire_date`,`has_discount`,`free_modem`,`free_router`,`free_adapter`,`free_installation`,`free_transfer`,`full_name`
          FROM `customers`
          WHERE `customer_id`=?";

  $stmt1 = $dbTools->getConnection()->prepare($query);

  $stmt1->bind_param('s',
                    $reseller_id);
  $stmt1->execute();
  $discount = $stmt1->get_result();
  $reseller_row = $dbTools->fetch_assoc($discount);


  $today_date = new DateTime();
  $discount_expire_date=(isset($reseller_row['discount_expire_date']) && strlen($reseller_row['discount_expire_date'])>0)?$reseller_row['discount_expire_date']:$today_date->format('Y-m-d');
  $reseller_row["discount_expire_date"]=$discount_expire_date;

  $query="SELECT * FROM `products`
          INNER JOIN `reseller_discounts` on `products`.`product_id`=`reseller_discounts`.`product_id`
          WHERE `reseller_discounts`.`reseller_id`=?";
  $stmt2 = $dbTools->getConnection()->prepare($query);
  $stmt2->bind_param('s',
                    $reseller_id);
  $stmt2->execute();
  $products_result=$stmt2->get_result();

  if($stmt2->affected_rows<=0)
  {

      $query="SELECT * FROM `products`";

      $stmt3 = $dbTools->getConnection()->prepare($query);


      $stmt3->execute();
      $products_result = $stmt3->get_result();

      $products=array();
          while ($product = $dbTools->fetch_assoc($products_result)) {

            $products[] = $product;

          }
      $json_products = json_encode($products);
      $json_reseller_row = json_encode($reseller_row);
      echo "{\"products\" :", $json_products
        ,",\"reseller_row\" : ", $json_reseller_row
        , ",\"error\":false}";
      exit();

  }
  else{
    $products=array();
        while ($product = $dbTools->fetch_assoc($products_result)) {
          array_push($products,$product);
        }
      $json_products = json_encode($products);
      $json_reseller_row = json_encode($reseller_row);
      echo "{\"products\" :", $json_products
        ,",\"reseller_row\" : ", $json_reseller_row
        , ",\"error\":false}";
      exit();
  }



}
else if(isset($_POST["products"]))
{

  if(isset($_POST["services"]))
  {
    $services["free_modem"]=isset($_POST["services"]["free_modem"])?"yes":"no";
    $services["free_router"]=isset($_POST["services"]["free_router"])?"yes":"no";
    $services["free_setup"]=isset($_POST["services"]["free_setup"])?"yes":"no";
    $services["free_adapter"]=isset($_POST["services"]["free_adapter"])?"yes":"no";
    $services["free_installation"]=isset($_POST["services"]["free_installation"])?"yes":"no";
    $services["free_transfer"]=isset($_POST["services"]["free_transfer"])?"yes":"no";
  }
  else{
      $services["free_modem"]="no";
      $services["free_router"]="no";
      $services["free_setup"]="no";
      $services["free_adapter"]="no";
      $services["free_installation"]="no";
      $services["free_transfer"]="no";
  }
  $free_modem=$services["free_modem"];
  $free_router=$services["free_router"];
  $free_setup=$services["free_setup"];
  $free_adapter=$services["free_adapter"];
  $free_installation=$services["free_installation"];
  $free_transfer=$services["free_transfer"];
  $reseller_id=$_POST['reseller_id'];
  $query="UPDATE `customers` SET
           `has_discount`=N'yes',
           `free_modem`=?,
           `free_router`=?,
           `free_setup`=?,
           `free_adapter`=?,
           `free_installation`=?,
           `free_transfer`=?
           WHERE `customer_id`=?";

     $stmt1 = $dbTools->getConnection()->prepare($query);

     $stmt1->bind_param('sssssss',
                       $free_modem,
                       $free_router,
                       $free_setup,
                       $free_adapter,
                       $free_installation,
                       $free_transfer,
                       $reseller_id);


   $stmt1->execute();

   $discount = $stmt1->get_result();

   if ($stmt1->errno==0) {
     $query="SELECT * FROM `products` INNER JOIN `reseller_discounts` ON `products`.`product_id`=`reseller_discounts`.`product_id` WHERE `reseller_discounts`.`reseller_id`=?";
     $stmt2 = $dbTools->getConnection()->prepare($query);

     $stmt2->bind_param('s',
                       $reseller_id);


   $stmt2->execute();

   $products = $stmt2->get_result();
   if ($stmt1->errno==0) {

     if($products->num_rows ==0)
     {
       $query="INSERT INTO `reseller_discounts`
             ( `reseller_id`, `product_id`, `discount`, `discount_duration`)
         VALUES (?,?,?,?)";

         $stmt = $dbTools->getConnection()->prepare($query);


       foreach ($_POST['products'] as $key => $value) {
         $stmt->bind_param('ssss', $_POST['reseller_id'], $key, $value['discount'],$value['discount_duration']);
         $stmt->execute();

       }
       echo "{\"updated\" :true,\"error\" :null}";

     }
     else {
       $query="UPDATE `reseller_discounts` SET
                 `reseller_id`=?,
                 `product_id`=?,
                 `discount_duration`=?,
                 `discount`=?
               WHERE `reseller_discounts_id`=?";

         $stmt = $dbTools->getConnection()->prepare($query);


       foreach ($_POST['products'] as $key => $value) {

         $stmt->bind_param('sssss',
                          $_POST['reseller_id'],
                          $key,
                          $value['discount_duration'],
                          $value['discount'],
                          $value['reseller_discounts_id']);
         $stmt->execute();

       }
       echo "{\"updated\" :true,\"error\" :null}";


     }

   }
   else{
     echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";
   }
   }
   else{
     echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";
   }
}
else if (isset($_POST['discount_toggle']))
{
  $query="UPDATE `customers` SET
             `has_discount`=N'yes',
             `has_discount`=?
           WHERE `customer_id`=?";

    $has_discount=($_POST['discount_toggle']==="true"?"yes":"no");
    $customer_id=$_POST['reseller_id'];
    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('ss',
                      $has_discount,
                      $customer_id);

    $stmt1->execute();
    $discount = $stmt1->get_result();
    if ($stmt1->errno==0) {
      echo "{\"updated\" :true,\"error\" :null}";
    }
    else{
      echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";
    }

}
?>
