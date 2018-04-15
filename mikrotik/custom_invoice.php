<?php
if (!isset($_GET["do"])) {
    include_once "header.php";
    ?>

    <title>Create Custom Invoice</title>
    <div class="page-header">
        <h4>Create Custom Invoice | <a href="#previous_invoices">Previous Invoices</a></h4>    
    </div>
    <form class="register-form row " action="custom_invoice.php?do=makePDF"  method="post">
        <div class="form-inline">
            <div class="form-group">
                <label>invoice for:</label>
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
                <tr>
                    <th>product name</th>
                    <th>product quantity</th>
                    <th>product price</th>
                    <th>delete</th>
                </tr>
            </thead>
            <tbody class="product-list">
                <tr>
                    <td>
                        <select name="product_name[]" value="" class="form-control">
                            <option value="Setup fees">Setup fees</option>
                            <option value="Recurring payment">Recurring payment</option>
                            <option value="Installation fee">Installation fee</option>
                            <option value="Transfer fee">Transfer fee</option>
                            <option value="Hitron modem">Hitron modem</option>
                            <option value="TC 4300 modem">TC 4300 modem</option>
                            <option value="Router hap mini">Router hap mini</option>
                            <option value="Router hap series">Router hap series</option>
                            <option value="Swap">Swap</option>
                            <option value="Upgrade and Downgrade">Upgrade and Downgrade</option>
                            <option value="Monthly charge and Setup fees">Monthly charge and Setup fees</option>
                            <option value="Installation fees (promotion)">Installation fees (promotion)</option>
                            <option value="Transfer fees (promotion)">Transfer fees (promotion)</option>
                            <option value="Phone Adapter (promotion)">Phone Adapter (promotion)</option>
                            <option value="Canada Phone charge">Canada Phone charge</option>
                            <option value="Phone Adapter">Phone Adapter</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="product_quantity[]" class="form-control quantity" placeholder="product quantity"/>
                    </td>
                    <td>
                        <input type="text" name="product_price[]" class="form-control price" placeholder="product price"/>
                    </td>
                    <td>
                        <a href="" class="delete-item glyphicon glyphicon-remove"></a>
                    </td>
                </tr>
                <tr>

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
                </tr>
            </tfoot>
        </table>

        <input type="checkbox" name="istax" class="istax" /> Tax<br/><br/>
        <div class="form-inline tax-box" style="display: none;">
            <div class="form-group">
                <label for="gst">gst:</label>
                <input id="gst" type="text" name="gst" class="form-control" value="9.75"/>
            </div>
            <div class="form-group">
                <label for="qst">qst:</label>
                <input id="qst" type="text" name="qst" class="form-control" value="5"/>
            </div>
        </div>
        <br>

        <div class="form-group">
            <label for="subtotal">Subtotal:</label>
            <input id="subtotal" type="text" name="subtotal" class="form-control" placeholder="subtotal"/>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment method:</label>
            <input id="payment_method" type="text" name="payment_method" class="form-control" placeholder="Payment method"/>
        </div>     
        <div class="form-group">
            <label for="total">Total:</label>
            <input id="total" type="text" name="total" class="form-control" placeholder="total"/>
        </div>

        <input type="submit" class="btn btn-primary" value="create">

        <br><br>

        <div class="panel panel-info">
            <div class="panel-heading" id="previous_invoices">Previous Invoices:</div>
            <ul class="list-group" style="max-height: 200px; overflow-y:scroll;">
                <?php
                $files = array();
                if ($handle = opendir('custom_invoices')) {
                    while (false !== ($entry = readdir($handle))) {  
                        if ($entry != "." && $entry != "..") {
                            $files[] = $entry;  
                        }
                    }
                    closedir($handle);
                }
                sort($files);
                foreach($files as $file){
                    echo "<li class=\"list-group-item\"><a target='_blank' href='custom_invoices/$file'>$file</a><span class=\"badge\"><a id='$file' class='send-invoice' href='javascript:{}' style='color:white;'>Send</a></span></li>";
                }
                ?>
            </ul>
        </div>
    </form>

    <div id="dialog" title="Send Invoice">
        <form class="register-form" method="get" action="custom_invoice_send_email.php">
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
                        '                <select name="product_name[]" value="" class="form-control">' +
                        '                    <option value="Setup fees">Setup fees</option>' +
                        '                    <option value="Recurring payment">Recurring payment</option>' +
                        '                    <option value="Installation fee">Installation fee</option>' +
                        '                    <option value="Transfer fee">Transfer fee</option>' +
                        '                    <option value="Hitron modem">Hitron modem</option>' +
                        '                    <option value="TC 4300 modem">TC 4300 modem</option>' +
                        '                    <option value="Router hap mini">Router hap mini</option>' +
                        '                    <option value="Router hap series">Router hap series</option>' +
                        '                    <option value="Swap">Swap</option>' +
                        '                    <option value="Upgrade and Downgrade">Upgrade and Downgrade</option>' +
                        '                    <option value="Monthly charge and Setup fees">Monthly charge and Setup fees</option>' +
                        '                    <option value="Installation fees (promotion)">Installation fees (promotion)</option>' +
                        '                    <option value="Transfer fees (promotion)">Transfer fees (promotion)</option>' +
                        '                    <option value="Phone Adapter (promotion)">Phone Adapter (promotion)</option>' +
                        '                    <option value="Canada Phone charge">Canada Phone charge</option>' +
                        '                    <option value="Phone Adapter">Phone Adapter</option>' +
                        '                </select>' +
                        '            </td>' +
                        '            <td>' +
                        '                <input type="text" name="product_quantity[]" class="form-control quantity" placeholder="product "/>' +
                        '            </td>' +
                        '            <td>' +
                        '                <input type="text" name="product_price[]" class="form-control price" placeholder="product "/>' +
                        '            </td>' +
                        '            <td>' +
                        '                <a href="" class="delete-item glyphicon glyphicon-remove"></a>' +
                        '            </td>' +
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

            $("tbody").on("click", "a.delete-item", function () {
                $(this).parent().parent().remove();
                return false;
            });

            $("#dialog").dialog({
                autoOpen: false,
                modal: true,
                height: 400,
                width: 400,
                open: function (ev, ui) {
                }
            });
            $("a.send-invoice").click(function () {
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
Invoice</h1>


<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
                    <!-- <h3>Billing Address:</h3> -->
                    ' . $_POST["full_name"] . '<br/>' . $_POST["address"] . '								
                </td>
		<td class="address shipping-address">
					</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Invoice For:</th>
					<td>' . $_POST["invoice_for"] . '</td>
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
                </tr>';
}
$html .= '
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="customer-notes">
																			</div>				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<tr class="cart_subtotal">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Subtotal</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["subtotal"] . '</span></span></td>
						</tr>';

if (isset($_POST["istax"])) {
    $html .= '<tr class="fee_418">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (GST 5%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["gst"] . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (QST 9.75%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["qst"] . '</span></span></td>
						</tr>';
}
$html .= '<tr class="payment_method">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Payment method</th>
                                                    <td class="price"><span class="totals-price">' . $_POST["payment_method"] . '</span></td>
						</tr>
						<tr class="order_total">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Total</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $_POST["total"] . '</span></span></td>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>


<div id="footer">
	<p>Website: www.amprotelecom.com | Phone: +1-514-548-2555 | Email: Info@amprotelecom.com</p>
</div><!-- #letter-footer -->




<style>
div.break { page-break-before: always; }
div.terms { text-align: justify;  text-justify: inter-word; }
</style>
<div class="break"></div>
<br/>
<br/>
<center><h1>AM PRO TELECOM TERMS AND CONDITIONS</h1></center>

The next following Terms and Conditions will be apply to all AM PRO TELECOM Services.
<br/><br/><br/>
<div class="terms">

<h2>Service Charge:</h2><br/>
The services that we provide are prepaid services. your first payment includes one-time fee, and after that it will be monthly payment depending on the plan that you registered in. you will be responsible to make all the payment in time before the due date. Balance that remain unpaid after the due date will be charged interest  rate of 2% pre month.
In case of a failure to make payment, AM Pro Telecom have the right to suspend and or terminate your service at any time with or without further notice. To reactivate or re-installation the service you may be charged a reactivation or re-installation fee. 
<br/><br/><br/>

<h2>Services Installation:</h2><br/>
you may ask the technician to install the service at your place if the functional jack is available there. In case of there is no or broken functional jake, the technician will instal the service to where a functional jack is available.The technician may or may not connect your modem.you will be responsible to connect devices.
<br/><br/><br/>

<h2>Cancellation:</h2><br/>
-If you subscribe to one year contract or one year prepaid Internet Service, in case of early cancellation the installation fee that was concede at the registration will be charged back, or a fee of $90  will be charged.
<br/><br/>
-AM Pro Telecom have all the right to apply your deposit without  notice,in case of any unpaid balances ,and the remaining balance will be sent to you within ten days after return of all the leased equipment in good working condition with complete parts, failure to do so within ten business days after the service stop, you will be charged for the full cost of any equipment that not returned toAM Pro Telecom.
<br/><br/><br/>

<h2>Moving your Service to another place:</h2><br/>
There is a fee of $90 will be applied to all our services.
<br/><br/><br/>

<h2>Limited  Liability:</h2><br/>
AM Pro Telecom will make every effort to provide hight quality service. however, if the services are interrupted or have lower performed than what expected for more than 72 consecutive hours, AM Pro Telecom&rsquo;s liability will be limited to crediting you the service fee only for the period in question. 
<br/><br/>


</div>
<div id="footer">
	<p>Website: www.amprotelecom.com | Phone: +1-514-548-2555 | Email: Info@amprotelecom.com</p>
</div><!-- #letter-footer -->

</body>
</html>';



// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();


if ($_GET["do"] == "makePDF") {
// Output the generated PDF to Browser
    $filePath = 'custom_invoices/invoice_' . $_POST["full_name"] . "_" . strtotime("now") . '.pdf';
    $output = $dompdf->output();
    file_put_contents($filePath, $output);

    //$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
    echo "<script>window.location.href ='" . $filePath . "'</script>";

    exit(0);
}
