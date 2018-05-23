<?php
include_once "../header.php";
?>



<script>
    $(document).ready(function () {
      var Mytable = $('#expireTable').DataTable( {

        "columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='btn btn-primary'>OK Noted</button>"
        } ]
    } );
    $('.dataTables_empty').html('<div class="loader"></div>');

    $('#expireTable tbody').on( 'click', 'button', function () {
        var data = Mytable.row( $(this).parents('tr') ).data();

        $.post("<?= $api_url ?>orders_expire.php",
        {
          order_expiration_notify_id: data[0]
        },
        function(data,status){
            //alert("Data: " + data + "\nStatus: " + status);
            data=JSON.parse(data);
            if(data.inserted)
            {
              Mytable.clear();
              $.getJSON("<?= $api_url ?>orders_expire.php", function (result) {
                if(result['orders'].length==0)
                {
                  Mytable.clear().draw();
                  $('.dataTables_empty').html('<div > No Data Found</div>');
                }
                $('#expireCount').html(result['orders'].length);
                  $.each(result['orders'], function (i, field) {
                      var customer_name=field["customer"][0]["full_name"];
                      var customer_id=field["customer"][0]["customer_id"];
                      var reseller_name=field["reseller"][0]["full_name"];
                      var reseller_id=field["reseller"][0]["customer_id"];



                      Mytable.row.add([
                          field['order_expiration_notify_id'],
                          customer_name,
                          reseller_name,
                          field['expiration_date'],
                          field['remaining_days']
                      ]).draw(false);
                  });
              });
            }
        });
    } );


        $.getJSON("<?= $api_url ?>orders_expire.php", function (result) {
          $('#expireCount').html(result['orders'].length);
            if(result['orders'].length==0)
            {
              $('.dataTables_empty').html('<div > No Data Found</div>');
            }
            $.each(result['orders'], function (i, field) {
                var customer_name=field["customer"][0]["full_name"];
                var customer_id=field["customer"][0]["customer_id"];
                var reseller_name=field["reseller"][0]["full_name"];
                var reseller_id=field["reseller"][0]["customer_id"];



                Mytable.row.add([
                    field['order_expiration_notify_id'],
                    customer_name,
                    reseller_name,
                    field['expiration_date'],
                    field['remaining_days']
                ]).draw(false);
            });
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

<title>Support</title>
<div class="page-header">
    <a class="last" href="">Support</a>
</div>
<table id="expireTable" class="display table table-striped table-bordered">
    <thead>
    <th style="width: 5%">ID</th>
    <th style="width: 17%">Full Name</th>
    <th style="width: 17%">Reseller</th>
    <th style="width: 13%">Expire Date</th>
    <th style="width: 13%">Remaining days</th>
    <th style="width: 13%">Action</th>
</thead>
<tbody>
</tbody>
</table>

<?php
include_once "../footer.php";
?>
