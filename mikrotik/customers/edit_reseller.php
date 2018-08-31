<?php
include_once "../header.php";
?>

<?php
$c = curl_init('http://38.104.226.51/ahmed/subscribers_list.php');
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//curl_setopt(... other options you want...)

$html = curl_exec($c);
?>

<script>
    $(document).ready(function () {

        $.getJSON("<?= $api_url ?>reseller_edit_api.php?customer_id=<?= $_GET["customer_id"] ?>", function (result) {
                    $.each(result['resellers'], function (i, item) {

                        $("select[name=\"parent_reseller\"]").append($('<option>', {
                            value: item.customer_id,
                            text : item.full_name
                        }));
                    });
                    $("#reseller_name").html(result['customer']['full_name']);
                    $("input[name=\"full_name\"]").val(result['customer']['full_name']);
                    $("input[name=\"username\"]").val(result['customer']['username']);
                    $("select[name=\"parent_reseller\"]").val(result['customer']['parent_reseller']);
                    $("input[name=\"email\"]").val(result['customer']['email']);
                    $("input[name=\"address_line_1\"]").val(result['customer']['address_line_1']);
                    $("input[name=\"address_line_2\"]").val(result['customer']['address_line_2']);
                    $("input[name=\"postal_code\"]").val(result['customer']['postal_code']);
                    $("input[name=\"city\"]").val(result['customer']['city']);
                    $("input[name=\"phone\"]").val(result['customer']['phone']);
                    $("input[name=\"reseller_commission_percentage\"]").val(result['customer']['reseller_commission_percentage']);



                });



                $("#submit_general").click(function () {
                    var customer_id = "<?= $_GET['customer_id'] ?>";
                    var full_name = $("input[name=\"full_name\"]").val();
                    var parent_reseller = $("select[name=\"parent_reseller\"]").val();
                    var email = $("input[name=\"email\"]").val();
                    var address_line_1 = $("input[name=\"address_line_1\"]").val();
                    var address_line_2 = $("input[name=\"address_line_2\"]").val();
                    var postal_code = $("input[name=\"postal_code\"]").val();
                    var city = $("input[name=\"city\"]").val();
                    var phone = $("input[name=\"phone\"]").val();
                    var reseller_commission_percentage=$("input[name=\"reseller_commission_percentage\"]").val();

                    $.post("<?= $api_url ?>reseller_edit_api.php",
                            {
                              customer_id: customer_id,
                              full_name: full_name,
                              parent_reseller: parent_reseller,
                              email: email,
                              address_line_1: address_line_1,
                              address_line_2: address_line_2,
                              postal_code: postal_code,
                              city: city,
                              phone: phone,
                              reseller_commission_percentage: reseller_commission_percentage
                            }
                    , function (data, status) {
                        data = $.parseJSON(data);
                        if (data.inserted == true) {
                            alert("General info updated");
                        } else
                            alert("Error, try again");
                    });
                    return false;
                });

                $("#submit_account").click(function () {
                    var customer_id = "<?= $_GET['customer_id'] ?>";
                    var username = $("input[name=\"username\"]").val();
                    var password = $("input[name=\"password\"]").val();
                    var new_password = $("input[name=\"new_password\"]").val();

                    $.post("<?= $api_url ?>reseller_edit_api.php",
                            {
                              customer_id: customer_id,
                              username: username,
                              password: password,
                              new_password: new_password
                            }
                    , function (data, status) {
                        data = $.parseJSON(data);
                        if (data.inserted == true) {
                            alert("info updated");
                        } else
                            alert(data.error);
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

<title>Reseller Details</title>
<div class="page-header">
    <a class="last" href="">Reseller Details</a>
</div>
<div class="col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2><i class="fa fa-bars"></i> Reseller Details for <span id="reseller_name"></span></h2>

      <div class="clearfix"></div>
    </div>
    <div class="x_content">


      <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
          <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">General Information</a>
          </li>
          <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Account Information</a>
          </li>

        </ul>
        <div id="myTabContent" class="tab-content">
          <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
            <form class="general-form" method="post">
                <div class="form-group">
                  <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Full name:</label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" name="full_name" class="form-control" />
                      </div>
                  </div>
                  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Parent reseller</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <select name="parent_reseller" class="select2_single form-control">
                            <option value="0"></option>
                          </select>
                        </div>
                      </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Email:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="email" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Address line 1:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="address_line_1" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Address line 2:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="address_line_2" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Postal code:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="postal_code" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">City:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="city" class="form-control" />
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" name="phone" class="form-control" />
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Reseller commission percentage:</label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="number" name="reseller_commission_percentage" class="form-control" />
                        </div>

                    </div>
                </div>
                <input type="submit" class="btn btn-success" id="submit_general"  value="Send">
            </form>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
            <form class="form-horizontal form-label-left input_mask" method="post">

              <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                <input name="username" type="text" class="form-control has-feedback-left" placeholder="User Name">
                <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
              </div>


              <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                <input name="password" type="text" class="form-control has-feedback-left" placeholder="Enter your cuurent password">
                <span class="fa fa-unlock form-control-feedback left" aria-hidden="true"></span>
              </div>

              <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                <input name="new_password" type="text" class="form-control has-feedback-left" placeholder="Enter your new password">
                <span class="fa fa-lock form-control-feedback left" aria-hidden="true"></span>
              </div>




              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                  <button type="submit" id="submit_account" class="btn btn-success">Submit</button>
                </div>
              </div>

            </form>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>



<?php
include_once "../footer.php";
?>
