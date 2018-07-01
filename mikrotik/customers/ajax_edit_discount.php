<?php
include_once "../dbconfig.php";
$dbTools->query("SET CHARACTER SET utf8");
if(isset($_POST["products"]))
{
  if(isset($_POST["services"]))
  {
    $services["free_modem"]=isset($_POST["services"]["free_modem"])?"yes":"no";
    $services["free_router"]=isset($_POST["services"]["free_router"])?"yes":"no";
    $services["free_setup"]=isset($_POST["services"]["free_setup"])?"yes":"no";
  }
  else{
      $services["free_modem"]="no";
      $services["free_router"]="no";
      $services["free_setup"]="no";
  }
  $dbTools->query("UPDATE `customers` SET
     `has_discount`=N'yes',
     `free_modem`=N'".$services["free_modem"]."',
     `free_router`=N'".$services["free_router"]."',
     `free_setup`=N'".$services["free_setup"]."'
     WHERE `customer_id`=".$_POST['reseller_id']);
  $products = $dbTools->query("SELECT * FROM `products` INNER JOIN `reseller_discounts` on `products`.`product_id`=`reseller_discounts`.`product_id` WHERE `reseller_discounts`.`reseller_id`='" . $_POST['reseller_id'] . "'");

  if($products->num_rows ==0)
  {
    $query="INSERT INTO `reseller_discounts`
          ( `reseller_id`, `product_id`, `discount`)
      VALUES";
      $values="";
    foreach ($_POST['products'] as $key => $value) {
      $values.="(N'".$_POST['reseller_id']."',N'".$key."',N'".$value['discount']."'),";
    }
    $values= substr($values, 0, strlen($values)-1);
    $query.=$values;

    $query_result= $dbTools->query($query);
  }
  else {
    foreach ($_POST['products'] as $key => $value) {
      $query="UPDATE `reseller_discounts` SET
      `reseller_id`=N'".$_POST['reseller_id']."',
      `product_id`=N'".$key."',
      `discount`=N'".$value['discount']."'
      WHERE `reseller_discounts_id`=".$value['reseller_discounts_id'];
      $query_result= $dbTools->query($query);

    }
    $query_result= $dbTools->query($query);
  }
}
else if (isset($_POST['discount_toggle']))
{
  $query="UPDATE `customers` SET
     `has_discount`=N'yes',
     `has_discount`=N'".($_POST['discount_toggle']==="true"?"yes":"no")."'
     WHERE `customer_id`=".$_POST['reseller_id'];
  $result=$dbTools->query($query);


     if($result)
       echo "{\"updated\" :\"yes\",\"error\" :null}";
     else
        echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";

}
?>
