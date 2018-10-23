<?php
include_once "../header.php";
?>

<script>
$(document).ready(function () {
    $('.dataTables_empty').html('<div class="loader"></div>');

    var table2=$('#myTable2').DataTable({
        "bProcessing": true,
        "serverSide": true,
        "ajax": {
            url: "<?= $api_url ?>routers/routers_api.php", // json datasource
            type: "post", // type of method  , by default would be get
            error: function () {  // error handling code
                $("#myTable2").css("display", "none");
            }
        }
    });
    $( "#myTable2 tbody" ).on( "click", ".edit", function() {
      var edit_id = $(this).attr('data-id');
      window.location.href = "edit_router.php?router_id="+edit_id;
    });
    $( "#myTable2 tbody" ).on( "click", ".remove", function() {

        var delete_id = $(this).attr('data-id');
        $.post("<?= $api_url ?>routers/delete_router_api.php",
                {
                  delete_id: delete_id
                }
        , function (data, status) {
            data = $.parseJSON(data);
            if (data.deleted == true) {
                alert("Record deleted");
                table2.ajax.reload();
            } else
                alert("Error: delete record failed, try again later");
        });
      });
});
</script>

<title>Routers</title>
<div class="page-header">
    <h4>Routers</h4>
</div>
<a href="create_router.php" class="btn btn-primary">+ Create</a>

<br><br>
<table id="myTable2"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Serial Number</th>
    <th>Reseller</th>
    <th>Customer</th>
    <th>Functions</th>
</thead>
<tbody>
    	
</tbody>
</table>

<?php
include_once "../footer.php";
?>
