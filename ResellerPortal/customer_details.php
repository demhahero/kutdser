<?php
include_once "header.php";
?>

<?php
$customer_id = intval(filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT));

$sql = "select * from `customers` where `customer_id`='" . $customer_id . "'";
$result = $dbToolsReseller->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $full_name = $row["full_name"];
    $address = $row["address"];
    $product_id = $row["product_id"];
    $order_id = $row["order_id"];
    $customer_phone = $row["phone"];
    $customer_email = $row["email"];
    $start_date = strtotime($row["start_date"]);
    $actual_installation_date = $row["actual_installation_date"];
    $actual_installation_time_from = $row["actual_installation_time_from"];
    $actual_installation_time_to = $row["actual_installation_time_to"];
    $completion = $row["completion"];

}

$servername = "localhost";
$username = "i3702914_wp1";
$password = "D@fH(9@QUrGOC7Ki5&*61]&0";
$dbname = "i3702914_wp1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT
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

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $product_name = $row["post_title"];
    $product_price = $row["VALUE"];
    $gst = round($product_price * 0.05, 2);
    $qst = round($product_price * 0.09975, 2);
    $total = round($product_price + $gst + $qst, 2);
}

?>
<title><?= $full_name ?> - Details</title>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
        <th>Name</th>
        <th>Value</th>
    </thead>
    <tbody>
        <tr>
            <td style="width: 30%;">Full Name:</td>
            <td><?= $full_name ?></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td><?= $address ?></td>
        </tr>
        <tr>
            <td>Order ID:</td>
            <td><?= $order_id ?></td>
        </tr>
        <tr>
            <td>Product Name:</td>
            <td><?= $product_name ?></td>
        </tr>
        <tr>
            <td>Phone:</td>
            <td><?= $customer_phone ?></td>
        </tr>
        <tr>
            <td>Completion:</td>
            <td><?= $completion ?></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><?= $customer_email ?></td>
        </tr>
        <tr>
            <td>Actual Installation Date:</td>
            <td><?= $actual_installation_date ?></td>
        </tr>
        <tr>
            <td>Actual Installation Time:</td>
            <td><?= $actual_installation_time_from ?> to <?= $actual_installation_time_to ?></td>
        </tr>
        <tr>
            <td>Note</td>
            <td><a href="send_note.php?customer_id=<?= $customer_id ?>" class="btn btn-primary">Send Note</a></td>
        </tr>

    </tbody>
</table>

<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Type</th>
    <th>Note</th>
    <th>Due Date</th>
    <th>Completion</th>
</thead>
<tbody>
    <?php
    $query = $dbToolsReseller->query("select * from `customer_log` where `customer_id`='" . $customer_id . "'");
    while ($row = mysql_fetch_array($query)) {
        ?>
        <tr>
            <td style="width: 5%;">
                <?php echo $row["customer_log_id"]; ?>
            </td>
            <td style="width: 15%;">
                <?php echo $row["type"]; ?>
            </td>
            <td style="width: 30%;">
                <?php echo $row["note"]; ?>
            </td>
            <td style="width: 9%;">
                <?php echo $row["log_date"]; ?>
            </td>
            <td style="width: 9%;">
                <?php if($row["completion"] == "0") echo "Incomplete"; else echo "Complete"; ?>
            </td>
        </tr>
    <?php
}
?>
</tbody>
</table>

<script>
$(document).ready(function() {
    $('#myTable').DataTable().destroy();
    $('#myTable').dataTable( {
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bSort": false,
        "bInfo": false,
        "bAutoWidth": false
    } );
} );
</script>
<?php
include_once "footer.php";
?>
