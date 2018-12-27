<?php
include_once "../header.php";
$month=isset($_GET["month"])?$_GET["month"]:5;
$year=isset($_GET["year"])?$_GET["year"]:2018;
?>

<script>
    $(document).ready(function () {
      $('.loader').hide();
      $('.print_form').hide();
      var tableDetailsTag=$('#resellerTableDetails');
      var tableDetails=tableDetailsTag.DataTable({"pageLength": 14,"ordering": false});
        $('.dataTables_empty').html('<div class="loader"></div>');
      var customersData=[];
      var tableTag=$('#resellerTable');
      var table=tableTag.DataTable( {
        "drawCallback": function( settings ) {
            //alert( 'DataTables has redrawn the table' );
            $('[data-toggle="tooltip"]').tooltip();
        },
        "scrollX": true,   // enables horizontal scrolling
        "dom": 'Bfrtip',
        buttons: [
            //'copy', 'csv', 'excel', 'pdf', 'print'
            {
                extend: 'excel',
                messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.'
            }
        ],
        "createdRow": function ( row, data, index ) {
          //console.log(row);
          //console.log(data);
          //console.log(index);
          $('td', row).eq(5).addClass('bg-success');
          $('td', row).eq(7).addClass('bg-warning');
          $('td', row).eq(8).addClass('bg-danger');
        }
    } );
        $('.dataTables_empty').html('<div class="loader"></div>');
        function loadTable()
        {
          $('.loader').show();
          table.clear().draw();
          $.getJSON("<?= $api_url ?>statistics/orders_by_month_for_reseller.php?reseller_id=<?= $_GET["reseller_id"] ?>&month=<?= $month ?>&year=<?= $year ?>", function (result) {
            var total=0;
            var totalCB=0;
            var totalWoR=0;
  					var totalWT=0;
            var table_header="";
            var total_terminated_orders=0;
            var total_new_orders=0;
            var total_transfer_orders=0;
            $.each(result['customers'], function (index, customers) {
          					$.each(customers['orders'], function (i, field) {
                      table_header=field["reseller_name"]+"'s Customers STATISTICS for month <?=$month?> and year <?=$year?>";
                      $(".last").html(table_header);
                      var product_price;
                      var product_title;
                      var request_product_price;
                      var request_product_title;
                      var request_action_on_date;
                      var current_product_price;
                      var current_product_title;
                      $("table.orders").append(
                      '<tr>'
                          +'<td style="width:20%;">#'+(i+1)+'</td>'
                          +'<td >'+field["product_title"]+'</td>'
                          +'<td style="width:20%;">'+field["start_active_date"]+'</td>'
                          +'<td style="width:20%;">'+field["recurring_date"]+'</td>'
                          +'<td>'+field["product_price"]+'</td>'
        							+'</tr>');
                      $.each(field["requests"], function (i2, field2) {
                          $("table.requests").append('<tr><td>' + field2["action"] + '</td><td>' + field2["action_on_date"] + '</td><td>' + field2["product_title"] + "" + '</td><td>' + field2["product_price"] + '$</td></tr>');
                      });
                      				$.each(field["monthInfo"], function (i2, monthInfo) {
                      					var product_price=parseFloat(monthInfo["product_price"]).toFixed(2);
                      					var product_title=monthInfo["product_title"];
                      					var days=monthInfo["days"];
                                if (typeof monthInfo["product_price_2"] !== 'undefined' && monthInfo["product_price_2"] !== null)
                      					{
                      						product_title=monthInfo["product_title"]+" ("+monthInfo["days"]+" days), "+monthInfo["product_title_2"]+" ("+monthInfo["days_2"]+" days)";
                      						product_price=monthInfo["product_price"].toFixed(2)+"$  ("+monthInfo["product_price_previous"]+"$), "+monthInfo["product_price_2"].toFixed(2)+"$ ("+monthInfo["product_price_current"]+"$)";
                      					}
								//workaround negative values in commission base amount
								if(monthInfo["total_price_with_out_router"]<0 && monthInfo["action"]=="terminate")
								{
									monthInfo["total_price_with_out_router"]=parseFloat(field["yearlyInvoice"][0]["pricePrevious"]).toFixed(2);
									
									monthInfo["total_price_with_out_tax"]=parseFloat(monthInfo["total_price_with_out_router"])+82;
									var qst_tax=parseFloat(monthInfo["total_price_with_out_tax"])*0.09975;
									var gst_tax=parseFloat(monthInfo["total_price_with_out_tax"])*0.05;
                                    monthInfo["total_price_with_tax_p7"]=parseFloat(parseFloat(monthInfo["total_price_with_out_tax"])+qst_tax+gst_tax).toFixed(2);
									
									product_title=monthInfo["product_title"]+" for only "+field["yearlyInvoice"][0]["daysPrevious"]+" days";
								}

                                var discountText=" ";
                                if(field['discount']!=="0")
                                  discountText+="Product discount "+field['discount']+"%,";
                                if(field['free_router']==="yes")
                                  discountText+="Free Router,";
                                if(field['free_modem']==="yes")
                                  discountText+="Free Modem,";
                                if(field['free_setup']==="yes")
                                  discountText+="Free Setup,";
                                discountText=discountText.substring(0, discountText.length-1);
                                // var tooltipTex='</br><button type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="'+discountText+'">'
                                //                     +'<i class="fa fa-tags"></i>'
                                //                   +'</button>';
                                var tooltipTex='</br>With offer';
                                if(discountText.length===0){
                                  tooltipTex="";
                                }
                                var reseller_commission_percentage = result['reseller']["reseller_commission_percentage"];
                                if(field["reseller_commission_percentage"]>=0)
                                  reseller_commission_percentage = field["reseller_commission_percentage"];
                                var total_commission_base_amount_long=parseFloat((monthInfo["total_price_with_out_router"]*(reseller_commission_percentage/100)));
                                total_commission_base_amount=total_commission_base_amount_long.toFixed(2);
                                totalCB+= parseFloat(monthInfo["total_price_with_out_router"]);
                                totalWoR+= parseFloat(total_commission_base_amount);
                      					total+= parseFloat(monthInfo["total_price_with_out_tax"]);
                      					totalWT+= parseFloat(monthInfo["total_price_with_tax_p7"]);
                                var join_type=(field['cable_subscriber']==='yes')?"transfer":"new";
                                customersData[customers['customer_id']]=[];
                                customersData[customers['customer_id']].push({
                                  name:"ID",
                                  value:customers['customer_id']
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Full Name",
                                  value:customers['full_name']
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Product",
                                  value:product_title
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Payment Method",
                                  value:field['payment_method']
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Start Date",
                                  value:field['start_active_date']
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Join Type",
                                  value:join_type
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Recurring Start Date",
                                  value:field['recurring_date']
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Product Price",
                                  value:product_price
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Commission Base Amount",
                                  value:monthInfo["total_price_with_out_router"]
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Reseller Commission percentage",
                                  value:reseller_commission_percentage+"%"
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Monthly commission",
                                  value:total_commission_base_amount
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Type",
                                  value:monthInfo["action"]+tooltipTex
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Subtotal",
                                  value:monthInfo["total_price_with_out_tax"]
                                });
                                customersData[customers['customer_id']].push({
                                  name:"Total With Tax",
                                  value:monthInfo["total_price_with_tax_p7"]
                                });
                                table.row.add([
                                    customers['customer_id']+
                                    '<a target="_blank" href="<?=$api_url?>print/print_order.php?order_id='
                                            + field["order_id"] + '" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print </a>'
                                    ,
                                    customers['full_name'],
                                    product_title,
                                    product_price,
                                    monthInfo["total_price_with_out_router"],
                                    total_commission_base_amount,
                                    monthInfo["action"]+tooltipTex,
                                    monthInfo["total_price_with_out_tax"],
                                    monthInfo["total_price_with_tax_p7"],
                                    field['payment_method'],
                                    field['start_active_date'],
                                    join_type,
                                    field['recurring_date'],
                                    reseller_commission_percentage+"%" +'<a data-id="'+field['order_id']+'" data-id-2="'+field['reseller_commission_percentage']+'" type="button" class="btn btn-danger change_commission" >Change</a>',
                                    '<a data-id="'+customers['customer_id']+'" type="button" class="btn btn-primary openPopup" >Details</a>',
                                ]).draw(false);
                                if(join_type==='new' && monthInfo["action"].includes('order'))
                                {
                                  total_new_orders++;
                                }
                                else if (join_type==='transfer' && monthInfo["action"].includes('order'))
                                {
                                  total_transfer_orders++;
                                }
                                else if (monthInfo["action"].includes("terminated"))
                                {
                                  total_terminated_orders++;
                                }

                      });
                  });
                  });
                  $('.loader').hide();
                  $('.print_form').show();
                  $('#print_total_commission_base_amount').val(totalWoR.toFixed(2));
                  var MyMessageBottom='Commission Base amount :'+totalCB.toFixed(2)+'$, '
        							+'Total Price for subtotal :'+total.toFixed(2)+'$, '
        							+'Total Price for all orders With Tax :'+totalWT.toFixed(2)+'$';
                   MyMessageBottom+='Total Terminated Orders :'+total_terminated_orders+', '
        							+'Total New Orders :'+total_new_orders+', '
        							+'Total Transfer Order :'+total_transfer_orders+'';
                  var buttonCommon = {
                                          exportOptions: {
                                              format: {
                                                  body: function ( data, row, column, node ) {
                                                    customeData=data;
                                                      if(column === 0)
                                                      {
                                                        customeData=data.substr(0, data.indexOf('<'));
                                                      }
                                                      else if(column === 14){
                                                        customeData="";
                                                      }
                                                      return customeData;
                                                  }
                                              }
                                          }
                                      };
                  var tableOptions={
                    "drawCallback": function( settings ) {
                        //alert( 'DataTables has redrawn the table' );
                        $('[data-toggle="tooltip"]').tooltip();
                    },
                    dom: 'Bfrtip',
                    "scrollX": true,
                    buttons: [
                        //'copy', 'csv', 'excel', 'pdf', 'print'
                        $.extend( true, {}, buttonCommon, {
                            extend: 'excel',
                            className: 'btn btn-success',
                            messageTop: table_header,
                            title: table_header,
                            messageBottom: MyMessageBottom
                        }),
                        $.extend( true, {}, buttonCommon, {
                          extend: 'pdfHtml5',
                          orientation: 'landscape',
                          pageSize: 'LEGAL',
                          className: 'btn btn-danger',
                          messageTop: table_header,
                          title: table_header,
                          messageBottom: MyMessageBottom
                        })
                    ],
                    "createdRow": function ( row, data, index ) {
                      //console.log(row);
                      //console.log(data);
                      //console.log(index);
                      $('td', row).eq(5).addClass('bg-success');
                      $('td', row).eq(7).addClass('bg-warning');
                      $('td', row).eq(8).addClass('bg-danger');
                    }
                }
                  tableTag.DataTable().destroy()
                  tableTag.DataTable(tableOptions);
                  //$('.openPopup').click(function() {
                    $( "#resellerTable tbody" ).on( "click", ".openPopup", function() {
                    $('#myModal').modal({show:true});
                    var customer_id = $(this).attr('data-id');
                    tableDetails.clear().draw();
                    customersData[customer_id].map((item)=>{
                      tableDetails.row.add([
                          item.name,
                          item.value
                      ]).draw(false);
                    })
                  });
                  ////////////// add total prices for Commission base, all orders with tax and subtotal
        					$("#totalTable").html('<tr>'
                      +'<td  class="bg-default">Commission Base Amount </td>'
                      +'<td class="bg-default">'+totalCB.toFixed(2)+'$</td>'
                      +'<td  class="bg-success">Monthly commission </td>'
        							+'<td class="bg-success">'+totalWoR.toFixed(2)+'$</td>'
        							+'<td  class="bg-warning">Total Price for subtotal</td>'
        							+'<td class="bg-warning">'+total.toFixed(2)+'$</td>'
        							+'<td  class="bg-danger">Total Price for all orders With Tax</td>'
        							+'<td class="bg-danger">'+totalWT.toFixed(2)+'$</td>'
        							+'</tr>');
                ////////////////////////// add total terminated, new and transfer orders
                  $("#totalTable").append('<tr>'
                      +'<td colspan="2" class="bg-default">Total Terminated Orders </td>'
        							+'<td class="bg-default">'+total_terminated_orders+'</td>'
        							+'<td  class="bg-default">Total New Orders</td>'
        							+'<td class="bg-default">'+total_new_orders+'</td>'
        							+'<td colspan="2" class="bg-default">Total Transfer Orders</td>'
        							+'<td class="bg-default">'+total_transfer_orders+'</td>'
        							+'</tr>');
                  });

        }

        loadTable();

                $( "#resellerTable tbody" ).on( "click", ".change_commission", function() {
                    $('#change_commission').modal({show:true});
                    var order_id = $(this).attr('data-id');
                    var reseller_commission_percentage = $(this).attr('data-id-2');
                    $("input[name=\"order_id\"]").val(order_id);
                    $("input[name=\"reseller_commission_percentage\"]").val(reseller_commission_percentage);

                  });

                  //////////////// form post for reseller commission percentage
                  $( ".update-form" ).submit(function( event ) {
                      event.preventDefault();

                      var order_id=$("input[name=\"order_id\"]").val();
                      var reseller_commission_percentage=$("input[name=\"reseller_commission_percentage\"]").val();
                      $.post("<?= $api_url ?>statistics/update_order_reseller_commission_percentage_api.php",
                              {
                                "action":"update_order_reseller_commission_percentage",
                                "edit_id":order_id,
                                "reseller_commission_percentage": reseller_commission_percentage
                              }
                      , function (data, status) {
                          data = $.parseJSON(data);
                          if (data && data.updated == true) {
                              alert("Record updated");
                              $('#change_commission').modal("hide");
                              location.reload();
                          } else
                          {
                            alert("Error: udpate record failed, try again later");
                            $('#change_commission').modal("hide");
                          }

                      });
                    });
                  ///////////////// end form post
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

<title><?= "reseller" //$reseller->getFullName(); ?>'s customers</title>
<div class="page-header">
    <a href="resellers.php">Resellers</a>
    <span class="glyphicon glyphicon-play"></span>
    <a class="last" href=""></a>
</div>
<form class="register-form form-inline" method="get">
    <input name="reseller_id" style="display:none;" value="<?= $_GET["reseller_id"] ?>"/>
    <div class="form-group">
        <label for="email">Month:</label>
        <select  name="month" class="form-control">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                if ($month == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
        <label for="email">Year:</label>
        <select  name="year" class="form-control">
            <?php
            for ($i = 2017; $i <= 2020; $i++) {
                if ($year == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
    </div>
    <input type="submit" class="btn btn-default" value="Search">
</form>
<div class="loader"></div>
<form target="_blank" action="<?= $api_url ?>print/print_statement.php" class="print_form" method="POST">
  <input name="reseller_id" type="hidden" value="<?= $_GET["reseller_id"] ?>"/>
  <input name="month" type="hidden" value="<?= $month ?>"/>
  <input name="year" type="hidden" value="<?= $year ?>"/>
  <input id="print_total_commission_base_amount" name="total_commission_base_amount" type="hidden" value="0"/>
  <input type="submit" class="btn btn-success" value="Print Reseller Commission">
</form>
<br><br>
<table id="resellerTable" class="display table table-striped table-bordered">
    <thead>

    <th style="width:50px">ID</th>
    <th style="width:100px">Full Name</th>
    <th style="width:100px">Product</th>
    <th style="width:50px">Product Price</th>
    <th style="width:100px">Commission base amount</th>
    <th style="width:100px">Monthly commission</th>
    <th style="width:50px">Type</th>
    <th style="width:50px">Subtotal</th>
    <th style="width:50px">total with Tax </th>
    <th style="width:50px">Payment Method</th>
    <th style="width:100px">Start Date</th>
    <th style="width:50px">Join Type</th>
    <th style="width:100px">Recurring Start Date</th>
    <th style="width:50px">Reseller Commission percentage</th>
    <th style="width:50px">More Info</th>
</thead>
<tbody>


</tbody>
</table>
<table id="totalTable" class="display table table-striped table-bordered">

</table>
<p id="total_price_with_out_router"></p>
<p id="total_price_with_out_tax"></p>
<p id="total_price_with_tax_p7"></p>

<div id="myModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">More details</h4>
      </div>
      <div class="modal-body">
        <table id="resellerTableDetails" class="display table table-striped table-bordered">
            <thead>
            <th style="width:5%">Name</th>
            <th style="width:5%">Value</th>
        </thead>
        <tbody>


        </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>
  </div>
</div>


<div id="change_commission" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Change Reseller Commission</h4>
      </div>
      <div class="modal-body">

        <form class="update-form">
            <div class="form-group">
                <label>Reseller commission percentage for this order only:</label>
                <input type="number" min="-1" max="100" name="reseller_commission_percentage" value="-1" class="form-control" placeholder="Reseller commission percentage"/>
            </div>
                <input type="hidden" name="order_id" value="-1"/>
            <input type="submit" class="btn btn-primary" value="Change">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>
  </div>
</div>

<?php
include_once "../footer.php";
?>
