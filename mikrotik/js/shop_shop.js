$(document).ready(function () {

    var product_type = "internet";
    setupInternetForm();
    function setupInternetForm() {
      /*
        $("label.subscriber-on").hide();
        $("input.modem-off").prop("checked", true);
        $("input.router-off").prop("checked", true);
        $("input.subscriber-off").prop("checked", true);

        //$("div.modem-inventory-list").hide();
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
          /*
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
        */
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

        //$("input.plan-monthly").prop("checked", true);

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
    /*
    $("select[name=\"customer_id\"] option:first").attr("selected", "selected");


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
*/
/*
    //Steps Validation
    $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
        if (stepNumber == 1 && stepDirection == "forward") {
            priceCalculator();
            return validateChooseProduct();
        } else if (stepNumber == 2 && stepDirection == "forward")
            return validateCustomerInformation();
        return true;
    });
*/

    

/*
    //Set 1st product is monthly by default and disable yearly existed customers.
    //$("select[name=\"product\"] option:first").attr("selected", "selected");
    $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', false);
    $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', true);

    $("select[name=\"product\"]").change(function () {
        //If yearly, disable monthly existed customers
        if ($("select[name=\"product\"] option:selected").attr("type") == "yearly") {
            $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', true);
            $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', false);
            $("input.plan-monthly").prop('disabled', true); // NO plans if YEALY
            $("input.plan-monthly-2").prop('disabled', true);// NO plans if YEARLY
            $("input.rent-router").prop('disabled', true); // Rent rounter is disabled if yearly
            $("input.rent-router").prop('checked', false); // Remove check from rent rounter
        } else { //If monthly, disable yearly existed customers
            //alert(1);
            $("select[name=\"customer_id\"] option[type=\"yearly\"]").prop('disabled', true);
            $("select[name=\"customer_id\"] option[type=\"monthly\"]").prop('disabled', false);
            $("input.plan-monthly").prop('disabled', false);
            $("input.plan-monthly-2").prop('disabled', false);
            $("input.rent-router").prop('disabled', false);
        }
    });
*/
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
/*
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
*/
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

            //Get product price
            product_price = parseFloat($("select[name=\"product\"] option:selected").attr("price"));

            //If rent modem
            if ($("input[name=\"options[modem]\"]:checked").val() == "rent") {
                modem_cost = 59.90;

                //Deposit has no tax
                //value_has_no_tax = modem_cost;
            }

            if ($("input[name=\"options[router]\"]:checked").val() == "rent") { //If rent router
                router_cost = 2.90;
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
                        installation_transfer_cost = 19.90;
                    else
                        installation_transfer_cost = 60.00;
                }
            }

            //NOTICE: Have to be changed later
            installation_transfer_cost = 0;

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

            $("div.order_details span.product-name").html($("select[name=\"product\"]:enabled option:selected").text());

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

            //Get product price
            product_price = parseFloat($("select[name=\"product\"] option:selected").attr("price"));

            //If buy adapter
            if ($("input[name=\"options[adapter]\"]:checked").val() == "buy_Cisco_SPA112") {
                adapter_price = 59.90;
            }
            //NOTICE: Have to be changed later
            adapter_price = 0;

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

            $("div.order_details span.product-name").html($("select[name=\"product\"] option:selected").text());

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
/*
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
    */
});
