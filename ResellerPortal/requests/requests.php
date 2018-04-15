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
    $requests = $dbTools->request_query("select * from `requests` where `reseller_id`='" . $reseller_id . "'", 2);
    foreach ($requests as $request) {
        ?>
        <tr>
            <td style="width: 5%;"><?= $request->getRequestID(); ?></td>
            <td style="width: 20%;"><?= $request->getOrder()->getOrderID(); ?></td>
            <td style="width: 20%;"><?= $request->getOrder()->getCustomer()->getFullName(); ?></td>
            <td style="width: 10%;"><?= $request->getAction(); ?></td>
            <td style="width: 25%;"><?php if($request->getCreationDate() != null) echo $request->getCreationDate()->format("Y-m-d"); ?></td>
            <td style="width: 10%;"><?= $request->getVerdict(); ?></td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>

<?php
include_once "../footer.php";
?>