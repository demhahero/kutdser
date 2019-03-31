<?php
include_once "../header.php";
?>


<script>
    $(document).ready(function () {
      $(".loader").hide();
      $(".to_reseller").hide();
      $.post("<?= $api_url ?>customers/get_resellers_api.php",
              {"post_action": "get_reseller"}
      , function (data, status) {
          data = $.parseJSON(data);
          $("select[name=\"from_reseller\"]").empty();
          $("select[name=\"from_reseller\"]").append($('<option>', {
              value: -1,
              text : "Select Reseller"
          }));
          $.each(data.data, function (i, item) {
            $("select[name=\"from_reseller\"]").append($('<option>', {
                value: item.customer_id,
                text : item.full_name
            }));
          });

      });
      $("select[name=\"from_reseller\"]" ).change(function() {
        if($(this ).val()>0)
        {
          $('#btnSubmit').attr("disabled", true);
          $(".loader").show();
          $.post("<?= $api_url ?>customers/get_resellers_api.php",
            {
              "post_action": "get_reseller",
              "reseller_condition":"not_equal",
              "reseller_id":$(this).val()
            }
          , function (data, status) {
              data = $.parseJSON(data);
              $("select[name=\"to_reseller\"]").empty();
              $("select[name=\"to_reseller\"]").append($('<option>', {
                  value: -1,
                  text : "Select Reseller"
              }));
              $.each(data.data, function (i, item) {
                $("select[name=\"to_reseller\"]").append($('<option>', {
                    value: item.customer_id,
                    text : item.full_name
                }));
              });
              $(".loader").hide();
              $(".to_reseller").show();
          });
        }
        else {
          $("select[name=\"to_reseller\"]").empty();
          $(".to_reseller").hide();
          $('#btnSubmit').attr("disabled", true);
        }

      });
      $("select[name=\"to_reseller\"]" ).change(function() {
        if($(this ).val()>0)
          $('#btnSubmit').attr("disabled", false);
        else{
          $('#btnSubmit').attr("disabled", true);
        }
      });

      $(".submit").click(function () {
          var from_reseller = $("select[name=\"from_reseller\"]").val();
          var to_reseller = $("select[name=\"to_reseller\"]").val();
          $.post("<?= $api_url ?>customers/customers_transfer_api.php",
            {
              "post_action":"customers_transfer",
              "from_reseller": from_reseller,
              "to_reseller": to_reseller
            }
          , function (data, status) {
              data = $.parseJSON(data);
              if (data.inserted == true) {
                  alert("Customers Transfered successfully");
              } else
                  alert("Error, try again");
          });
          return false;
      });


  });
</script>
<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 60px;
        height: 60px;
        margin:0 auto;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<title>Customers Transfer</title>
<div class="page-header">
    <a class="last" href="">Customers Transfer</a>
</div>


<form class="register-form" method="post">
    <div class="form-group">
        <label>Transfer customers for one reseller to another:</label>
        <div class="form-group">
            <label>Transfer FROM reseller:</label>
            <select  name="from_reseller" class="form-control" />

            </select>
        </div>
        <div class="loader"></div>
        <div class="form-group to_reseller">
            <label>TO reseller:</label>
            <select  name="to_reseller" class="form-control" />

            </select>
        </div>

    </div>
    <input id="btnSubmit" disabled type="submit" class="btn btn-default submit"  value="Submit">
</form>

<?php
include_once "../footer.php";
?>
