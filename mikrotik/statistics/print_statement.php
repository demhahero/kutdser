<?php

include_once "../dbconfig.php";
include "../../terms.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$month = intval(filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT));
$year = intval(filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT));

$dateObj   = DateTime::createFromFormat('!m', $month);
$monthName = $dateObj->format('F'); // March

$reseller_id = intval(filter_input(INPUT_POST, 'reseller_id', FILTER_VALIDATE_INT));
$total_commission_base_amount = floatval(filter_input(INPUT_POST, 'total_commission_base_amount', FILTER_VALIDATE_FLOAT));

$total_commission_base_amount_with_tax=0;
//Calculate texes
$qst_tax = ($total_commission_base_amount) * 0.09975;
$gst_tax = ($total_commission_base_amount) * 0.05;

//Add taxes to total price
$total_commission_base_amount_with_tax = $total_commission_base_amount + $qst_tax + $gst_tax;
$total_commission_base_amount_with_tax=number_format((float) $total_commission_base_amount_with_tax, 2, '.', '');

$reseller = $dbTools->objCustomerTools($reseller_id);

$statement_no=$reseller_id."0".$year."0".$month;

$displayed_statement_no=(((0x0000FFFF & (int) $statement_no) << 16) + ((0xFFFF0000 & (int) $statement_no) >> 16));
$html = $terms_header . '
                    ' . $reseller->getFullName() . '<br/>' . $reseller->getAddress() . '
                </td>
    <td></td>
		<td class="order-data">
			<table>
				<tr class="invoice-date">
					<th>Statement Date:</th>
					<td>' . date("Y/m/d") . '</td>
				</tr>
        <tr class="invoice-date">
					<th>Statement For :</th>
					<td>' . $monthName ." ".$year. '</td>
				</tr>
				<tr class="order-date">
					<th>Statement No:</th>
					<td>#' . $displayed_statement_no . '</td>
				</tr>

			</table>
		</td>
	</tr>
</table>


<table class="order-details">
	<thead>
            <tr>
                <th class="product">Title</th>
                <th class="quantity">Amount</th>
                <th class="price">Total Amount With Tax</th>
            </tr>
	</thead>
	<tbody>
            <tr class="415">
		<td class="product">
                    <span class="item-name">Monthly commission</span>
                    <dl class="meta">																</dl>
		</td>
		<td class="quantity">' . $total_commission_base_amount . '</td>
		<td class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $total_commission_base_amount_with_tax . '</span></td>
            </tr>
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



// Output the generated PDF to Browser
$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));


exit(0);
