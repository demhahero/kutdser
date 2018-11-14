<?php
include "./init.php";

if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_all_products")
  {
    /// get customer account status
      $query="SELECT * FROM `products`";
      $stmt1 = $dbTools->getConnection()->prepare($query);

      $stmt1->execute();
      $products_result = $stmt1->get_result();

      if($products_result)
      {
      $products=[];
      while($product_row = $dbTools->fetch_assoc($products_result))
      {

        array_push($products,$product_row);
      }

      $products_json = json_encode($products);

      echo "{\"products\":",$products_json
        , ",\"message\":\"\""
        , ",\"error\":false}";

      }
      else{
      echo "{\"products\":[]"
        , ",\"message\":\" An error occurs. Please, contact support team\""
        , ",\"error\":true}";
      }
  }// end get_shopping_products
}
else {
  echo "{\"products\":[]"
    , ",\"message\":\" you are not authorized\""
    , ",\"error\":true}";
}

?>
