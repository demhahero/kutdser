<?php
include "../terms.php";
if (!isset($_GET["do"])) {
    include_once "header.php";
    ?>

    <title>Create Custom Statement</title>
    <div class="page-header">
        <h4>Create Custom Statement | <a href="#previous_statements">Previous Statements</a></h4>    
    </div>
    <form class="register-form row " action="custom_statement.php?do=makePDF"  method="post">
        <div class="form-inline">
            <div class="form-group">
                <label>Statement for:</label>
                <input type="text" name="invoice_for" value="" class="form-control" placeholder="invoice for"/>
            </div>
            <div class="form-group">
                <label >full name:</label>
                <input type="text" name="full_name" value="" class="form-control" placeholder="full name"/>
            </div>
        </div>
        <br>
        <div class="form-group">
            <label>Address:</label>
            <textarea name="address" value="" class="form-control"/></textarea>
        </div>
        <br>
        <table class="table">
            <thead>
            <th>product name</th>
            <th>product quantity</th>
            <th>product price</th>
            <th class="reseller_discount">Reseller Discount</th>
            <th class="new_total">New Total</th>
            <th>product tax</th>
            <th class="amount">Amount</th>
            </thead>
            <tbody class="product-list">
                <tr>
                    <td>
                        <input type="text" name="product_name[]" class="form-control quantity" placeholder="product name"/>
                    </td>
                    <td>
                        <input type="text" name="product_quantity[]" class="form-control quantity" placeholder="product quantity"/>
                    </td>
                    <td>
                        <input type="text" name="product_price[]" class="form-control price" placeholder="product price"/>
                    </td>
                    <td>
                        <input type="text" name="product_reseller_discount[]" class="form-control price" placeholder="product reseller_discount"/>
                    </td>
                    <td>
                        <input type="text" name="product_new_total[]" class="form-control price" placeholder="product new_total"/>
                    </td>
                    <td>
                        <input type="text" name="product_tax[]" class="form-control price" placeholder="product tax"/>
                    </td>
                    <td>
                        <input type="text" name="product_amount[]" class="form-control price" placeholder="product amount"/>
                    </td>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <button class="btn btn-success btn-lg add-product">+</button>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>


        <input type="submit" class="btn btn-primary" value="create">

        <br><br>

        <div class="panel panel-info">
            <div class="panel-heading" id="previous_statements">Previous Statements:</div>
            <ul class="list-group" style="max-height: 200px; overflow-y:scroll;">
                <?php
                if ($handle = opendir('custom_statement')) {

                    while (false !== ($entry = readdir($handle))) {

                        if ($entry != "." && $entry != "..") {

                            echo "<li class=\"list-group-item\"><a target='_blank' href='custom_statement/$entry'>$entry</a><span class=\"badge\"><a id='$entry' class='send-statement' href='javascript:{}' style='color:white;'>Send</a></span></li>";
                        }
                    }

                    closedir($handle);
                }
                ?>
            </ul>
        </div>
    </form>

    <div id="dialog" title="Send Invoice">
        <form class="register-form" method="get" action="custom_statement_send_email.php">
            <div>
                <div class="form-group">
                    <label>To:</label>
                    <input type="text" name="to" value="" class="form-control" placeholder="To (Email)"/>
                </div>
                <div class="form-group">
                    <label >Message:</label>
                    <textarea name="body" class="form-control">Dear Sir,
Please, find the attached file.
Best,</textarea>
                </div>
                <div class="form-group">

                    <input type="text" hidden="" class="filename"  name="filename" value="" class="form-control" placeholder="full name"/>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Send">
        </form>
    </div>    
    
    <script>
        $(document).ready(function () {
            $("#subtotal").val("0");
            $("button.add-product").click(function () {
                var myvar = '<tr>' +
                        '            <td>' +
                        '                <input type="text" name="product_name[]" class="form-control quantity" placeholder="product name"/>' +
                        '            </td>' +
                        '            <td>' +
                        '                <input type="text" name="product_quantity[]" class="form-control quantity" placeholder="product quantity"/>' +
                        '            </td>' +
                        '            <td>' +
                        '                <input type="text" name="product_price[]" class="form-control price" placeholder="product price"/>' +
                        '            </td>' + 
                        '           <td>' +
                        '               <input type="text" name="product_reseller_discount[]" class="form-control price" placeholder="product reseller_discount"/>' +
                        '           </td>' +
                        '           <td>' +
                        '               <input type="text" name="product_new_total[]" class="form-control price" placeholder="product new_total"/>' +
                        '           </td>' +
                        '            <td>' +
                        '                <input type="text" name="product_tax[]" class="form-control price" placeholder="product tax"/>' +
                        '            </td>' +
                        '           <td>' +
                        '               <input type="text" name="product_amount[]" class="form-control price" placeholder="product amount"/>' +
                        '           </td>' +
                        '        </tr>';
                $(".product-list").append(myvar);
                return false;
            });
            $("tbody").on("change", "tr .quantity,.price", function () {
                $("#subtotal").val("0");
                $(".quantity").each(function () {
                    var price = $(this).parent().parent().find(".price");
                    //alert(price.val());
                    $("#subtotal").val(parseInt($("#subtotal").val()) + parseInt(price.val() * $(this).val()));
                });
                var total = parseInt($("#subtotal").val());
                var tax_value = 0;

                var gst = (total * 0.0975).toFixed(2);
                var qst = (total * 0.05).toFixed(2);

                $("#gst").val(gst);
                $("#qst").val(qst);
                if ($(".istax").is(":checked")) {
                    tax_value = (total * 0.1475);
                }

                total = total + tax_value;
                $("#total").val(total.toFixed(2));
            });
            $(".istax").change(function () {
                if ($(this).is(":checked")) {
                    $(".tax-box").show();
                } else {
                    $(".tax-box").hide();

                }
                $(".quantity").change();
            });
            
            
            $("#dialog").dialog({
                autoOpen: false,
                modal: true,
                height: 400,
                width: 400,
                open: function (ev, ui) {
                }
            });
            $("a.send-statement").click(function () {
                $('#dialog').dialog('open');
                $(".filename").val($(this).attr("id"));
                return false;
            });
        });
    </script>
    <?php
    include_once "footer.php";
}


