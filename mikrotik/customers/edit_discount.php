<?php
include_once "../header.php";
$reseller_id=isset($_GET["reseller_id"])?$_GET["reseller_id"]:0;
?>

<script>
    $(document).ready(function () {
      $('#discount_form').hide();
      var tableDetailsTag=$('.products_table');

      var tableDetails=tableDetailsTag.DataTable({"pageLength": 50,"ordering": false});

      $(document).on('change', '#discount_toggle', function(){
        $('#discount_form').toggle("slow");
        var discount=$('#discount_toggle');
        var discount_checked=discount.prop('checked');
        $.post( "<?=$api_url?>customers/edit_discount_api.php", {
          discount_toggle:discount_checked,
          reseller_id:<?=$reseller_id?>
        } , function (result) {


          if(result.updated===true)
          {
            $('#message').html('<div class="alert alert-success"><strong>Success!</strong> updated successfully</div>');
          }
          else {
            $('#message').html('<div class="alert alert-danger"><strong>failed!</strong> update failed</div>');
          }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
          $('#message').html('<div class="alert alert-danger"><strong>failed!</strong> '+errorThrown+'</div>');

         });
      });
      $(document).on('change', '.discount_amount', function(){
        var id=$(this).attr("data-product-id");
        var value=$(this).val();

        var price_id='#price_'+id;
        var price=parseFloat($(price_id).html(), 10);
        var discount=parseFloat(value, 10);
        var price_after_discount=price-((discount/100)*price);
        $('#discount_'+id).html(price_after_discount.toFixed(2));
      });

      $.post("<?= $api_url ?>customers/edit_discount_api.php",
       {
         "action": "get_discount_details",
          "reseller_id": <?= $reseller_id?>}
           , function (data, status) {
          data = $.parseJSON(data);
          if (data.error != true) {

            if(data.reseller_row.has_discount==='yes')
            {
              $('#discount_form').show();
            }else{
              $('#discount_form').hide();
            }

            $("#reseller_name").html(data.reseller_row.full_name);
            if(data.reseller_row.has_discount==='yes')
              $("#discount_toggle").attr("checked",true);
            else{
              $("#discount_toggle").attr("checked",false);
            }

            $("input[name=\"discount_expire_date\"]").val(data.reseller_row.discount_expire_date);

            if(data.reseller_row.free_modem==='yes')
              $("input[name=\"services[free_modem]\"]").attr("checked",true);
            else{
              $("input[name=\"services[free_modem]\"]").attr("checked",false);
            }
            if(data.reseller_row.free_router==='yes')
              $("input[name=\"services[free_router]\"]").attr("checked",true);
            else{
              $("input[name=\"services[free_router]\"]").attr("checked",false);
            }
            if(data.reseller_row.free_adapter==='yes')
              $("input[name=\"services[free_adapter]\"]").attr("checked",true);
            else{
              $("input[name=\"services[free_adapter]\"]").attr("checked",false);
            }
            if(data.reseller_row.free_installation==='yes')
              $("input[name=\"services[free_installation]\"]").attr("checked",true);
            else{
              $("input[name=\"services[free_installation]\"]").attr("checked",false);
            }
            if(data.reseller_row.free_transfer==='yes')
              $("input[name=\"services[free_transfer]\"]").attr("checked",true);
            else{
              $("input[name=\"services[free_transfer]\"]").attr("checked",false);
            }



            $.each(data.products, function (i, products_row) {
              var reseller_discounts_id= (products_row.hasOwnProperty("reseller_discounts_id")?products_row.reseller_discounts_id:0);
              var discount=(products_row.hasOwnProperty('discount')?products_row.discount:0);
              var product_price=parseFloat(products_row.price);
              var price_after_discount=product_price-((discount/100)*product_price);


              var col4="<span id='price_"+products_row.product_id+"'>"+products_row.price+"</span>";
              var col5='<input type="hidden" name="products['+products_row.product_id+'][reseller_discounts_id]" value="'+reseller_discounts_id+'"/>'
                        +'<input type="number" min="0" max="100" class="discount_amount" data-product-id="'+products_row.product_id+'" value="'+discount+'" name="products['+products_row.product_id+'][discount]" class="form-control"/>';
              var col6='<span id="discount_'+products_row.product_id+'">'
                          +price_after_discount.toFixed(2)
                        +'</span>';
              var col7 = '<select name="products['+products_row.product_id+'][discount_duration]" class="form-control">'
                            +'<option '+((products_row.hasOwnProperty("discount_duration") && products_row.discount_duration ==="three_months")?"selected":"")+' value="three_months">3 Months </option>'
                            +'<option '+((products_row.hasOwnProperty("discount_duration") && products_row.discount_duration ==="six_months")?"selected":"")+' value="six_months">6 Months </option>'
                            +'<option '+((products_row.hasOwnProperty("discount_duration") && products_row.discount_duration ==="one_year")?"selected":"")+' value="one_year">1 Year </option>'
                          +'</select>';
              tableDetails.row.add([
                products_row.title,
                products_row.category,
                products_row.subscription_type,
                col4,
                col5,
                col6,
                col7

              ]).draw( false );;

            });



          } else
              alert("Error, try again");
      });

      $("#discount_form").submit(function(e){
        e.preventDefault();
        var post_data=$("form").serialize();
        $.post("<?= $api_url ?>customers/edit_discount_api.php",
              post_data
             , function (data, status) {
            data = $.parseJSON(data);
            if(data.updated===true)
            {
              $('#message').html('<div class="alert alert-success"><strong>Success!</strong> updated successfully</div>');
            }
            else {
              $('#message').html('<div class="alert alert-danger"><strong>failed!</strong> update failed</div>');
            }
            window.scrollTo(0, 0);
          }
        );

      });
    });
</script>
<title>Reseller's Discount</title>

<div class="page-header">
    <a class="last" id="reseller_name" href=""></a>
</div>
<div id="message">

</div>
<div class="checkbox">
  <label><input id="discount_toggle" type="checkbox"  >disable /enable discounts</label>
</div>
<form id="discount_form" >
  <p class="rounded  form-row form-row-wide custom_installation-date  ">
  <div class="panel panel-primary installation">
      <div class="panel-heading">Discount Expire date</div>
      <div class="panel-body">
        <div class="date4" style="width: 30%">
          Expire Date : <input name="discount_expire_date"  readonly=""  class="form-control datepicker" >
        </div>
      </div>
    </div>
  </p>
  <p class="rounded  form-row form-row-wide custom_installation-date  ">
  <div class="panel panel-primary installation">
      <div class="panel-heading">Services</div>
      <div class="panel-body">
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_modem]" value="yes" >Free Modem</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_router]" value="yes" >Free Router</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_adapter]" value="yes" >Free Adapter (Cisco ATA)</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_installation]" value="yes" >Free Installation fees</label>
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="services[free_transfer]" value="yes" >Free Transfer fees</label>
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
              <table class="table table-hover products_table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Discount %</th>
                    <th>Price after discount</th>
                    <th>duration</th>
                  </tr>
                </thead>
                <tbody>


                </tbody>
              </table>
            </div>
        </div>

        </p>
    <input type="hidden" value="<?=$reseller_id?>" name="reseller_id"/>
    <input type="submit" value="Save" class="btn btn-primary" />
</form>

<?php
include_once "../footer.php";
?>
