<?php

include_once "../dbconfig.php";
include "../../terms.php";
require_once '../../mikrotik/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


$request_id = intval(filter_input(INPUT_GET, 'request_id', FILTER_VALIDATE_INT));
$query = "SELECT
          `orders`.`order_id` as 'customer_order_id',

          `requests`.`action` ,
          `requests`.`modem_id`,
          `requests`.`fees_charged`,
          `requests`.`verdict_date`,

          `customers`.`full_name` as 'customer_full_name',
          `customers`.`address` as 'customer_address',
          `customers`.`city` as 'customer_city',
          `customers`.`address_line_1` as 'customer_address_line_1',
          `customers`.`address_line_2` as 'customer_address_line_2',
          `customers`.`postal_code` as 'customer_postal_code',
          resellers.`full_name` as 'reseller_full_name',
          resellers.`address` as 'reseller_address',
          resellers.`city` as 'reseller_city',
          resellers.`address_line_1` as 'reseller_address_line_1',
          resellers.`address_line_2` as 'reseller_address_line_2',
          resellers.`postal_code` as 'reseller_postal_code'

          FROM `orders`
          LEFT JOIN `requests` ON `requests`.`order_id` = `orders`.`order_id`
          LEFT JOIN `customers` ON `orders`.`customer_id`=`customers`.`customer_id`
          LEFT JOIN `customers` resellers ON resellers.`customer_id` = `orders`.`reseller_id`

          WHERE `requests`.`request_id`=?";

        $stmt1 = $dbTools->getConnection()->prepare($query);




        $stmt1->bind_param('s',
                          $request_id
                          ); // 's' specifies the variable type => 'string'


        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $result = $dbTools->fetch_assoc($result1);
        if($result["action"]==="change_speed" && is_numeric($result["modem_id"])  && (int)$result["modem_id"] >0)
        {
          $result["action"]="swap_modem_and_change_speed";
        }
        $result["displayed_order_id"]=$result["customer_order_id"];
        if ((int) $result["customer_order_id"] > 10380)
            $result["displayed_order_id"] = (((0x0000FFFF & (int) $result["customer_order_id"]) << 16).((0xFFFF0000 & (int) $result["customer_order_id"]) >> 16));
        $result["full_address"]=$result['customer_address'].$result['customer_city']." " .
                $result['customer_address_line_1']." ".$result['customer_address_line_2']." " .
                $result['customer_postal_code'];
        $result["reseller_full_address"]=$result['reseller_address'].$result['reseller_city']." " .
                $result['reseller_address_line_1']." ".$result['reseller_address_line_2']." " .
                $result['reseller_postal_code'];

$title_array= array(
    "change_speed" => 'Change speed request',
    "customer_information_modification" => 'Customer information modification request',
    "moving" => 'Moving address request',
    "swap_modem" => 'Swap modem requst',
    "terminate" => 'Terminate request',
    "swap_modem_and_change_speed" => 'Swap modem and change speed request',
);

$request_fees=$result['fees_charged'];
$request_verdict_date=new DateTime($result['verdict_date']);
$title=$title_array[$result["action"]];
$qst_tax=$request_fees*0.09975;
$gst_tax=$request_fees*0.05;
$sub_total=$request_fees;
$total_price=$request_fees+$qst_tax+$gst_tax;

$request_fees = number_format((float)$request_fees, 2, '.', '');
$qst_tax = number_format((float)$qst_tax, 2, '.', '');
$gst_tax = number_format((float) $gst_tax, 2, '.', '');
$sub_total = number_format((float)$sub_total, 2, '.', '');
$total_price = number_format((float) $total_price, 2, '.', '');

$html = $terms_header . '
                    ' . $result["customer_full_name"] . '<br/>' . $result["full_address"] . '
                </td>
		<td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $result["reseller_full_name"] . '<br/>' . $result["reseller_full_address"] . '
		</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . $request_verdict_date->format("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Order:</th>
					<td>#' . $result["displayed_order_id"] . '</td>
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
            <tr class="415">
		<td class="product">
                    <span class="item-name">'.$title.'</span>
                    <dl class="meta">																</dl>
		</td>
		<td class="quantity">1</td>
		<td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $request_fees . '</span></td>
            </tr>
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

						<tr class="fee_418">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (GST 5%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $gst_tax . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (QST 9.975%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $qst_tax . '</span></span></td>
						</tr>
						<tr class="payment_method">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Payment method</th>
                                                    <td class="price"><span class="totals-price">Cash on delivery</span></td>
						</tr>
                                                <tr class="order_total">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Subtotal</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $sub_total . '</span></span></td>
						</tr>
						<tr class="order_total">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Total</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $total_price . '</span></span></td>
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



// Output the generated PDF to Browser
$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));


exit(0);
