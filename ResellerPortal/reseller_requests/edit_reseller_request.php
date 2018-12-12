<?php
include_once "../header.php";
?>
<?php
$edit_id=intval($_GET["reseller_request_id"]);

?>

<script>
    $(document).ready(function () {
      function isNumber(n) {
       return !isNaN(parseFloat(n)) && isFinite(n);
      }

      $(document).on('click', '.update', function(){
        var id=$(this).attr("data-reseller-request-item-id");
        var modem_type=$("input[name=\"reseller_request_items["+id+"][modem_type]\"]").val();
        var modem_mac_address=$("input[name=\"reseller_request_items["+id+"][modem_mac_address]\"]").val();
        var modem_serial_number=$("input[name=\"reseller_request_items["+id+"][modem_serial_number]\"]").val();

        var note=$("textarea[name=\"reseller_request_items["+id+"][note]\"]").val();

        $.post("<?= $api_url ?>reseller_requests/edit_reseller_request_api.php",
              {
                "post_action":"edit_reseller_request_item",
                "edit_id":id,
                "modem_type":modem_type,
                "modem_mac_address":modem_mac_address,
                "modem_serial_number":modem_serial_number,
                "note":note
              }
             , function (data, status) {
            data = $.parseJSON(data);
            if(data.edited===true)
            {
              $('#message').html('<div class="alert alert-success"><strong>Success</strong> info saved</div>');
            }
            else {
              $('#message').html('<div class="alert alert-danger"><strong>Failed</strong> info didn\'t save</div>');
            }
            window.scrollTo(0, 0);
          }
        );

      });
      $(document).on('click', '.delete', function(){
        var id=$(this).attr("data-reseller-request-item-id");
        $.post("<?= $api_url ?>reseller_requests/delete_reseller_request_api.php",
              {
                "post_action":"delete_reseller_request_item",
                "delete_id":id
              }
             , function (data, status) {
            data = $.parseJSON(data);
            if(data.deleted===true)
            {
              $('#message').html('<div class="alert alert-success"><strong>Success</strong> item deleted</div>');
              refreshData();
            }
            else {
              $('#message').html('<div class="alert alert-danger"><strong>Error:</strong> operation failed</div>');
            }
            window.scrollTo(0, 0);
          }
        );
      });
  function refreshData()
  {
    if ( $.fn.dataTable.isDataTable( '#myTable2' ) ) {
        var tableDetails = $('#myTable2').DataTable();
        tableDetails.destroy();
    }
    var tableDetailsTag=$('#myTable2');
    var tableDetails=tableDetailsTag.DataTable({
      "createdRow": function( row, data, dataIndex){
          if( data[4] ==  `approve`){

            $(row).css({"background-color": "#dff0d8"});

          }
          else if (data[4] ==  `disapprove`) {
            $(row).css({"background-color": "#f2dede"});
          }
          else {
            $(row).css({"background-color": "#ffffff"});
          }
      },
      "paging":   false,
      "ordering": false,
      "info":     false,
      "searching":false
    });
    tableDetails.clear().draw();
    $.post("<?= $api_url ?>reseller_requests/edit_reseller_request_api.php"
    ,{
          "edit_id":<?=$edit_id?>,
          "post_action": "get_reseller_request_by_id"
      }, function (data) {
      data = $.parseJSON(data);
      if (data.error != true) {
        $.each(data['reseller_request_items'], function (i, reseller_request_item) {

          var col1='<input type="text" name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][modem_type]" value="'+reseller_request_item.modem_type+'"/>';
          var col2='<input type="text" name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][modem_mac_address]" value="'+reseller_request_item.modem_mac_address+'"/>';
          var col3='<input type="text" name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][modem_serial_number]" value="'+reseller_request_item.modem_serial_number+'"/>';
          var col4='<textarea name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][note]">'+reseller_request_item.note+'</textarea>';
          var col5=reseller_request_item.verdict;
          var col6=reseller_request_item.verdict_date;
          var col7=reseller_request_item.verdict_reason;
          var col8='<span data-reseller-request-item-id="'+reseller_request_item.reseller_request_item_id+'" class="btn btn-primary update">Update</span>';
          var col9='<span data-reseller-request-item-id="'+reseller_request_item.reseller_request_item_id+'" class="btn btn-danger delete">Delete</span>';
          if(reseller_request_item.verdict=="approve")
          {
            col1=reseller_request_item.modem_type;
            col2=reseller_request_item.modem_mac_address;
            col3=reseller_request_item.modem_serial_number;
            col4=reseller_request_item.note;
            col8="";
            col9="";
          }


          tableDetails.row.add([
            col1,
            col2,
            col3,
            col4,
            col5,
            col6,
            col7,
            col8+
            col9
          ]).draw( false );
        });


        }
        else{
          alert("error loading data, please contact admin");
        }
    });
}
refreshData();

    });
</script>
<title>Edit reseller_request</title>
<div class="page-header">
    <h4>Edit reseller_request</h4>
</div>
<div id="message">

</div>

<div class="row">
  <form id="reseller_request_items">
    <table id="myTable2"  class="display table table-striped table-bordered">
        <thead>
          <th>Modem Type</th>
          <th>Mac address</th>
          <th>Serial Number</th>
          <th>Note</th>
          <th>Verdict</th>
          <th>Verdict Date</th>
          <th>Verdict Reason</th>
          <th>Options</th>
      </thead>
      <tbody>

      </tbody>
    </table>
  </form>
</div>
<?php
include_once "../footer.php";
?>
