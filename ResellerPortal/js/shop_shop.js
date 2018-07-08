$(document).ready(function () {


    var product_type = "internet";

    function setupInternetForm() {
        $("label.subscriber-on").hide();
        $("input.modem-off").prop("checked", true);
        $("input.router-off").prop("checked", true);
        $("input.subscriber-off").prop("checked", true);

        $("div.modem-inventory-list").hide();
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
                $("div.modem-inventory-list").hide();
                $("div.modem-info").hide();
            } else if (this.value == "own_modem") {
                $("div.modem-inventory-list").hide();
                $("div.modem-info").show();
            } else if (this.value == "inventory") {
                $("div.modem-info").hide();
                $("div.modem-inventory-list").show();
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
    }
    $("select[name=\"customer_id\"] option:first").attr("selected", "selected");

    // Step show event
    $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
        //alert("You are on step "+stepNumber+" now");
        if (stepPosition === 'first') {
            $("#prev-btn").addClass('disabled');
        } else if (stepPosition === 'final') {
            $("#next-btn").addClass('disabled');
        } else {
            $("#prev-btn").removeClass('disabled');
            $("#next-btn").removeClass('disabled');
        }
    });

    // Smart Wizard
    $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'arrows',
        transitionEffect: 'fade',
        showStepURLhash: true
    });

    // Set selected theme on page refresh
    $("#theme_selector").change();
    $('#smartwizard').smartWizard("reset");

    var temp_content = null;
    var last_temp = "internet";
    //Select Internet product
    $(".product-internet").click(function () {
        product_type = "internet";
        //if 1st time, remove phone form and save it in temp_content
        if (temp_content == null) {
            temp_content = $("div.phone").html();
            $("div.phone").remove();
        } else if (temp_content != null && last_temp == "phone") {
            temp_content2 = $("div.phone").html();
            $("div#step-2").html("<div class='internet'>" + temp_content + "</div>");
            temp_content = temp_content2;
            $("div.phone").remove();
        }
        last_temp = "internet";
        setupInternetForm();
        $('#smartwizard').smartWizard("next");
        return false;
    });

    //Select Phone product
    $(".product-phone").click(function () {
        product_type = "phone";

        //if 1st time, remove internet form and save it in temp_content
        if (temp_content == null) {
            temp_content = $("div.internet").html();
            $("div.internet").remove();
        } else if (temp_content != null && last_temp == "internet") {
            temp_content2 = $("div.internet").html();
            $("div#step-2").html("<div class='phone'>" + temp_content + "</div>");
            temp_content = temp_content2;
            $("div.internet").remove();
        }
        last_temp = "phone";

        $('#smartwizard').smartWizard("next");
        return false;
    });

    //Steps Validation
    $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
        if (stepNumber == 1 && stepDirection == "forward") {
            priceCalculator();
            return validateChooseProduct();
        } else if (stepNumber == 2 && stepDirection == "forward")
            return validateCustomerInformation();
        return true;
    });

    function validateChooseProduct() {
        if (product_type == "internet") {
            //If own_moden selected, you have to enter modem information
            if ($("input[name=\"options[modem]\"]:checked").val() == "own_modem") {
                if ($("input[name=\"options[modem_serial_number]\"]").val().length < 3
                        || $("input[name=\"options[modem_mac_address]\"]").val().length < 3
                        || $("input[name=\"options[modem_modem_type]\"]").val().length < 3
                        ) {
                    alert("Enter modem information");
                    return false;
                }
            } else if ($("input[name=\"options[modem]\"]:checked").val() == "inventory") { // if inventory selected and has no modem
                if ($("select[name=\"options[modem_id]\"] option:selected").val() == null) {
                    alert("You have no modems in your inventory");
                    return false;
                }
            }

            //If customer is currently a cable subscriber, he has to enter his provider name and cancellation date.
            if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "yes") {
                if (($("select[name=\"options[current_cable_provider]\"]").val().length < 3 && $("input[name=\"options[subscriber_other]\"]").val().length < 3)
                        || $("input[name=\"options[cancellation_date]\"]").val().length < 3
                        ) {
                    alert("Enter current provider's name and cancellation date");
                    return false;
                }
            }

            //If customer is not a cable subscriber, he has to pick dates and times for installation
            if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "no") {
                if ($("input[name=\"options[installation_date_1]\"]").val().length < 3
                        || $("input[name=\"options[installation_date_2]\"]").val().length < 3
                        || $("input[name=\"options[installation_date_3]\"]").val().length < 3
                        || $("input[name=\"options[installation_time_1]\"]:checked").val().length < 3
                        || $("input[name=\"options[installation_time_2]\"]:checked").val().length < 3
                        || $("input[name=\"options[installation_time_3]\"]:checked").val().length < 3
                        ) {
                    alert("Enter three dates and times for installation");
                    return false;
                }
            }

            return true;
        } else if (product_type == "phone") { // Check if he did not enter his current phone number
            if ($("input[name=\"options[you_have_phone_number]\"]:checked").val() == "yes"
                    && $("input[name=\"options[current_phone_number]\"]").val() == "") {
                alert("Enter your current phone number");
                return false;
            }
            return true;
        }
    }
    var has_discount_input=$("#has_discount").val();
    var free_modem_input=$("#free_modem").val();
    var free_router_input=$("#free_router").val();
    var free_adapter_input=$("#free_adapter").val();
    var free_installation_input=$("#free_installation").val();
    var free_transfer_inpu=$("#free_transfer").val();

    $("#has_discount").val("no");
    $("#free_modem").val("no");
    $("#free_router").val("no");
    $("#free_adapter").val("no");
    $("#free_installation").val("no");
    $("#free_transfer").val("no");
    //Set 1st product is monthly by default and disable yearly existed customers.
    $("select[name=\"product\"] option:first").attr("selected", "selected");
    $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', false);
    $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', true);

    $("select[name=\"product\"]").change(function () {

        //If yearly, disable monthly existed customers
        if ($("select[name=\"product\"] option:selected").attr("type") == "yearly") {

          $("#has_discount").val(has_discount_input);
          $("#free_modem").val(free_modem_input);
          $("#free_router").val(free_router_input);
          $("#free_adapter").val(free_adapter_input);
          $("#free_installation").val(free_installation_input);
          $("#free_transfer").val(free_transfer_inpu);

            $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', true);
            $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', false);
            $("input.plan-monthly").prop('disabled', true); // NO plans if YEALY
            $("input.plan-monthly-2").prop('disabled', true);// NO plans if YEARLY
            $("input.rent-router").prop('disabled', true); // Rent rounter is disabled if yearly
            $("input.rent-router").prop('checked', false); // Remove check from rent rounter
        } else { //If monthly, disable yearly existed customers
            //alert(1);
            $("#has_discount").val("no");
            $("#has_discount").val("no");
            $("#free_modem").val("no");
            $("#free_router").val("no");
            $("#free_adapter").val("no");
            $("#free_installation").val("no");
            $("#free_transfer").val("no");
            $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', true);
            $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', false);
            $("input.plan-monthly").prop('disabled', false);
            $("input.plan-monthly-2").prop('disabled', false);
            $("input.rent-router").prop('disabled', false);
        }
    });

    //////////// calculate discount only if yealry subscription selected


    $("#has_discount").val("no");
    $('input[type=radio][name=\'options[plan]\']').change(function() {
        if (this.value == 'yearly') {

            $("#has_discount").val(has_discount_input);
            $("#free_modem").val(free_modem_input);
            $("#free_router").val(free_router_input);
            $("#free_adapter").val(free_adapter_input);
            $("#free_installation").val(free_installation_input);
            $("#free_transfer").val(free_transfer_inpu);
        }
        else if (this.value == 'monthly') {

          $("#has_discount").val("no");
          $("#free_modem").val("no");
          $("#free_router").val("no");
          $("#free_adapter").val("no");
          $("#free_installation").val("no");
          $("#free_transfer").val("no");
        }
    });


    function validateCustomerInformation() {
        if ($("select[name=\"customer_id\"]  option:selected").val() == "0") {
            if ($("input[name=\"full_name\"]").val().length < 3
                    || $("input[name=\"email\"]").val().length < 3
                    || $("input[name=\"phone\"]").val().length < 3
                    || $("input[name=\"address_line_1\"]").val().length < 3
                    || $("input[name=\"postal_code\"]").val().length < 3
                    || $("input[name=\"city\"]").val().length < 3) {
                alert("Invalid input");
                return false;
            }
        }
        return true;
    }

    function validateCardInfo() {
        if ($("input[name=\"card_holders_name\"]").val().length < 3
                || $("input[name=\"card_cvv\"]").val().length != 3
                || $("input[name=\"card_number\"]").val().length < 3
                || $("input[name=\"card_expiry\"]").val().length != 4) {
            alert("Invalid input");
            return false;
        }
        return true;
    }

    $("input.checkout-button").click(function () {

        //if checkout clicked and its a new customer, validate credit info
        if ($("select[name=\"card_type\"]  option:selected").val() == "cache_on_delivery") {
                return true;
        } else{
            if (!validateCardInfo())
                return false;
            else
                return true;
        }
    });

    //Create or already existed customer
    $(".new-customer-form").show();
    $("select[name=\"customer_id\"]").change(function () {
        if ($(this).val() == "0") {
            $(".new-customer-form").show();
        } else {
            $(".new-customer-form").hide();
        }
    });

    function priceCalculator() {
        if (product_type == "internet") {
            var total_price = 0;
            var product_price = 0;
            var price_of_remainig_days = 0;
            var installation_transfer_cost = 0;
            var router_cost = 0;
            var modem_cost = 0;
            var start_date = ""; // When will the customer join
            var remainigDays = 0; //Remaining days in the month
            var value_has_no_tax = 0; // Exclude items that have no tax such as deposits
            var gst_tax = 0;
            var qst_tax = 0;
            var additional_service = 0;
            var has_discount=false;
            var free_modem=false;
            var free_router=false;
            var free_adapter=false;
            var free_installation=false;
            var free_transfer=false;


            //Get product price
            has_discount = $("input[name=\"has_discount\"]").val()==='yes';
            free_modem = $("input[name=\"free_modem\"]").val()==='yes';
            free_router = $("input[name=\"free_router\"]").val()==='yes';
            free_adapter = $("input[name=\"free_adapter\"]").val()==='yes';
            free_installation = $("input[name=\"free_installation\"]").val()==='yes';
            free_transfer = $("input[name=\"free_transfer\"]").val()==='yes';

            product_price = parseFloat($("select[name=\"product\"] option:selected").attr("real_price"));
            if(has_discount)
              product_price = parseFloat($("select[name=\"product\"] option:selected").attr("price"));

            //product title

            var title=$("select[name=\"product\"] option:selected").attr("data_title")+" "+product_price
            if(has_discount)
              var title=$("select[name=\"product\"] option:selected").text();
            //If rent modem
            if ($("input[name=\"options[inventory_modem_price]\"]").prop('checked') == true)
            {
              modem_cost = 59.90;
            }
            if ($("input[name=\"options[modem]\"]:checked").val() == "rent" ) {
                modem_cost = 59.90;
                if(has_discount && free_modem)
                modem_cost=0;
                //Deposit has no tax
                //value_has_no_tax = modem_cost;
            }

            if ($("input[name=\"options[router]\"]:checked").val() == "rent") { //If rent router
                router_cost = 2.90;
                if(has_discount && free_router)
                router_cost=0;
            } else if ($("input[name=\"options[router]\"]:checked").val() == "buy_hap_ac_lite") { //if buy hap ac lite
                router_cost = 74.00;
            } else if ($("input[name=\"options[router]\"]:checked").val() == "buy_hap_mini") { //if buy hap mini
                router_cost = 39.90;
            }

            //Check additional service
            if ($("input[name=\"options[additional_service]\"]").prop('checked') == true) {
                additional_service = 5;

                //if yearly, multiply additional+service by 12 months.
                if ($("select[name=\"product\"] option:selected").text().includes("Yearly") != false) {
                    additional_service = additional_service * 12;
                }
            }

            //if NOT yearly payment, check monthly (no contract) for transfer or installation fees.
            if ($("select[name=\"product\"] option:selected").text().includes("Yearly") == false) {
                if ($("input[name=\"options[plan]\"]:checked").val() == "monthly") {
                    if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "yes")
                    {
                      installation_transfer_cost = 19.90;
                      if(has_discount && free_transfer)
                      installation_transfer_cost=0;
                    }
                    else
                    {
                      installation_transfer_cost = 60.00;
                      if(has_discount && free_installation)
                      installation_transfer_cost=0;
                    }


                }
            }



            //if transfer
            if ($("input[name=\"options[cable_subscriber]\"]:checked").val() == "yes") {

                //Convert cancellation_date to Date
                start_date = parseDate($("input[name=\"options[cancellation_date]\"]").val());

                //Calculate the number of days in this month
                var days_in_month = parseInt(daysInMonth(start_date.getMonth(), start_date.getYear()));

                //Calculate the remaining days in this month
                remainigDays = days_in_month - start_date.getDate() + 1;
            } else { //if new installation

                //Convert installation_date_1 to Date
                start_date = parseDate($("input[name=\"options[installation_date_1]\"]").val());

                //Calculate the number of days in this month
                var days_in_month = parseInt(daysInMonth(start_date.getMonth(), start_date.getYear()));

                //Calculate the remaining days in this month
                remainigDays = days_in_month - start_date.getDate() + 1;
            }



            //If 1st day of month, pay 1 month only.
            if (parseInt(start_date.getDate()) == 1) {
                remainigDays = 0;
                price_of_remainig_days = 0;
            } else {
                //Calculate the price of the remaining days
                if ($("select[name=\"product\"] option:selected").text().includes("Yearly") != false) { //if yearly payment, divide price by 12 months
                    //Calculate additional + product for the rest of the month
                    price_of_remainig_days = parseFloat((product_price / 12) / days_in_month) * remainigDays;
                    price_of_remainig_days += parseFloat(additional_service / days_in_month) * remainigDays;
                } else {
                    //Calculate additional + product + router rent for the rest of the month
                    price_of_remainig_days = parseFloat(product_price / days_in_month) * remainigDays;
                    price_of_remainig_days += parseFloat(additional_service / days_in_month) * remainigDays;
                    if ($("input[name=\"options[router]\"]:checked").val() == "rent") {
                        price_of_remainig_days += parseFloat(router_cost / days_in_month) * remainigDays;
                    }
                }
            }

            //Calculate total price
            total_price = product_price + price_of_remainig_days + installation_transfer_cost + router_cost + modem_cost + additional_service;

            //Calculate texes
            qst_tax = (total_price - value_has_no_tax) * 0.09975;
            gst_tax = (total_price - value_has_no_tax) * 0.05;

            //Add taxes to total price
            total_price += qst_tax + gst_tax;

            //Display price list
            var monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            //if first day, don't show remaining days from to
            if (parseInt(start_date.getDate()) != 1) {
                $("div.order_details span.remaining-days-from-to").html("From: " + start_date.getDate() + "/" + monthNames[start_date.getMonth()] + " To " + days_in_month + "/" + monthNames[start_date.getMonth()]);
            }

            $("div.order_details span.remaining-days-cost").html("$" + price_of_remainig_days.toFixed(2));

            $("div.order_details span.setup-cost").html("$" + installation_transfer_cost.toFixed(2));

            $("div.order_details span.modem-cost").html("$" + modem_cost.toFixed(2));

            $("div.order_details span.router-cost").html("$" + router_cost.toFixed(2));

            $("div.order_details span.additional-service-cost").html("$" + additional_service.toFixed(2));

            $("div.order_details span.qst-cost").html("$" + qst_tax.toFixed(2));

            $("div.order_details span.gst-cost").html("$" + gst_tax.toFixed(2));

            $("div.order_details span.product-name").html(title);

            $("div.order_details span.total").html("$" + total_price.toFixed(2));
        } else if (product_type == "phone") {
            var total_price = 0;
            var product_price = 0;
            var price_of_remainig_days = 0;
            var adapter_price = 0;
            var transfer_price = 0;
            var start_date = ""; // When will the customer join
            var remainigDays = 0; //Remaining days in the month
            var value_has_no_tax = 0; // Exclude items that have no tax such as deposits
            var gst_tax = 0;
            var qst_tax = 0;
            var additional_service = 0;

            //Get product
            product_price = parseFloat($("select[name=\"product\"] option:selected").attr("real_price"));
            if(has_discount)
              product_price = parseFloat($("select[name=\"product\"] option:selected").attr("price"));

            //If buy adapter
            if ($("input[name=\"options[adapter]\"]:checked").val() == "buy_Cisco_SPA112") {
                adapter_price = 59.90;
                if(has_discount && free_adapter)
                adapter_price=0;
            }
            //NOTICE: Have to be changed later
            //adapter_price = 0;

            //If transfer
            if ($("input[name=\"options[you_have_phone_number]\"]:checked").val() == "yes") {
                transfer_price = 15;
            }

            //today is the start day
            start_date = new Date();

            //Calculate the number of days in this month
            var days_in_month = parseInt(daysInMonth(start_date.getMonth(), start_date.getYear()));

            //Calculate the remaining days in this month
            remainigDays = days_in_month - start_date.getDate() + 1;

            if (parseInt(start_date.getDate()) == 1) {
                remainigDays = 0;
                price_of_remainig_days = 0;
            } else {
                //Calculate the price of the remaining days
                if ($("select[name=\"product\"] option:selected").text().includes("1 year") != false) { //if yearly payment, divide price by 12 months
                    //Calculate product for the rest of the month
                    price_of_remainig_days = parseFloat((product_price / 12) / days_in_month) * remainigDays;
                } else {
                    //Calculate product for the rest of the month
                    price_of_remainig_days = parseFloat(product_price / days_in_month) * remainigDays;
                }
            }

            //Calculate total price
            total_price = product_price + price_of_remainig_days + transfer_price + adapter_price;

            //Calculate texes
            qst_tax = (total_price - value_has_no_tax) * 0.09975;
            gst_tax = (total_price - value_has_no_tax) * 0.05;

            //Add taxes to total price
            total_price += qst_tax + gst_tax;

            //Display price list
            var monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            //if first day, don't show remaining days from to
            if (parseInt(start_date.getDate()) != 1) {
                $("div.order_details span.remaining-days-from-to").html("From: " + start_date.getDate() + "/" + monthNames[start_date.getMonth()] + " To " + days_in_month + "/" + monthNames[start_date.getMonth()]);
            }

            $("div.order_details span.remaining-days-cost").html("$" + price_of_remainig_days.toFixed(2));

            $("div.order_details span.setup-cost").html("$" + transfer_price.toFixed(2));

            $("div.order_details span.adapter-cost").html("$" + adapter_price.toFixed(2));

            $("div.order_details span.qst-cost").html("$" + qst_tax.toFixed(2));

            $("div.order_details span.gst-cost").html("$" + gst_tax.toFixed(2));

            $("div.order_details span.product-name").html($("select[name=\"product\"] option:selected").attr("data_title")+" "+product_price);

            $("div.order_details span.total").html("$" + total_price.toFixed(2));
        }
    }

    //Convert string to a Date
    function parseDate(str) {
        var mdy = str.split('/');
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }

    //Get number of days in a specific month
    function daysInMonth(month, year) {
        return new Date(year, month+1, 0).getDate();
    }

    //if customer has phone, show the current phone field
    $("label.current-phone-subscriber").hide();
    $("input.new-number").prop("checked", true);
    $("input.phone-subscriber").change(function () {
        if (this.value == "yes") {
            $("label.current-phone-subscriber").show();
        } else {
            $("label.current-phone-subscriber").hide();
        }
    });
});
