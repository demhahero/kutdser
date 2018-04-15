<?php
if ($_GET["do"] == "generateXLS") {
    include_once "dbconfig.php";
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=reseller_customers.xls");
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

    $sql = "select * from `customers` where `reseller_id`='" . $customer_id . "'";
    $result = $connection->query($sql);

    echo "<table><tr><th>Reseller</th></tr><tr><td>$full_name</td></tr></table>";
    ?>

    <table id="myTable" class="display">
        <thead>
        <th>ID</th>
        <th>Full Name</th>
        <th>Product</th>
        <th>Product Without Tax</th>
        <th>Joining Date</th>
        <th>Termination Date</th>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $full_name = $row["full_name"];
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
                }else{
                    $product_name = "";
                }
                ?>
                <tr>
                    <td><?= $row["customer_id"] ?></td>
                    <td><?= $row["full_name"] ?></td>
                    <td><?= $product_name ?></td>
                    <td><?= $product_price ?></td>
                    <td><?= $row["start_date"] ?></td>
                    <td><?= $row["termination_date"] ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
    </table>

    <?php
    die();
}

////////////////////////////////////////////////////////////////////////////////
?>