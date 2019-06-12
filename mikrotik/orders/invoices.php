<?php
include_once "../header.php";
?>

<script>
    $(document).ready(function () {
        $('.dataTables_empty').html('<div class="loader"></div>');

        $('#myTable2').DataTable({
            "order": [[ 0, "desc" ]],
            "bProcessing": true,
            "serverSide": true,
            "ajax": {
                url: "<?= $api_url ?>orders/invoices_api.php?order_id=<?=$_GET['order_id']?>", // json datasource
                type: "post", // type of method  , by default would be get
                error: function () {  // error handling code
                    
                    $("#employee_grid_processing").css("display", "none");
                }
            }
        });
  
        <?php
            if($admin_type == 1){
        ?>
        $('body').on('dblclick', '.editable', function(){
            if($(this).children().length){
                
            }
            else{
                str = $(this).html();
                str = str.substring(0, str.length - 1);
                $(this).html("<input old-value='"+str+"'  style='color:black;' value='"+str+"'>"
                +"<button class=\"btn yes-change\"><i class=\"glyphicon glyphicon-ok\"></i></button>"
                +"<button class=\"btn no-change\"><i class=\"glyphicon glyphicon-remove\"></i></button>");
            }
            
        });
        
        
        $('body').on('click', '.yes-change', function(){
            
            var invoice_item_id = $(this).closest('tr').attr('id');
            var item_duration_price = $(this).parent().find("input").val();
  
            $.post("<?= $api_url ?>orders/invoice_edit_api.php",
                    {invoice_item_id: invoice_item_id, item_duration_price: item_duration_price}
                    , function (data, status) {
                        data = $.parseJSON(data);
                        if (data.updated == true) {
                            alert("Value updated");
                        } else
                            alert("Error, try again");
            });
            $(this).parent().html($(this).parent().find("input").val()+"$");
                
        });
        
        $('body').on('click', '.no-change', function(){
            $(this).parent().html($(this).parent().find("input").attr('old-value')+"$");
        });
        
        <?php
            }
        ?>
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

<title>Invoices</title>
<div class="page-header">
    <a class="last" href="">Invoices</a>
</div>

<table id="myTable2"  class="display table table-striped table-bordered">
    <thead>
    <th style="width: 10%">Invoice ID</th>
    <th style="width: 10%">From</th>
    <th style="width: 10%">To</th>
    <th style="width: 10%">Type</th>
    <th style="width: 40%">Items</th>
</thead>
<tbody>
</tbody>
</table>



<?php
include_once "../footer.php";
?>
