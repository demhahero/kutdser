<?php
include_once "../header.php";
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



$reseller_id = intval($_GET["reseller_id"]);
$reseller = $dbTools->query("SELECT `has_discount`,`free_modem`,`free_router`,`free_setup`,`full_name` FROM `customers` WHERE `customer_id`='" . $reseller_id . "'");
$reseller_row=$dbTools->fetch_assoc($reseller);

$products = $dbTools->query("SELECT * FROM `products` INNER JOIN `reseller_discounts` on `products`.`product_id`=`reseller_discounts`.`product_id` WHERE `reseller_discounts`.`reseller_id`='" . $reseller_id . "'");

if($products->num_rows ==0)
$products = $dbTools->query("SELECT * FROM `products`");

?>

<title>Reseller's Discount</title>

<div class="page-header">
    <a class="last" href=""><?= $reseller_row['full_name']?></a>
</div>
<div id="message">
<?php
if(isset($query_result) && $query_result)
{
  ?>
  <div class="alert alert-success">
    <strong>Success!</strong> update discount
  </div>
  <?php
}
else if(isset($query_result) && !$query_result)
{
  ?>
  <div class="alert alert-danger">
    <strong>failed!</strong> update discount
  </div>
  <?php
}
 ?>
</div>
<div class="checkbox">
  <label><input id="discount_toggle" type="checkbox" onchange="discount_change();" <?=$reseller_row['has_discount']==='yes'?"checked":""?>>disable /enable discounts</label>
</div>
<form id="discount_form" action="#" method="POST">
  <p class="rounded  form-row form-row-wide custom_installation-date  ">
  <div class="panel panel-primary installation">
      <div class="panel-heading">Services</div>
      <div class="panel-body">
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_modem]" value="yes" <?=$reseller_row['free_modem']==='yes'?"checked":""?>>Free Modem</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_router]" value="yes" <?=$reseller_row['free_router']==='yes'?"checked":""?>>Free Router</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_setup]" value="yes" <?=$reseller_row['free_setup']==='yes'?"checked":""?>>Free Setup</label>
          </div>
        </div>
    </div>

    </p>

    <p class="rounded  form-row form-row-wide custom_installation-date  ">
    <div class="panel panel-primary installation">
        <div class="panel-heading">Products</div>
        <div class="panel-body">

              <div class="alert alert-info">
                Write the discount percentage you want for the product you want,
                 you can see the price after discount in the last field,
                  if you don't want to make discount to a product then set it's discount value to Zero
                </div>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Discount %</th>
                    <th>Price after discount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while($products_row=$dbTools->fetch_assoc($products))
                  {?>
                    <tr>
                      <td><?=$products_row['title']?></td>
                      <td><?=$products_row['category']?></td>
                      <td><?=$products_row['subscription_type']?></td>

                      <td ><span id='price_<?=$products_row['product_id']?>'><?=$products_row['price']?></span></td>
                      <td>
                        <input type="hidden" name="products[<?=$products_row['product_id']?>][reseller_discounts_id]" value="<?=isset($products_row['reseller_discounts_id'])?$products_row['reseller_discounts_id']:0?>"/>
                        <input type="number" min="0" max="100" onchange="setDiscount(this.value,<?=$products_row['product_id']?>)" value="<?=isset($products_row['discount'])?$products_row['discount']:0?>" name="products[<?=$products_row['product_id']?>][discount]" class="form-control"/>
                      </td>
                      <td>
                        <span id='discount_<?=$products_row['product_id']?>'>
                          <?php
                          $discount=(float)isset($products_row['discount'])?$products_row['discount']:0;
                          $product_price=(float)$products_row['price'];
                          $price_after_discount=$product_price-(($discount/100)*$product_price);
                          echo round($price_after_discount,2);
                          ?>
                        </span>
                      </td>
                    </tr>
                  <?php
                  } ?>

                </tbody>
              </table>
            </div>
        </div>

        </p>
    <input type="hidden" value="<?=$reseller_id?>" name="reseller_id"/>
    <input type="submit" value="Save" class="btn btn-primary" />
</form>
<script>
<?php
if($reseller_row['has_discount']==='yes')
{
  echo "$('#discount_form').show();";
}else{
  echo "$('#discount_form').hide();";
}?>
function discount_change(){
  $('#discount_form').toggle("slow");
  var discount=$('#discount_toggle');
  var discount_checked=discount.prop('checked');
  $.post( "<?=$site_url?>/customers/ajax_edit_discount.php", {
    discount_toggle:discount_checked,
    reseller_id:<?=$reseller_id?>
  } , function (result) {


    if(result.updated==="yes")
    {
      $('#message').html('<div class="alert alert-success"><strong>Success!</strong> updated successfully</div>');
    }
    else {
      $('#message').html('<div class="alert alert-danger"><strong>failed!</strong> update failed</div>');
    }
  }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
    $('#message').html('<div class="alert alert-danger"><strong>failed!</strong> '+errorThrown+'</div>');

   });
}
function setDiscount(value,id){
  console.log(id,value);
  var price_id='#price_'+id;
  var price=parseFloat($(price_id).html(), 10);
  var discount=parseFloat(value, 10);
  var price_after_discount=price-((discount/100)*price);
  $('#discount_'+id).html(price_after_discount.toFixed(2));
}
</script>
<?php
include_once "../footer.php";
?>
