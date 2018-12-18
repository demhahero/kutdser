<?php
$terms_header_statement = '<!DOCTYPE html>
<html>

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


<body class="invoice">

<table class="head container">
	<tr>
		<td class="header">
		<img src="'.__DIR__ .'/logo-1.png" width="516" height="300" alt="AM Pro Telecom Inc." />		</td>
		<td class="shop-info">
			<div class="shop-name"><h3>AM Pro Telecom Inc.</h3></div>
			<div class="shop-address"><p>1600 Boul Henri-Bourassa<br />
H3M 3E3, Suite 1590<br />
Montréal, Canada-Qc</p>
</div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
Commission Statement</h1>


<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
                    <!-- <h3>Billing Address:</h3> -->';

$terms_header = '<!DOCTYPE html>
<html>

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
font.initial{
    font-size: 10pt;
    position: absolute;
    bottom: 0cm;
    right: 0cm;
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


<body class="invoice">

<table class="head container">
	<tr>
		<td class="header">
		<img src="'.__DIR__ .'/logo-1.png" width="516" height="300" alt="AM Pro Telecom Inc." />		</td>
		<td class="shop-info">
			<div class="shop-name"><h3>AM Pro Telecom Inc.</h3></div>
			<div class="shop-address"><p>1600 Boul Henri-Bourassa<br />
H3M 3E3, Suite 1590<br />
Montréal, Canada-Qc</p>
</div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
Invoice</h1>


<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
                    <!-- <h3>Billing Address:</h3> -->';

$terms_footer = '
<font class="initial">initial:___ ___</font>
<font class="tax-number">GST: 745440297 | QST:  1224265740 | partner with: <img src="'.__DIR__ .'/bidsettle.png" width="120" height="20" /></font>
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
-If you subscribe to one year contract or one year prepaid Internet Service, in case of early cancellation the installation fee that was concede at the registration will be charged back, or a fee of $83+tax  will be charged.
<br/><br/>
-AM Pro Telecom have all the right to apply your deposit without  notice,in case of any unpaid balances ,and the remaining balance will be sent to you within ten days after return of all the leased equipment in good working condition with complete parts, failure to do so within ten business days after the service stop, you will be charged for the full cost of any equipment that not returned toAM Pro Telecom.
<br/><br/><br/>

<h2>Moving your Service to another place:</h2><br/>
There is a fee of $90 will be applied to all our services.
<br/><br/><br/>

<h2>Limited  Liability:</h2><br/>
AM Pro Telecom will make every effort to provide hight quality service. however, if the services are interrupted or have lower performed than what expected for more than 72 consecutive hours, AM Pro Telecom&rsquo;s liability will be limited to crediting you the service fee only for the period in question.
<br/><br/>

<font class="initial">initial:___ ___</font>
</div>

<div id="footer">
	<p>Website: www.amprotelecom.com | Phone: +1-514-548-2555 | Email: Info@amprotelecom.com</p>
</div><!-- #letter-footer -->


<div class="break"></div>



<center><h1>AM PRO TELECOM Termes et conditions :</h1></center>

Les termes et conditions suivante vont être appliques pour tous les services fournis par AM PRO TELECOM.
<br/><br/><br/>
<div class="terms">

<h2>Charge pour le service:</h2><br/>
Les services qu’on offre sont des services prépayée. Votre premier paiement incluras  des charges uniques, par la suite il seras question de paiement mensuelle équivalent au plan que vous avez choisis.
Vous serez responsable de payée votre service a temps avant la date d’échéance. Une balance qui reste impayée après la date d‘échéance seras soumis a un taux d’intérêt de 2% par mois. Dans le cas ou le paiement ne seras pas fait, AM PRO Telecom a le droit de suspendre et/ou terminer votre service a n’importe quel moment et sans aucun préavis. Pour réactiver ou réinstaller votre service de nouveau vous pouvez être charger des frais de réactivation ou d’installation de service.
<br/><br/><br/>

<h2>Installation de Service :</h2><br/>
Vous pouvez demander au technicien d’installer votre service n’importe ou dans votre maison si il existe une prise fonctionnelle, dans le cas ou la prise est inexistante ou non fonctionnelle, le technicien installeras le service sur une des prise fonctionnelle, le technicien se réserve le droit de ne pas installer vos équipements, dans ce cas vous serais responsable de le faire.
<br/><br/><br/>

<h2>Résiliation:</h2><br/>
-si vous vous abonnez pour un contrat d’un ans ou un ans de service internet prépayer, et vous résilier avant de complété la durée les frais d’installations qui non pas été payée au début seront charger ou une frais de résiliation de service qui s’élève a 83$+ taxes.
-AM PRO Telecom se réserve tout les droits de se procurer votre dépôt sans préavis, dans le cas de n’importe quelle balance impayée,et la balance restante vous seras envoyer dans un délais de 10 jours ouvrables après la réception de tous les équipement louer dans un état fonctionnelle avec tous les accessoires, si les accessoire ne sont pas rendu après 10 jours ouvrables de la date de résiliation, vous serais charger le montant complet de n’importe quel équipement qui n’est pas rendu a AM PRO Telecom.
<br/><br/><br/>

<h2>Responsabilité limite:</h2><br/>
AM PRO Telecom vas fournir tous les effort de donnée un service de Haut qualité. Cependant si les services sont interrompus ou ont une performance inférieure à celle attendue pour plus de 72 heurs consécutif, AM PRO Telecom responsabilité seras limite de vous créditer juste la période en question.
<br/><br/>

<font class="initial">signature:________________________________</font>
</div>
<div id="footer">
	<p>Site web: www.amprotelecom.com | Téléphone: +1-514-548-2555 | Email: Info@amprotelecom.com</p>
</div><!-- #letter-footer -->

</body>
</html>';
