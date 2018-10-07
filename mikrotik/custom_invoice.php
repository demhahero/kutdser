<?php
include "../terms.php";
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
                            <option value="custom">Custom</option>
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
                foreach ($files as $file) {
                    $file_name = explode("_", $file);
                    $date = substr($file_name[2], 0, strrpos($file_name[2], "."));
                    $name = $file_name[1] . " : " . date('d/m/Y', $date);
                    echo "<li class=\"list-group-item\"><a target='_blank' href='custom_invoices/$file'>" . $name . "</a><span class=\"badge\"><a id='$file' class='send-invoice' href='javascript:{}' style='color:white;'>Send</a></span></li>";
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
                        '                    <option value="custom">Custom</option>' +
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

            $("tbody").on("change", "select", function () {
                if ($(this).val() == "custom") {
                    $(this).replaceWith(function () {
                        return $("<input name='product_name[]' />");
                    });
                }
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

$html = $terms_header . '
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
				<tr class="order-date">
					<th>Invoice:</th>
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
    $filePath = 'custom_invoices/invoice_' . $_POST["full_name"] . "_" . strtotime("now") . '.pdf';
    $output = $dompdf->output();
    file_put_contents($filePath, $output);

    //$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
    echo "<script>window.location.href ='" . $filePath . "'</script>";

    exit(0);
}
