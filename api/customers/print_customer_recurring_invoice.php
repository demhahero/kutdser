<?php
include_once "../dbconfig.php";
include "../../terms.php";
require_once '../../mikrotik/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
if(
	(isset($_GET['month']) && ctype_digit($_GET['month']) && ((int)$_GET['month'] >=1 && (int)$_GET['month'] <=12 ))
	&&
	(isset($_GET['year']) && ctype_digit($_GET['year']))
	&&
	(isset($_GET['customer_id']) && ctype_digit($_GET['customer_id']))
	){


$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));
$month = $_GET["month"];
$year = intval(filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT));


$ordersMonthly=$dbTools->orders_by_month($customer_id,$year,$month);

$ordersYearly=$dbTools->orders_by_month_yearly($customer_id,$year,$month);



$selectedDate = new DateTime("1" . "-" . $month . "-" . $year);

$orders=array_merge($ordersMonthly,$ordersYearly);

$rent_router_cost = 0;
$total_price_before_tax = 0;
$total_price_after_tax = 0;
$additional_service_cost = 0;
$qst_tax = 0;
$gst_tax = 0;


foreach ($orders as $order) {

  $rent_router_cost += $order["monthInfo"][0]["router_price"];
  $total_price_before_tax += $order["monthInfo"][0]["total_price_with_out_tax"];
  $total_price_after_tax += $order["monthInfo"][0]["total_price_with_tax_p7"];
  $additional_service_cost += $order["monthInfo"][0]["additional_service_price"];

  $qst_tax += $order["monthInfo"][0]["qst_tax"];
  $gst_tax += $order["monthInfo"][0]["gst_tax"];

}

$html = $terms_header . '
                    ' . $orders[0]["customer_name"] . '<br/>' . $orders[0]["address"] . '
                </td>
		<td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $orders[0]["reseller_name"] . '<br/>' . $orders[0]["reseller_address"] . '
		</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Invoice:</th>
					<td>#' . $year . $month . $orders[0]["customer_id"]. '</td>
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
	<tbody>';
foreach ($orders as $order):
  $product_price=$order["monthInfo"][0]["product_price"];
  $product_title=$order["monthInfo"][0]["product_title"];
  $days=$order["monthInfo"][0]["days"];
  if (isset($order["monthInfo"][0]["product_price_2"]) && $order["monthInfo"][0]["product_price_2"] !=="null")

  {
    $product_title=$order["monthInfo"][0]["product_title"]+" ("+$order["monthInfo"][0]["days"]+" days), "+$order["monthInfo"][0]["product_title_2"]+" ("+$order["monthInfo"][0]["days_2"]+" days)";
    $product_price=$order["monthInfo"][0]["product_price"]+"$  ("+$order["monthInfo"][0]["product_price_previous"]+"$), "+$order["monthInfo"][0]["product_price_2"]+"$ ("+$order["monthInfo"][0]["product_price_current"]+"$)";
  }
    $html .= '<tr class="415">
		<td class="product">
                    <span class="item-name">' . $product_title ."  ". '</span>
                    <dl class="meta">																</dl>
		</td>
		<td class="quantity">1</td>
		<td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $product_price . '</span></td>
            </tr>';
endforeach;
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
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Router Costs</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $rent_router_cost, 2, '.', '') . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Additional Service</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $additional_service_cost, 2, '.', '') . '</span></span></td>
						</tr>
						<tr class="fee_418">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (GST 5%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $gst_tax, 2, '.', '') . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Tax Fees (QST 9.975%)</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $qst_tax, 2, '.', '') . '</span></span></td>
						</tr>
						<tr class="payment_method">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Payment method</th>
                                                    <td class="price"><span class="totals-price">Cash on delivery</span></td>
						</tr>
                                                <tr class="payment_method">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Payment method</th>
                                                    <td class="price"><span class="totals-price">Cash on delivery</span></td>
						</tr>
                                                <tr class="order_total">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Subtotal</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $total_price_before_tax, 2, '.', '') . '</span></span></td>
						</tr>
						<tr class="order_total">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Total</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $total_price_after_tax, 2, '.', '') . '</span></span></td>
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
}