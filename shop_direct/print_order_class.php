<?php

include_once "dbconfig.php";
include "../terms.php";
require_once '../ResellerPortal/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PrintOrder {

    function output($order_id) {
        global $dbToolsReseller, $terms_header, $terms_footer;
        $order = $dbToolsReseller->objOrderTools($order_id, 3);

        $qst_tax = number_format((float) $order->getQSTTax(), 2, '.', '');
        $gst_tax = number_format((float) $order->getGSTTax(), 2, '.', '');
        $price_of_remaining_days = number_format((float) $order->getRemainingDaysPrice(), 2, '.', '');
        $installation_transfer_cost = number_format((float) $order->getSetupPrice(), 2, '.', '');
        $router_cost = number_format((float) $order->getRouterPrice(), 2, '.', '');
        $modem_cost = number_format((float) $order->getModemPrice(), 2, '.', '');
        $adapter_cost = number_format((float) $order->getAdapterPrice(), 2, '.', '');
        $additional_service = number_format((float) $order->getAdditionalServicePrice(), 2, '.', '');
        $total_price = number_format((float) $order->getTotalPrice(), 2, '.', '');
        $sub_total = number_format((float) $order->getTotalPrice() - $qst_tax - $gst_tax, 2, '.', '');
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

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();


        return $output = $dompdf->output();
    }

    function outputInvoice($invoice_id) {
        global $dbToolsReseller, $terms_header, $terms_footer;
        $invoice_info_result = $dbToolsReseller->query(
          "SELECT
            `customers`.`full_name` AS `customer_full_name`,
            CONCAT(`customers`.`address_line_1`, ' ',
                   `customers`.`address_line_2`, ' ',
                   `customers`.`postal_code`, ' ',
                   `customers`.`city`, ' ', `customers`.`address`) AS 'customer_full_address',
            `resellers`.`full_name` AS `reseller_full_name`,
            CONCAT(`resellers`.`address_line_1`, ' ',
                   `resellers`.`address_line_2`, ' ',
                   `resellers`.`postal_code`, ' ',
                   `resellers`.`city`, ' ', `resellers`.`address`) AS 'reseller_full_address',
            `invoices`.`order_id`,
            date(`invoices`.`valid_date_from`) AS `valid_date_from`,
            date(`invoices`.`valid_date_to`) AS `valid_date_to`
            FROM `invoices`
            INNER JOIN `invoice_types` ON `invoices`.`invoice_type_id` = `invoice_types`.`invoic_type_id`
            LEFT JOIN `customers`     ON `invoices`.`customer_id` = `customers`.`customer_id`
            LEFT JOIN `customers` AS `resellers` ON `invoices`.`reseller_id` = `resellers`.`customer_id`
            WHERE `invoices`.`invoice_id`={$invoice_id}");

        $invoice_info= $dbToolsReseller->fetch_assoc($invoice_info_result);
        if((int)$invoice_info["order_id"] > 10380)
        {
          $invoice_info["order_id"]= (((0x0000FFFF & (int)$invoice_info["order_id"]) << 16) + ((0xFFFF0000 & (int)$invoice_info["order_id"]) >> 16));
        }
        $subtotal_items_query="SELECT * FROM
            (SELECT *  FROM `invoice_items` WHERE `invoice_id` = {$invoice_id} AND `invoice_items`.`item_name` NOT LIKE '%tax%') AS `invoice`
            LEFT JOIN
            (SELECT sum(`item_duration_price`) AS `subtotal`
             FROM `invoice_items` WHERE `invoice_id` = {$invoice_id} AND `invoice_items`.`item_name` NOT LIKE '%tax%') AS `subtotal`
             ON 1=1";
        $tax_items_query="SELECT * FROM
            (SELECT *  FROM `invoice_items` WHERE `invoice_id` = {$invoice_id} AND `invoice_items`.`item_name`  LIKE '%tax%') AS `invoice`
            LEFT JOIN
            (SELECT sum(item_duration_price) AS `total`  FROM `invoice_items` WHERE `invoice_id` = {$invoice_id} ) AS `total`
            ON 1=1";
        $invoice_items=[];
        $sub_total_price=0;
        $total_price=0;
        $subtotal_items = $dbToolsReseller->query($subtotal_items_query);
        while($subtotal_item=$dbToolsReseller->fetch_assoc($subtotal_items))
        {
          $sub_total_price=$subtotal_item["subtotal"];
          array_push($invoice_items,$subtotal_item);
        }
        $tax_items = $dbToolsReseller->query($tax_items_query);
        while($tax_item=$dbToolsReseller->fetch_assoc($tax_items))
        {
          $total_price=$tax_item["total"];
          array_push($invoice_items,$tax_item);
        }



        $total_price = number_format((float) $total_price, 2, '.', '');
        $sub_total = number_format((float) $sub_total_price, 2, '.', '');

        $invoice_items_trs="";
        foreach ($invoice_items as $key => $item) {
        // code...
        $item_tr='
        <tr class="415">
        <td class="product">
        <span class="item-name">' . $item["item_name"] . '</span>
        <dl class="meta">																</dl>
        </td>
        <td class="quantity">' . number_format((float) $item["item_price"], 2, '.', '') . '</td>
        <td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . number_format((float) $item["item_duration_price"], 2, '.', '') . '</span></td>
        </tr>';
        $invoice_items_trs.=$item_tr;
        }


        $html = $terms_header . '
                    ' . $invoice_info["customer_full_name"] . '<br/>' . $invoice_info["customer_full_address"] . '
                </td>
        <td class="address shipping-address">
                    <h3>Reseller:</h3>
                    ' . $invoice_info["reseller_full_name"] . '<br/>' . $invoice_info["reseller_full_address"]  . '
        </td>
        <td class="order-data">
        <table>
        <tr class="invoice-date">
          <th>Invoice Date:</th>
          <td>' . date("Y/m/d") . '</td>
        </tr>
        <tr class="order-date">
          <th>Order:</th>
          <td>#' . $invoice_info["order_id"] . '</td>
        </tr>

              </table>
        </td>
        </tr>
        <tr>
        <td></td>
        <td></td>
        <td>Valid Date from (included)</td>
        <td>'.$invoice_info["valid_date_from"].'</td>
        </tr>
        <tr>
        <td></td>
        <td></td>
        <td>Valid Date to (included)</td>
        <td>'.$invoice_info["valid_date_to"].'</td>
        </tr>
        </table>


        <table class="order-details">
        <thead>
            <tr>
                <th class="product">Product</th>
                <th class="quantity">Basic Price</th>
                <th class="price">Total Price</th>
            </tr>
        </thead>
        <tbody>
        '.$invoice_items_trs.'
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


        return $output = $dompdf->output();
    }

}