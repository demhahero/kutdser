<?php
include_once "../header.php";
?>
<title>Shop</title>
<form class="register-form" action="customers_information.php" method="post">
    <div class="form-group">
        <label>Speed:</label>
        <select name="speed" class="form-control">
            <?php
            $sql_customer = "SELECT
  wpp.ID,
  wpp.post_title,
  wppm.meta_key AS FIELD,
  wppm.meta_value AS VALUE,
  wppm.*
FROM wp_posts AS wpp
  LEFT JOIN wp_postmeta AS wppm
    ON wpp.ID = wppm.post_id
WHERE wpp.post_type = 'product'
      AND (wppm.meta_key = '_regular_price')
ORDER BY wpp.ID ASC, FIELD ASC, wppm.meta_id DESC;";

            $result_customer = $conn_wordpress->query($sql_customer);

            while ($row_customer = $result_customer->fetch_assoc()) {

                $product_name = $row_customer["post_title"];
                $product_id = $row_customer["post_id"];

                if (strpos($product_name, 'Internet') === false) {
                    continue;
                }

                echo "<option value='" . $product_id . "'>" . $product_name . "</option>\t\n";
            }
            ?>
        </select>
    </div>

    <div class="row" style="width:100% !important;">
        <div class="col-sm-12" >
            <p class="rounded form-row form-row-wide custom_check-service-availabilty  ">
            <div class="panel panel-danger">
                <div class="panel-heading">Check Service Availabilty</div>
                <div class="panel-body">


                    <a class="btn btn-info" id="check-service-availability" href="">
                        Check Service Availabilty
                    </a>


                </div>
            </div>
            </p>
        </div>
    </div>	

    <div class="row" style="width:100% !important;">
        <div class="col-sm-12" >
            <p class="rounded form-row form-row-wide custom_modem  ">
            <div class="panel panel-primary">
                <div class="panel-heading">Plan</div>
                <div class="panel-body">
                    <label class="radio-inline">
                        <input type="radio" checked  class="input-text plan plan-monthly custom-options custom_field" data-price="" name="options[plan]" value="monthly" />Monthly Payment ($60.00 New Installation Fees <b>OR</b> $19.90 Transfer Fees for <span style="color:red;">current Cable subscriber</span>)<br/>
                    </label><br/>	
                    <label class="radio-inline">	
                        <input type="radio" class="input-text plan plan-monthly-2 custom-options custom_field" data-price="" name="options[plan]" value="yearly"   />Yearly Contract, Payment Monthly (Free Installation)<br/>
                    </label><br/>
                </div>
            </div>
            </p>
        </div>
    </div>	

    <div class="row" style="width:100% !important;">
        <div class="col-sm-6" >
            <p class="rounded form-row form-row-wide custom_modem  ">
            <div class="panel panel-primary">
                <div class="panel-heading">Modem</div>
                <div class="panel-body">
                    <label class="radio-inline">
                        <input type="radio" class="input-text modem custom-options custom_field" data-price="60" name="options[modem]" value="rent" />Free Rent Modem ($59.90 deposit)
                    </label>	<br/>
                    <label class="radio-inline">		
                        <input type="radio" class="input-text modem-off modem custom-options custom_field" data-price="20" name="options[modem]" value="own_modem" />I have my own modem
                    </label><br/><br/>
                    <div class="modem-info">

                        <b>Enter modem Serial Number:</b><br/><input style="width: 100%;" type="text" class="input-text custom-options custom_field" data-price="0" name="options[modem_serial_number]" value="" />
                        <br/>

                        <b>Enter modem MAC address:</b><br/><input style="width: 100%;" type="text" class="input-text modem-off custom-options custom_field" data-price="0" name="options[modem_mac_address]" value="" />
                        <br/>

                        <b>Enter modem Type:</b><br/><input style="width: 100%;" type="text" class="input-text modem-off custom-options custom_field" data-price="0" name="options[modem_modem_type]" value="" />

                    </div>
                </div>
            </div>
            </p>
        </div>
        <div class="col-sm-6">
            <p class="rounded form-row form-row-wide custom_router  ">
            <div class="panel panel-primary">
                <div class="panel-heading">Router</div>
                <div class="panel-body">
                    <label class="radio-inline">
                        <input type="radio" class="input-text custom-options custom_field" data-price="2.90" name="options[router]" value="rent" />Rent WIFI Router MikroTik Hap Series ($2.90)<br/>
                    </label><br/>	
                    <label class="radio-inline">	
                        <input type="radio" class="input-text custom-options custom_field" data-price="74.00" name="options[router]" value="buy_hap_ac_lite"   />Buy WIFI Router MikroTik Hap ac lite ($74.00)<br/>
                    </label><br/>	
                    <label class="radio-inline">	
                        <input type="radio" class="input-text custom-options custom_field" data-price="39.90" name="options[router]" value="buy_hap_mini"   />Buy WIFI Router MikroTik Hap mini ($39.90)<br/>
                    </label><br/>		
                    <label class="radio-inline">
                        <input type="radio" class="input-text router-off custom-options custom_field" data-price="0" name="options[router]" value="dont_need" />I don't need a router
                    </label>
                </div>
            </div>
            </p>
        </div>
    </div>

    <div class="row"  style="width:100% !important;">


        <div class="col-sm-6">
            <p class="rounded form-row form-row-wide custom_are-you-currently-a-cable-subscriber  ">
            <div class="panel panel-primary">
                <div class="panel-heading">Are you currently a cable subscriber?</div>
                <div class="panel-body">
                    <label class="radio-inline">
                        <input type="radio" class="subscriber subscriber-on input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" value="yes" />Yes<br/>
                    </label><br/>
                    <label class="radio-inline">		
                        <input type="radio" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[cable_subscriber]" value="no" />No<br/>
                    </label>
                    </br>
                    <label class="subscriber-on">
                        </br></br>
                        Enter the name of your current cable provider:</br>
                        <select class="form-control" name="options[current_cable_provider]">

                            <option disabled="" selected="">Select a provider</option>
                            <option value="Acanac">Acanac</option>
                            <option value="ACN">ACN</option>
                            <option value="B2B2C">B2B2C</option>
                            <option value="CIK">CIK</option>
                            <option value="Distributel">Distributel</option>
                            <option value="Electronibox">Electronibox</option>
                            <option value="iTalk BB">iTalk BB</option>
                            <option value="Rogers">Rogers</option>
                            <option value="Shaw">Shaw</option>
                            <option value="TekSavvy">TekSavvy</option>
                            <option value="videotron">Videotron</option>
                            <option value="altimatel">Altimatel</option>
                            <option value="jamestelecom">James Telecom</option>
                            <option value="other">Other</option>
                        </select>
                        If other:</br>
                        <input type="text" class="subscriber subscriber-off input-text custom-options custom_field" data-price="0" name="options[subscriber_other]"  /><br>
                        Please select the cancellation date for your current Internet service:</br>

                        <div class="date4">
                            <div class="input-group input-append date" id="datePicker4">
                                <input readonly="readonly" type="text" name="options[cancellation_date]" class="form-control" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>

                    </label>
                </div>
            </div>
            </p>
        </div>
        <div class="col-sm-6">
            <p class="rounded  form-row form-row-wide custom_installation-date  ">
            <div class="panel panel-primary installation">
                <div class="panel-heading">Installation date</div>
                <div class="panel-body">
                    <label class="radio-inline">
                        <b>1st choice</b>

                        <div class="date1" style="display:none;">
                            <div class="input-group input-append date" id="datePicker1">
                                <input readonly="readonly" type="text" name="" class="form-control" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>

                        <div class="date5">
                            <div class="input-group input-append date" id="datePicker5">
                                <input readonly="readonly" type="text" name="options[installation_date_1]" class="form-control" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="before 12:00 PM"  />before 12:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="after 5:00 PM" />after 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_1]" value="All Day " />All Day  
                        </label><br>
                        <b>2nd choice</b>
                        <div class="date2">
                            <div class="input-group input-append date" id="datePicker2">
                                <input readonly="readonly" type="text" name="options[installation_date_2]" class="form-control" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="before 12:00 PM " />before 12:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="after 5:00 PM " />after 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_2]" value="All Day " />All Day  
                        </label><br>
                        <b>3rd choice</b>
                        <div class="date3">
                            <div class="input-group input-append date" id="datePicker3">
                                <input readonly="readonly" type="text" name="options[installation_date_3]" class="form-control" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="before 12:00 PM " />before 12:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="12:00 PM - 5:00 PM" />12:00 PM - 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="after 5:00 PM " />after 5:00 PM
                        </label>
                        <label class="radio-inline small">
                            <input type="radio" class="input-text custom-options custom_field" data-price="0" name="options[installation_time_3]" value="All Day " />All Day  
                        </label><br>
                    </label>
                </div>
            </div>




            </p>
        </div>
    </div> 



    <script>
        $(document).ready(function () {

            $("label.subscriber-on").hide();
            $("input.modem-off").prop("checked", true);
            $("input.router-off").prop("checked", true);
            $("input.subscriber-off").prop("checked", true);

            //jQuery( "div.modem-info" ).hide();

            $("input.subscriber").change(function () {
                if (this.value == "yes") {
                    $("div.installation").hide();
                    $("label.subscriber-on").show();
                } else {
                    $("div.installation").show();
                    $("label.subscriber-on").hide();
                }
            });

            $("input.modem").change(function () {
                if (this.value == "rent") {
                    $("div.modem-info").hide();
                } else {
                    $("div.modem-info").show();
                }
            });

            var currentDate = new Date();
            currentDate.setDate(currentDate.getDate() + 90);
            var fistDayInstallation = '3';
            var fistDayCancelattion = '2';
            if (currentDate.getDay() == "4") {
                fistDayInstallation = "5";
                fistDayCancelattion = "4";
            }

            $('#datePicker1').prop("readonly", true);
            $('#datePicker2').prop("readonly", true);
            $('#datePicker3').prop("readonly", true);
            $('#datePicker4').prop("readonly", true);
            $('#datePicker5').prop("readonly", true);

            $('#datePicker4').datepicker({
                format: 'mm/dd/yyyy',
                daysOfWeekDisabled: [0, 6],
                startDate: '+' + fistDayCancelattion + 'd'
            });
            $('#datePicker5').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '+' + fistDayInstallation + 'd',
                daysOfWeekDisabled: [0, 6],
                endDate: currentDate
            });
            $('#datePicker2').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '+' + fistDayInstallation + 'd',
                daysOfWeekDisabled: [0, 6],
                endDate: currentDate
            });
            $('#datePicker3').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '+' + fistDayInstallation + 'd',
                daysOfWeekDisabled: [0, 6],
                endDate: currentDate
            });

            $("input.plan-monthly").prop("checked", true);

            $('#check-service-availability').click(function () {
                PopupCenter("http://www.videotron.com/vcom/catalog/template/tool/_service_availability.jsp?lang=en&captchaRequired={1}&_requestid=3113680", "Check Service Availabilty", 600, 400);
                return false;
            });
        });

        function PopupCenter(url, title, w, h) {
            // Fixes dual-screen position                         Most browsers      Firefox
            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var left = ((width / 2) - (w / 2)) + dualScreenLeft;
            var top = ((height / 2) - (h / 2)) + dualScreenTop;
            var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

            // Puts focus on the newWindow
            if (window.focus) {
                newWindow.focus();
            }
        }
    </script>   
    <br>
    <br>
    <input type="submit" class="btn btn-success btn-block btn-lg"  value="Continue...">
</form>

<?php
include_once "../footer.php";
?>