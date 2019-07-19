$(document).ready(function () {


    var product_type = "phone";

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
    product_type = "phone";


    //Steps Validation
    $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
        if (stepNumber == 0 && stepDirection == "forward") {
            priceCalculator();
            return validateChooseProduct();
        } else if (stepNumber == 1 && stepDirection == "forward")
            return validateCustomerInformation();
        return true;
    });

    function validateChooseProduct() {
         if (product_type == "phone") { // Check if he did not enter his current phone number
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
        if (product_type == "phone") {

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
            var static_ip = 0;
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

            //Get product
            product_price = parseFloat($("select[name=\"product\"] option:selected").attr("real_price"));
            if(has_discount)
              product_price = parseFloat($("select[name=\"product\"] option:selected").attr("price"));

            //product title

            var title=$("select[name=\"product\"] option:selected").attr("data_title")+" "+product_price
            if(has_discount)
               title=$("select[name=\"product\"] option:selected").text();
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

            $("div.order_details span.product-name").html(title);

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
