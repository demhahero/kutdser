<?php
include_once "../header.php";
?>


<title>Make a request</title>

<script>
    $(document).ready(function () {
      function validateInputValues(){
        var validated=true;
        //////check if there are duplicated values
        $.each($('.modem_type'), function (index, modem_type) {
          if ($(modem_type).val().replace(/^\s+|\s+$/g, "").length == 0){
            validated=false;
            return false;
          }
        });
        $.each($('.modem_mac_address'), function (index, modem_mac_address) {
          if ($(modem_mac_address).val().replace(/^\s+|\s+$/g, "").length == 0){
            validated=false;
            return false;
          }
          $.each($('.modem_mac_address'), function (index, modem_mac_address2) {
            if ($(modem_mac_address2).val().replace(/^\s+|\s+$/g, "").length == 0){
              validated=false;
              return false;
            }
            if(modem_mac_address!=modem_mac_address2 && $(modem_mac_address).val()==$( modem_mac_address2 ).val())
            {
              validated=false;
              return false;
            }
          });
          if(!validated)
          {
            return false;
          }

        });
        if(!validated)
        {
          return false;
        }
        $.each($('.modem_serial_number'), function (index, modem_serial_number) {
          if ($(modem_serial_number).val().replace(/^\s+|\s+$/g, "").length == 0){
            validated=false;
            return false;
          }
          $.each($('.modem_serial_number'), function (index, modem_serial_number2) {
            if ($(modem_serial_number2).val().replace(/^\s+|\s+$/g, "").length == 0){
              validated=false;
              return false;
            }
            if(modem_serial_number!=modem_serial_number2 && $(modem_serial_number).val()==$( modem_serial_number2 ).val())
            {
              validated=false;
              return false;
            }
          });
          if(!validated)
          {
            return false;
          }

        });
        if(!validated)
        {
          return false;
        }
        $.each($('.modem_mac_address'), function (index, modem_mac_address) {
          var elem_name=$(modem_mac_address).attr('name');
          var modem_serial_number = elem_name.replace("modem_mac_address", "modem_serial_number");

            if($(modem_mac_address).val()==$("input[name=\""+modem_serial_number+"\"]").val())
            {
              validated=false;
              return false;
            }
          });

        return validated;
      }
      var count=0;
      var total=0;
      function add_row(withDelete=false){
        total++;
        var col1='<input class="modem_type" type="text" name="reseller_request_items['+count+'][modem_type]" />';
        var col2='<input class="modem_mac_address" type="text" name="reseller_request_items['+count+'][modem_mac_address]" />';
        var col3='<input class="modem_serial_number" type="text" name="reseller_request_items['+count+'][modem_serial_number]" />';
        var col4='<textarea name="reseller_request_items['+count+'][note]"></textarea>';
        var col5='<span class="btn btn-danger delete">Remove</span>';
        if(!withDelete)
          col5="";

        count++;

        tableDetails.row.add([
          col1,
          col2,
          col3,
          col4,
          col5
        ]).draw( false );
      }

      var tableDetailsTag=$('#myTable2');
      var tableDetails=tableDetailsTag.DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching":false
      });


      $('#myTable2 tbody').on( 'click', '.delete', function () {
          tableDetails
              .row( $(this).parents('tr') )
              .remove()
              .draw();
          total--;
      } );
      $("#add_new_row").click(function(){
        if(total<50)
        add_row(true);
      });

      add_row();

        $('#datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '+0d'
        });

        $(".add-form").submit(function(e){
            e.preventDefault();
            if(!validateInputValues())
            {
              alert("Either you have duplicated or empty value in one of the fields. Please, check them again before submitting.")
              return;
            }
            var post_data=$("form").serialize()

            $.post("<?= $api_url ?>reseller_requests/add_request_api.php",
                    post_data,
                     function (data, status) {
                      data = $.parseJSON(data);
                      if (data.inserted == true) {
                          alert("Request sent");
                          location.href = "reseller_requests.php";
                      } else {
                          if (data.error !== "null")
                              alert(data.error);
                          else
                              alert("Error, try again");
                      }

                  });
            return false;
        });


    });
</script>

<div id="message">

</div>
<form class="add-form" method="post">
    <div class="form-group">
        <label>Make a request:</label>

    </div>

    <div class="form-group">
        <label>Action:</label>
        <select name="action" class="form-control">
            <option data-value="add_modem" value="add_modem">Add Modem</option>
        </select>
    </div>
    <div class="form-group">
        <label>Action on date:</label>
        <input readonly="" name="action_on_date" type="text" id="datepicker" class="form-control" />
    </div>

<div class="row">
    <table id="myTable2"  class="display table table-striped table-bordered">
        <thead>
          <th>Modem Type</th>
          <th>Mac address</th>
          <th>Serial Number</th>
          <th>Note</th>
          <th></th>
      </thead>
      <tbody>

      </tbody>
    </table>
    <div style="text-align: right;">
        <span id="add_new_row" class="btn btn-primary"> Add new row</span>
    </div>


</div>
<input type="submit" class="btn btn-primary submit"  value="Send">
</form>
<?php
include_once "../footer.php";
?>
