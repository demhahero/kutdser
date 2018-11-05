<?php
include_once "../header.php";
?>

<title>Requests</title>
<div class="page-header">
    <h4>Requests</h4>
</div>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Order</th>
    <th>Customer</th>
    <th>Action</th>
    <th>Date</th>
    <th>Verdict</th>
</thead>
<tbody>
    <?php
    $query="SELECT
                `requests`.`request_id`,
                `requests`.`verdict`,
                `requests`.`note`,
                `orders`.`order_id`,
                `customers`.`customer_id`,
                `customers`.`full_name`,
                `requests`.`action`,
                `requests`.`action_value`,
                `requests`.`action_on_date`,
                `requests`.`creation_date`,
                `requests`.`modem_id`
              FROM `requests`
                INNER JOIN `orders` on `orders`.`order_id` = `requests`.`order_id`
                INNER JOIN `customers` on `customers`.`customer_id`=`orders`.`customer_id`
              WHERE `requests`.`reseller_id`='" . $reseller_id . "'";
              
    $requests = $dbToolsReseller->query($query);
    while ($row = mysqli_fetch_array($requests)) {
      $action_on_date="";

      if($row["action"]==="change_speed" && is_numeric($row["modem_id"])  && (int)$row["modem_id"] >0)
      {
        $row["action"]="swap modem and change speed";
      }
        ?>
        <tr>
            <td style="width: 5%;"><?= $row["request_id"] ?></td>
            <td style="width: 20%;"><?= $row["order_id"] ?></td>
            <td style="width: 20%;"><?= $row["full_name"] ?></td>
            <td style="width: 10%;"><?= $row["action"] ?></td>
            <td style="width: 25%;"><?= $row["creation_date"] ?></td>
            <td style="width: 10%;"><?= $row["verdict"] ?></td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>

<?php
include_once "../footer.php";
?>
