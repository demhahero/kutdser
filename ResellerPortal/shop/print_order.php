<?php

include_once "../dbconfig.php";
include "../../terms.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$order_id = intval(filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT));
$order = $dbTools->objOrderTools($order_id, 3);

$qst_tax = number_format((float) $order->getQSTTax(), 2, '.', '');
$gst_tax = number_format((float) $order->getGSTTax(), 2, '.', '');
$price_of_remaining_days = number_format((float) $order->getRemainingDaysPrice(), 2, '.', '');
$installation_transfer_cost = number_format((float) $order->getSetupPrice(), 2, '.', '');
$router_cost = number_format((float) $order->getRouterPrice(), 2, '.', '');
$modem_cost = number_format((float) $order->getModemPrice(), 2, '.', '');
$adapter_cost = number_format((float) $order->getAdapterPrice(), 2, '.', '');
$additional_service = number_format((float) $order->getAdditionalServicePrice(), 2, '.', '');
$static_ip = number_format((float) $order->getStaticIpPrice(), 2, '.', '');
$total_price = number_format((float) $order->getTotalPrice(), 2, '.', '');
$sub_total = number_format((float) $order->getTotalPrice()-$qst_tax-$gst_tax, 2, '.', '');
$product_price = number_format((float) $order->getProductPrice(), 2, '.', '');

$html = $terms_header . '
                    ' . $order->getCustomer()->getFullName() . '<br/>' . $order->getCustomer()->getAddress() . '
                </td>
		<td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $order->getReseller()->getFullName() . '<br/>' . $order->getReseller()->getAddress() . '
		</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
				<tr class="order-date">
					<th>Order:</th>
					<td>#' . $order->getDisplayedID() . '</td>
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
                    <span class="item-name">' . $order->getProduct()->getTitle() . '</span>
                    <dl class="meta">																</dl>
		</td>
		<td class="quantity">1</td>
		<td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $product_price . '</span></td>
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
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Remaining days</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $price_of_remaining_days . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Setup Costs</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $installation_transfer_cost . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Router Costs</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $router_cost . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Modem Costs</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $modem_cost . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Adapter Costs</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $adapter_cost . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Additional Service</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $additional_service . '</span></span></td>
						</tr>
                                                <tr class="fee_419">
                                                    <td class="no-borders"></td>
                                                    <th class="description">Static IP</th>
                                                    <td class="price"><span class="totals-price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $static_ip . '</span></span></td>
            </tr>
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
