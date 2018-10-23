<?php
include_once "../header.php";
$edit_id=intval($_GET["upcoming_customer_id"]);
?>

<?php
//
// $upcoming_customer = $dbTools->objUpcomingCustomerTools($_GET["upcoming_customer_id"]);
//
// if (isset($_POST["full_name"])){
//     $upcoming_customer->setFullName($_POST["full_name"]);
//     $upcoming_customer->setAddress($_POST["address"]);
//     $upcoming_customer->setEmail($_POST["email"]);
//     $upcoming_customer->setPhone($_POST["phone"]);
//     $upcoming_customer->setNote($_POST["note"]);
//     $upcoming_customer->setCreationDate(date("Y-m-d H:i:s"));
//
//     $result = $upcoming_customer->doUpdate();
//     if($result){
//         echo "<div class='alert alert-success'>done</div>";
//     }
// }
?>

<script>
    $(document).ready(function () {

        $.post("<?= $api_url ?>upcoming_customers/edit_upcoming_customer_api.php"
        ,{
              "edit_id":<?=$edit_id?>,
              "action": "get_upcoming_customer_by_id"
          }, function (data) {
          data = $.parseJSON(data);
          if (data.error != true) {
            $("input[name=\"full_name\"]").val(data.upcoming_customer.full_name);
            $("input[name=\"email\"]").val(data.upcoming_customer.email);
            $("input[name=\"phone\"]").val(data.upcoming_customer.phone);
            $("textarea[name=\"address\"]").val(data.upcoming_customer.address);
            $("textarea[name=\"note\"]").val(data.upcoming_customer.note);
            }
            else{
              alert("error loading data, please contact admin");
            }
        });

      $( ".update-form" ).submit(function( event ) {
          event.preventDefault();

          var full_name=$("input[name=\"full_name\"]").val();
          var email=$("input[name=\"email\"]").val();
          var phone=$("input[name=\"phone\"]").val();
          var address=$("textarea[name=\"address\"]").val();
          var note=$("textarea[name=\"note\"]").val();

          $.post("<?= $api_url ?>upcoming_customers/edit_upcoming_customer_api.php",
                  {
                    "action":"edit_upcoming_customer",
                    "edit_id":<?=$edit_id?>,
                    "full_name": full_name,
                    "email": email,
                    "phone": phone,
                    "address": address,
                    "note": note
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
<title>Edit Upcoming Customer</title>
<div class="page-header">
    <h4>Edit Upcoming Customer</h4>
</div>

<form class="update-form" >
    <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="full_name"  class="form-control" placeholder="Full Name"/>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="text" name="email"  class="form-control" placeholder="Email"/>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone"  class="form-control" placeholder="Phone"/>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <textarea type="text" name="address" class="form-control" /></textarea>
    </div>
    <div class="form-group">
        <label>Note:</label>
        <textarea type="text" name="note" class="form-control" /></textarea>
    </div>
    <input type="submit" class="btn btn-default" value="update">
</form>
<?php
include_once "../footer.php";
?>
