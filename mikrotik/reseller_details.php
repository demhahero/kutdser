<?php
include_once "header.php";
?>


<?php
$servername = "localhost";
$username = "i3702914_wp1";
$password = "D@fH(9@QUrGOC7Ki5&*61]&0";
$dbname = "i3702914_wp1";

// Create connection
$conn_wordpress = new mysqli($servername, $username, $password, $dbname);


$sql = "select * from `customers` where `customer_id`='" . $_GET["customer_id"] . "'";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $full_name = $row["full_name"];
    $address = $row["address"];
    $start_date = strtotime($row["start_date"]);
}

$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));
$month = intval(filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT));
$year = intval(filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT));

$date = new DateTime($month."/"."01"."/". $year);

$sql = "select * from `customers` where `reseller_id`='" . $customer_id . "'";
$result = $connection->query($sql);
?>
<title><?= $full_name ?>'s Customers</title>

<div class="page-header">
    <h4>Reseller: <?= $full_name ?></h4>    
</div>
<form class="register-form form-inline" method="get">
    <input name="customer_id" style="display:none;" value="<?= $customer_id ?>"/>
    <div class="form-group">
        <label for="email">Month:</label>
        <select  name="month" class="form-control">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                if ($month == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
        <label for="email">Year:</label>
        <select  name="year" class="form-control">
            <?php
            for ($i = 2017; $i <= 2020; $i++) {
                if ($year == $i)
                    echo "<option selected value=\"$i\">$i</option>";
                else
                    echo "<option value=\"$i\">$i</option>";
            }
            ?>

        </select>
    </div>
    <input type="submit" class="btn btn-default" value="Search">
    <a style="float:right;" href="reseller_details_generateXLS.php?customer_id=<?= $customer_id ?>&month=<?= $month ?>&year=<?= $year ?>&do=generateXLS" class="btn btn-primary">Generate Excel</a>
</form>

<br/>

<table id="myTable" class="display  table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Product</th>
    <th>Price</th>
    <th>Total (No Tax)</th>
    <th>Total</th>
    <th>Joining Date</th>
    <th>Join Type</th>
    <th>Invoice</th>
</thead>
<tbody>
    <?php
    $totalResellerCustomerAmount = 0;
    $totalResellerCustomerAmountWithoutTaxes = 0;
    $numberOfCustomers = 0;
    $totalPrices = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $date_customer_format = new DateTime($row["start_date"]);
            $date_customer_compatitor = new DateTime($row["start_date"]);
            $date_customer_compatitor->modify('first day of this month');
            
            $date_customer_format_next_month = new DateTime($row["start_date"]);
            $date_customer_format_next_month->modify('first day of next month');
            
            $terminatio_date = null;
            if($row["termination_date"] != "")
                $terminatio_date = new DateTime($row["termination_date"]);
            
            if ($date_customer_compatitor > $date)
                continue;

            $full_name = $row["full_name"];
            $address = $row["address"];
            $product_id = $row["product_id"];

            $sql_customer = "SELECT
  wpp.ID,
  wpp.post_title,
  wppm.meta_key AS FIELD,
  wppm.meta_value AS VALUE,
  wppm.*
FROM wp_posts AS wpp
  LEFT JOIN wp_postmeta AS wppm
    ON wpp.ID = wppm.post_id
WHERE wpp.post_type = 'product'
      AND (wppm.meta_key = '_regular_price')
      AND wpp.id='" . $product_id . "'
ORDER BY wpp.ID ASC, FIELD ASC, wppm.meta_id DESC;";

            $result_customer = $conn_wordpress->query($sql_customer);

            if ($result_customer->num_rows > 0) {
                $row_customer = $result_customer->fetch_assoc();

                $product_name = $row_customer["post_title"];
                $product_price = $row_customer["VALUE"];

                $total = 0;
                
                if($terminatio_date != null && $terminatio_date >= $date_customer_compatitor){
                    continue;
                }
                
                //Yearly product
                if (strpos(strtolower($product_name), "year") != FALSE || strpos(strtolower($product_name), "yearly") != FALSE) {
                    if ($date_customer_compatitor != $date)
                        continue;
                    if ($date_customer_format->format('d') != "01") {
                        $product_price_monthly = $product_price / 12;
                        $day_cost = $product_price_monthly / cal_days_in_month(CAL_GREGORIAN, $date_customer_format->format('m'), $date_customer_format->format('Y'));
                        $month_rest_days_cost = $day_cost * (cal_days_in_month(CAL_GREGORIAN, $date_customer_format->format('m'), $date_customer_format->format('Y')) - $date_customer_format->format('d') + 1);
                        $total = $month_rest_days_cost + $product_price;
                    } else {
                        $total = $product_price;
                    }
                } 
                //Monthly Product
                else {
                    if ($date_customer_format->format('d') != "01") {
                        if ($date_customer_compatitor == $date) {
                            $product_price_monthly = $product_price;
                            $day_cost = $product_price_monthly / cal_days_in_month(CAL_GREGORIAN, $date_customer_format->format('m'), $date_customer_format->format('Y'));
                            $month_rest_days_cost = $day_cost * (cal_days_in_month(CAL_GREGORIAN, $date_customer_format->format('m'), $date_customer_format->format('Y')) - $date_customer_format->format('d') + 1);
                            $total = $month_rest_days_cost + $product_price;
                        } else if ($date_customer_format_next_month == $date) {
                            continue;
                        } else {
                            $total = $product_price;
                        }
                    } else {
                        if ($date_customer_format == $date)
                            $total = $product_price;
                        else
                            $total = $product_price;
                    }
                }

                $gst = round($total * 0.05, 2);
                $qst = round($total * 0.09975, 2);
                $total_without_tax = round($total, 2);
                $total = round($total + $gst + $qst, 2);
                ?>
                <tr>
                    <td><?= $row["customer_id"] ?></td>
                    <td><?= $row["full_name"] ?></td>
                    <td><?= $product_name ?></td>
                    <td><?= $product_price ?>$</td>
                    <td><?= $total_without_tax ?>$</td>
                    <td><?= $total ?>$</td>
                    <td><?= $date_customer_format->format('Y-m-d') ?></td>
                    <td><?= $row["join_type"]?></td>
                    <td><a href="customer_invoices.php?customer_id=<?= $row["customer_id"]; ?>">Invoices</a></td>
                </tr>

                <?php
                $totalResellerCustomerAmount += $total;
                $totalResellerCustomerAmountWithoutTaxes += $total_without_tax;
                $totalPrices += $product_price;
                $numberOfCustomers++;
            } else
                echo $row["customer_id"];
            
        }
    }
    ?>
</tbody>
</table>
<br/>
<br/>
<ul class="list-group">
    <li class="list-group-item">Number of customers: <span class="badge"><?= $numberOfCustomers ?></span></li>

    <li class="list-group-item">Total: <span class="badge"><?= $totalResellerCustomerAmount ?>$</span></li>
    <li class="list-group-item">Total without Taxes: <span class="badge"><?= $totalResellerCustomerAmountWithoutTaxes ?>$</span></li>
    <li class="list-group-item">Total Prices: <span class="badge"><?= $totalPrices ?>$</span></li>
</ul> 

<script>
    $(document).ready(function () {
        $('#myTable').DataTable().destroy();
        $('#myTable').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, 500, 1000, -1], [10, 25, 50, 100, 500, 1000, "All"]],
            responsive:true
        });
    });
</script>
<?php
include_once "footer.php";
?>