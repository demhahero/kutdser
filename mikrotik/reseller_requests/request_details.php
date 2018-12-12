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
        var verdict=$("input[name=\"reseller_request_items["+id+"][verdict]\"]").prop('checked');

        var verdict_reason=$("textarea[name=\"reseller_request_items["+id+"][verdict_reason]\"]").val();

        $.post("<?= $api_url ?>reseller_requests/request_details_api.php",
              {
                "post_action":"edit_reseller_request_item",
                "edit_id":id,
                "verdict":verdict,
                "verdict_reason":verdict_reason
              }
             , function (data, status) {
            data = $.parseJSON(data);
            if(data.edited===true)
            {
              $("#"+id+"").hide();
            }
            else {
              $('#message').html('<div class="alert alert-danger"><strong>Failed</strong> info didn\'t save</div>');
              window.scrollTo(0, 0);
            }

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
        columnDefs: [
                      {
                          targets: 4,
                          className: 'text-center'
                      }
                    ],
        "createdRow": function( row, data, dataIndex){
            if( data[6].indexOf('Save') >= 0){

              $(row).css({"background-color": "#ffffff"});

            }
            else if (data[6].indexOf('Update') >= 0) {
              $(row).css({"background-color": "#f2dede"});

            }
            else {

              $(row).css({"background-color": "#dff0d8"});
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

            var col1=reseller_request_item.modem_type;
            var col2=reseller_request_item.modem_mac_address;
            var col3=reseller_request_item.modem_serial_number;
            var col4=reseller_request_item.note;
            var checked_verdict="";
            var button_text="Save";
            if(reseller_request_item.verdict=="approve")
            {
              checked_verdict="checked";
            }
            else if (reseller_request_item.verdict=="disapprove") {
              button_text="Update";
            }
            var col5='<input type="checkbox" class="js-switch" '+checked_verdict+' name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][verdict]" /></br> Disapprove/Approve';
            var col6='<textarea name="reseller_request_items['+reseller_request_item.reseller_request_item_id+'][verdict_reason]">'+reseller_request_item.verdict_reason+'</textarea>';

            var col7='<span id="'+reseller_request_item.reseller_request_item_id+'" data-reseller-request-item-id="'+reseller_request_item.reseller_request_item_id+'" class="btn btn-primary update">'+button_text+'</span>';

            if(reseller_request_item.verdict=="approve" )
            {

              col5=reseller_request_item.verdict;
              col6=reseller_request_item.verdict_reason;
              col7="";

            }


            tableDetails.row.add([
              col1,
              col2,
              col3,
              col4,
              col5,
              col6,
              col7
            ]).draw( false );
          });


          }
          else{
            alert("error loading data, please contact admin");
          }
          if ($(".js-switch")[0]) {
              var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
              elems.forEach(function (html) {
                  var switchery = new Switchery(html, {
                      color: '#26B99A'
                  });
              });
          }
      });
    }
    $("#reseller_request_items").submit(function(e){
        e.preventDefault();
        var post_data=$("form").serialize();
        debugger;
        $.post("<?= $api_url ?>reseller_requests/request_details_api.php",
              post_data
             , function (data, status) {
            data = $.parseJSON(data);
            if(data.edited===true)
            {
              $('#message').html('<div class="alert alert-success"><strong>Success</strong>info saved</div>');
              refreshData();
            }
            else {
              $('#message').html('<div class="alert alert-danger"><strong>Failed</strong> info didn\'t save</div>');
            }
            window.scrollTo(0, 0);
          }
        );

      });
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
          <th>Verdict Reason</th>
          <th>Options</th>
      </thead>
      <tbody>

      </tbody>
    </table>
    <input type="hidden" name="post_action" value="edit_all_request_items"/>
    <button class="btn btn-primary">Save all</button>
  </form>
</div>
<?php
include_once "../footer.php";
?>
