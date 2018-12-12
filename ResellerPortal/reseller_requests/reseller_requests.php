<?php
include_once "../header.php";

?>
<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var data_id={
      "data_id":<?=$reseller_id?>
    };
    var table2=$('#myTable2').DataTable({
            "createdRow": function( row, data, dataIndex){
                if( data[4]==data[5]){

                  $(row).css({"background-color": "#dff0d8"});

                }
                else if (data[6]>0 ) {
                  $(row).css({"background-color": "#f2dede"});
                }
                else {
                  $(row).css({"background-color": "#ffffff"});
                }
            },
            "bProcessing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= $api_url ?>reseller_requests/requests_by_reseller_api.php", // json datasource
                "type": "post", // type of method  , by default would be get
                "data": data_id,

                error: function () {  // error handling code
                    $("#myTable2").css("display", "none");
                }
            }
        });

    $( "#myTable2 tbody" ).on( "click", ".view", function() {
      var edit_id = $(this).attr('data-id');
      window.location.href = "edit_reseller_request.php?reseller_request_id="+edit_id;
    });

    $(document).on('click', '.delete', function(){
      var id = $(this).attr('data-id');
      $.post("<?= $api_url ?>reseller_requests/delete_reseller_request_api.php",
            {
              "post_action":"delete_reseller_request",
              "delete_id":id
            }
           , function (data, status) {
          data = $.parseJSON(data);
          if(data.deleted===true)
          {
            $('#message').html('<div class="alert alert-success"><strong>Success</strong> item deleted</div>');

            table2.ajax.reload();
          }
          else {
            $('#message').html('<div class="alert alert-danger"><strong>Error:</strong> operation failed</div>');
          }
          window.scrollTo(0, 0);
        }
      );
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
<title>Requests</title>
<div class="page-header">
    <h4>Requests</h4>
</div>
<div class="row">
<a class="btn btn-primary" href="make_request.php">Make a request</a>
</br></br>
</div>
<div id="message">

</div>
<div class="row">
<table id="myTable2"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Action</th>
    <th>Creation Date</th>
    <th>Action Date</th>
    <th>Patch Size</th>
    <th>Approved</th>
    <th>Disapproved</th>
    <th>View</th>
</thead>
<tbody>

</tbody>
</table>
</div>

<?php
include_once "../footer.php";
?>
