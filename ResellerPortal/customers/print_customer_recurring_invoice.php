<?php

include_once "../dbconfig.php";
include "../../terms.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));
$month = $_GET["month"];
$year = intval(filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT));

$customer = $dbTools->objCustomerTools($customer_id, 2);

$selectedDate = new DateTime("1" . "-" . $month . "-" . $year);
$orders = $customer->getRecurringOrdersByDate($selectedDate);

$rent_router_cost = 0;
$total_price_before_tax = 0;
$total_price_after_tax = 0;
$total_product_prices = 0;
$additional_service_cost = 0;
$qst_tax = 0;
$gst_tax = 0;

foreach ($orders as $order) {
    if ($order->getProduct()->getCategory() == "internet") {
        if ($order->getRouter() == "rent")
            $rent_router_cost = $order->getRouterPrice();

        $additional_service_cost = $order->getAdditionalServicePrice();
    }
    $total_product_prices += $order->getProductPrice();
}
$total_price_before_tax = $total_product_prices + $additional_service_cost + $rent_router_cost;
$qst_tax = $total_price_before_tax * 0.09975;
$gst_tax = $total_price_before_tax * 0.05;
$total_price_after_tax = $total_price_before_tax + $qst_tax + $gst_tax;

$html = $terms_header . '
                    ' . $customer->getFullName() . '<br/>' . $customer->getAddress() . '								
                </td>
		<td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $customer->getReseller()->getFullName() . '<br/>' . $customer->getReseller()->getAddress() . '
		</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Invoice:</th>
					<td>#' . $year . $month . $customer->getCustomerId(). '</td>
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
    $html .= '<tr class="415">
		<td class="product">
                    <span class="item-name">' . $order->getProduct()->getTitle() ." ". ($order->getTerminationDate() > $selectedDate)." ". '</span>
                    <dl class="meta">																</dl>
		</td>
		<td class="quantity">1</td>
		<td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $order->getProductPrice() . '</span></td>
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
