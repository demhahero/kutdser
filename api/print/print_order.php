<?php

include_once "../dbconfig.php";
include "../../terms.php";
require_once '../../mikrotik/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$order_id = intval(filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT));
$query = "SELECT
          `orders`.`order_id`,
          `order_options`.`qst_tax`,
          `order_options`.`gst_tax`,
          `order_options`.`remaining_days_price`,
          `order_options`.`setup_price`,
          `order_options`.`router_price`,
          `order_options`.`modem_price`,
          `order_options`.`adapter_price`,
          `order_options`.`additional_service_price`,
          `order_options`.`total_price`,
          `order_options`.`product_price`,
          `orders`.`product_title`,

          `customers`.`full_name`,
          `customers`.`address`,
          `customers`.`city`,
          `customers`.`address_line_1`,
          `customers`.`address_line_2`,
          `customers`.`postal_code`,
          resellers.`full_name` as 'reseller_full_name',
          resellers.`address` as 'reseller_address',
          resellers.`city` as 'reseller_city',
          resellers.`address_line_1` as 'reseller_address_line_1',
          resellers.`address_line_2` as 'reseller_address_line_2',
          resellers.`postal_code` as 'reseller_postal_code'

          FROM `orders`
          LEFT JOIN `order_options` ON `order_options`.`order_id`= `orders`.`order_id`
          LEFT JOIN `customers` ON `orders`.`customer_id`=`customers`.`customer_id`
          LEFT JOIN `customers` resellers ON resellers.`customer_id` = `orders`.`reseller_id`

          WHERE `orders`.`order_id`=?";

        $stmt1 = $dbTools->getConnection()->prepare($query);


        $stmt1->bind_param('s',
                          $order_id
                          ); // 's' specifies the variable type => 'string'


        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $result = $dbTools->fetch_assoc($result1);
        $result["displayed_order_id"]=$result["order_id"];
        if ((int) $result["order_id"] > 10380)
            $result["displayed_order_id"] = (((0x0000FFFF & (int) $result["order_id"]) << 16).((0xFFFF0000 & (int) $result["order_id"]) >> 16));
        $result["full_address"]=$result['address'].$result['city']." " .
                $result['address_line_1']." ".$result['address_line_2']." " .
                $result['postal_code'];
        $result["reseller_full_address"]=$result['reseller_address'].$result['reseller_city']." " .
                $result['reseller_address_line_1']." ".$result['reseller_address_line_2']." " .
                $result['reseller_postal_code'];


$qst_tax = number_format((float) $result["qst_tax"], 2, '.', '');
$gst_tax = number_format((float) $result["gst_tax"], 2, '.', '');
$price_of_remaining_days = number_format((float) $result["remaining_days_price"], 2, '.', '');
$installation_transfer_cost = number_format((float) $result["setup_price"], 2, '.', '');
$router_cost = number_format((float) $result["router_price"], 2, '.', '');
$modem_cost = number_format((float) $result["modem_price"], 2, '.', '');
$adapter_cost = number_format((float) $result["adapter_price"], 2, '.', '');
$additional_service = number_format((float) $result["additional_service_price"], 2, '.', '');
$total_price = number_format((float) $result["total_price"], 2, '.', '');
$sub_total = number_format((float) $result["total_price"]-$qst_tax-$gst_tax, 2, '.', '');
$product_price = number_format((float) $result["product_price"], 2, '.', '');

$html = $terms_header . '
                    ' . $result["full_name"] . '<br/>' . $result["full_address"] . '
                </td>
		<td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $result["reseller_full_name"] . '<br/>' . $result["reseller_full_address"] . '
		</td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Invoice Date:</th>
					<td>' . date("Y/m/d") . '</td>
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
                    <span class="item-name">' . $result["product_title"] . '</span>
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
                                                    <th class="description">Installation Costs</th>
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
//
// // (Optional) Setup the paper size and orientation
 $dompdf->setPaper('A4', 'portrait');
//
// // Render the HTML as PDF
 $dompdf->render();
//
//
//
// // Output the generated PDF to Browser
 $dompdf->stream($result["displayed_order_id"].".pdf", array("Attachment" => false));


exit(0);