require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$html = '<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Invoice</title>
	<style type="text/css">/* Main Body */
@font-face {
	font-family: \'Open Sans\';
	font-style: normal;
	font-weight: normal;
	src: local(\'Open Sans\'), local(\'OpenSans\'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/yYRnAC2KygoXnEC8IdU0gQLUuEpTyoUstqEm5AMlJo4.ttf) format(\'truetype\');
}
@font-face {
	font-family: \'Open Sans\';
	font-style: normal;
	font-weight: bold;
	src: local(\'Open Sans Bold\'), local(\'OpenSans-Bold\'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/k3k702ZOKiLJc3WVjuplzMDdSZkkecOE1hvV7ZHvhyU.ttf) format(\'truetype\');
}
@font-face {
	font-family: \'Open Sans\';
	font-style: italic;
	font-weight: normal;
	src: local(\'Open Sans Italic\'), local(\'OpenSans-Italic\'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/O4NhV7_qs9r9seTo7fnsVCZ2oysoEQEeKwjgmXLRnTc.ttf) format(\'truetype\');
}
@font-face {
	font-family: \'Open Sans\';
	font-style: italic;
	font-weight: bold;
	src: local(\'Open Sans Bold Italic\'), local(\'OpenSans-BoldItalic\'), url(http://themes.googleusercontent.com/static/fonts/opensans/v7/PRmiXeptR36kaC0GEAetxrQhS7CD3GIaelOwHPAPh9w.ttf) format(\'truetype\');
}

@page {
	margin-top: 1cm;
	margin-bottom: 3cm;
	margin-left: 2cm;
	margin-right: 2cm;
}
body {
	background: #fff;
	color: #000;
	margin: 0cm;
	font-family: \'Open Sans\', sans-serif;
	font-size: 9pt;
	line-height: 100%; /* fixes inherit dompdf bug */
}

h1, h2, h3, h4 {
	font-weight: bold;
	margin: 0;
}

h1 {
	font-size: 16pt;
	margin: 5mm 0;
}

h2 {
	font-size: 14pt;
}

h3, h4 {
	font-size: 9pt;
}


ol,
ul {
	list-style: none;
	margin: 0;
	padding: 0;
}

li,
ul {
	margin-bottom: 0.75em;
}

p {
	margin: 0;
	padding: 0;
}

p + p {
	margin-top: 1.25em;
}

a { 
	border-bottom: 1px solid; 
	text-decoration: none; 
}

/* Basic Table Styling */
table {
	border-collapse: collapse;
	border-spacing: 0;
	page-break-inside: always;
	border: 0;
	margin: 0;
	padding: 0;
}

th, td {
	vertical-align: top;
	text-align: left;
}

table.container {
	width:100%;
	border: 0;
}

tr.no-borders,
td.no-borders {
	border: 0 !important;
	border-top: 0 !important;
	border-bottom: 0 !important;
	padding: 0 !important;
	width: auto;
}

/* Header */
table.head {
	margin-bottom: 12mm;
}

td.header img {
	max-height: 3cm;
	width: auto;
}

td.header {
	font-size: 16pt;
	font-weight: 700;
}

td.shop-info {
	width: 40%;
}
.document-type-label {
	text-transform: uppercase;
}

/* Recipient addressses & order data */
table.order-data-addresses {
	width: 100%;
	margin-bottom: 10mm;
}

td.order-data {
	width: 40%;
}

.invoice .shipping-address {
	width: 30%;
}

.packing-slip .billing-address {
	width: 30%;
}

td.order-data table th {
	font-weight: normal;
	padding-right: 2mm;
}

/* Order details */
table.order-details {
	width:100%;
	margin-bottom: 8mm;
}

.quantity,
.price {
	width: 20%;
}

.order-details tr {
	page-break-inside: always;
	page-break-after: auto;	
}

.order-details td,
.order-details th {
	border-bottom: 1px #ccc solid;
	border-top: 1px #ccc solid;
	padding: 0.375em;
}

.order-details th {
	font-weight: bold;
	text-align: left;
}

.order-details thead th {
	color: white;
	background-color: black;
	border-color: black;
}

/* product bundles compatibility */
.order-details tr.bundled-item td.product {
	padding-left: 5mm;
}

.order-details tr.product-bundle td,
.order-details tr.bundled-item td {
	border: 0;
}


/* item meta formatting for WC2.6 and older */
dl {
	margin: 4px 0;
}

dt, dd, dd p {
	display: inline;
	font-size: 7pt;
	line-height: 7pt;
}

dd {
	margin-left: 5px;
}

dd:after {
	content: "\A";
	white-space: pre;
}
/* item-meta formatting for WC3.0+ */
.wc-item-meta {
	margin: 4px 0;
	font-size: 7pt;
	line-height: 7pt;
}
.wc-item-meta p {
	display: inline;
}
.wc-item-meta li {
	margin: 0;
	margin-left: 5px;
}

/* Notes & Totals */
.customer-notes {
	margin-top: 5mm;
}

table.totals {
	width: 100%;
	margin-top: 5mm;
}

table.totals th,
table.totals td {
	border: 0;
	border-top: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
}

table.totals th.description,
table.totals td.price {
	width: 50%;
}

table.totals tr.order_total td,
table.totals tr.order_total th {
	border-top: 2px solid #000;
	border-bottom: 2px solid #000;
	font-weight: bold;
}

table.totals tr.payment_method {
	display: none;
}

font.tax-number{
    font-size: 7pt;
    position: absolute;
    bottom: -1cm;
}

/* Footer Imprint */
#footer {
	position: absolute;
	bottom: -2cm;
	left: 0;
	right: 0;
	text-align: center;
	border-top: 0.1mm solid gray;
	margin-bottom: 0;
	padding-top: 2mm;
}

/* page numbers */
.pagenum:before {
	content: counter(page);
}
.pagenum,.pagecount {
	font-family: sans-serif;
}</style>
	<style type="text/css"></style>
</head>
<body class="invoice">

<table class="head container">
	<tr>
		<td class="header">
		<img src="logo-1.png" width="516" height="300" alt="AM Pro Telecom Inc." />		</td>
		<td class="shop-info">
			<div class="shop-name"><h3>AM Pro Telecom Inc.</h3></div>
			<div class="shop-address"><p>4230, Boul St Jean<br />
Dollard des ormuex<br />
#221<br />
H9H3X4<br />
Canada-Qc</p>
</div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
Statement</h1>


<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
                    <!-- <h3>Billing Address:</h3> -->' . '
                    ' .  $_POST["full_name"] . '<br/>' . $_POST["address"] . '								
                </td>
		<td class="address shipping-address">
					</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Statement Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Statement For:</th>
					<td>' . $_POST["invoice_for"] . '</td>
				</tr>
				<tr class="order-date">
					<th>Statement:</th>
					<td>' . uniqid() . '</td>
				</tr>		

							</table>			
		</td>
	</tr>
</table>


<table class="order-details">
	<thead>
            <tr>
                <th class="product">Product</th>
                <th class="quantity">Quantity</th>
                <th class="price">Price</th>
                <th class="tax">Reseller Discount</th>
                <th class="reseller_discount">New Total</th>
                <th class="new_total">Tax</th>
                <th class="amount">Amount</th>
            </tr>
	</thead>
	<tbody>
';
for ($i = 0; $i < 9; $i++) {
    if ($_POST["product_name"][$i] != "")
        $html .= '<tr class="415">
                    <td class="product">
                        <span class="item-name">' . $_POST["product_name"][$i] . '</span>
                        <dl class="meta">																</dl>
                    </td>
                    <td class="quantity">' . $_POST["product_quantity"][$i] . '</td>
                    <td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["product_price"][$i] . '</span></td>
                    <td class="reseller_discount"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["product_reseller_discount"][$i] . '</span></td> 
                    <td class="new_total"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["product_new_total"][$i] . '</span></td> 
                    <td class="tax"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["product_tax"][$i] . '</span></td>     
                    <td class="amount"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["product_amount"][$i] . '</span></td>     
                </tr>';
}
$html .= '
	</tbody>
	<tfoot>
	</tfoot>
</table>
' . $terms_footer;



// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();


if ($_GET["do"] == "makePDF") {
// Output the generated PDF to Browser
    $filePath = 'custom_statement/statement_' . $_POST["full_name"] . "_" . strtotime("now") . '.pdf';
    $output = $dompdf->output();
    file_put_contents($filePath, $output);

    //$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
    echo "<script>window.location.href ='" . $filePath . "'</script>";

    exit(0);
}
