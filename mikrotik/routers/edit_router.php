<?php
// include_once "../../api/dbconfig.php";
include_once "../header.php";
$edit_id=intval($_GET["router_id"]);
?>
<?php
// $routerTools = $dbTools->objRouterTools(intval($_GET["router_id"]));
//
//
// if (isset($_POST["serial_number"])) {
//
//     (isset($_POST["is_ours"])) ? $is_ours = "yes" : $is_ours = "no";
//     (isset($_POST["is_sold"])) ? $is_sold = "yes" : $is_sold = "no";
//
//     $routerTools->setIsSold($is_sold);
//     $routerTools->setType(mysql_real_escape_string($_POST["type"]));
//     $routerTools->setSerialNumber(mysql_real_escape_string($_POST["serial_number"]));
//     $routerTools->setIsOurs($is_ours);
//     $routerTools->setReseller($dbTools->objCustomerTools($_POST["reseller_id"]));
//     $routerTools->setCustomer($dbTools->objCustomerTools($_POST["customer_id"]));
//
//     $result = $routerTools->doUpdate();
//
//     if ($result)
//         echo "<div class='alert alert-success'>done</div>";
// }
?>

<script>
    $(document).ready(function () {
      $.getJSON("<?= $api_url ?>customers/add_customer_api.php?action=get_all_customers", function (result) {
          $.each(result['customers'], function (i, item) {

              $("select[name=\"customer_id\"]").append($('<option>', {
                  value: item.customer_id,
                  text : item.full_name
              }));
          });
          $.getJSON("<?= $api_url ?>customers/add_customer_api.php?action=get_all_resellers", function (result) {
              $.each(result['resellers'], function (i, item) {

                  $("select[name=\"reseller_id\"]").append($('<option>', {
                      value: item.customer_id,
                      text : item.full_name
                  }));
              });
              $.post("<?= $api_url ?>routers/edit_router_api.php"
              ,{
                    "edit_id":<?=$edit_id?>,
                    "action": "get_router_by_id"
                }, function (data) {
                data = $.parseJSON(data);
                if (data.error != true) {

                  $("input[name=\"serial_number\"]").val(data.router.serial_number);
                  $("input[name=\"type\"]").val(data.router.type);
                  $("select[name=\"reseller_id\"]").val(data.router.reseller_id);
                  $("select[name=\"customer_id\"]").val(data.router.customer_id);

                  if (data.router.is_ours==="yes")
                    {
                      $( "#is_ours" ).prop( "checked", true );
                    }
                    else{
                      $( "#is_ours" ).prop( "checked", false );
                    }
                    if (data.router.is_sold==="yes")
                      {
                        $( "#is_sold" ).prop( "checked", true );
                      }
                      else{
                        $( "#is_sold" ).prop( "checked", false );
                      }
                  }
                  else{
                    alert("error loading data, please contact admin");
                  }
              });
          });
      });
      $( ".update-form" ).submit(function( event ) {
          event.preventDefault();
          var is_ours="no";
          if ($('#is_ours').is(":checked"))
          {
            is_ours="yes";
          }
          var is_sold="no";
          if ($('#is_sold').is(":checked"))
          {
            is_sold="yes";
          }

          var serial_number=$("input[name=\"serial_number\"]").val();
          var type=$("input[name=\"type\"]").val();
          var reseller_id=$("select[name=\"reseller_id\"]").val();
          var customer_id=$("select[name=\"customer_id\"]").val();

          $.post("<?= $api_url ?>routers/edit_router_api.php",
                  {
                    "action":"edit_router",
                    "edit_id":<?=$edit_id?>,
                    "serial_number": serial_number,
                    "type": type,
                    "reseller_id": reseller_id,
                    "customer_id": customer_id,
                    "is_ours" : is_ours,
                    "is_sold": is_sold
                  }
          , function (data, status) {
              data = $.parseJSON(data);
              if (data.edited == true) {
                  alert("value updated");

              } else
                  alert("Error: "+data.error);
          });
        });

    });
</script>
<title>Edit Router</title>
<div class="page-header">
    <h4>Edit Router</h4>
</div>

<form class="update-form" >
    <div class="form-group">
        <label>Serial Number:</label>
        <input type="text" name="serial_number"  class="form-control" placeholder="serial number"/>
    </div>

    <div class="form-group">
        <label>Type:</label>
        <input type="text" name="type"  class="form-control" placeholder="type"/>
    </div>
    <div class="form-group">
        <label>Reseller:</label>
        <select  name="reseller_id" class="form-control">
            <option value="0">No Reseller</option>

        </select>
    </div>
    <div class="form-group">
        <label>Customer:</label>
        <select  name="customer_id" class="form-control">

        </select>
    </div>
    <div class="form-group">
        <label>Is ours:</label>
        <input id="is_ours" type="checkbox" name="is_ours"   class="form-control" placeholder=""/>
    </div>
    <div class="form-group">
        <label>Is Sold:</label>
        <input id="is_sold" type="checkbox" name="is_sold"   class="form-control" placeholder=""/>
    </div>
    <input  type="submit" class="btn btn-default" value="Update">
</form>

<?php
include_once "../footer.php";
?>
