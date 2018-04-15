<?php
include_once "../header.php";
?>

<?php
if (isset($_GET["upcoming_customer_id"])) {
    $result = $dbTools->objUpcomingCustomerTools($_GET["upcoming_customer_id"])->doDelete();
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>


<title>Upcoming Customers</title>
<div class="page-header">
    <h4>Upcoming Customers</h4>    
</div>
<a href="create_upcoming_customer.php" class="btn btn-primary">+ Create</a> 
<br/><br/>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Creation Date</th>
    <th>Admin</th>
    <th>Functions</th>
</thead>
<tbody>
    <?php
    $upcoming_customers = $dbTools->upcoming_customer_query("select * from `upcoming_customers`", 3);
    foreach ($upcoming_customers as $upcoming_customer) {
        ?>
        <tr>
            <td style="width: 5%;"><?= $upcoming_customer->getUpcomingCustomerID() ?></td>
            <td style="width: 25%;"><?= $upcoming_customer->getFullName(); ?></td>
            <td style="width: 20%;"><?= $upcoming_customer->getPhone(); ?></td>
            <td style="width: 20%;"><?= $upcoming_customer->getCreationDate() ?></td>
            <td style="width: 20%;"><?= $upcoming_customer->getAdmin()->getUsername(); ?></td>
            <td style="width: 10%;">
                <a href="edit_upcoming_customer.php?upcoming_customer_id=<?= $upcoming_customer->getUpcomingCustomerID() ?>">
                    <img title="Edit" width="30px" src="<?= $site_url ?>/img/edit-icon.png" />
                </a>
                <a class="check-alert" href="upcoming_customers.php?do=delete&upcoming_customer_id=<?=$upcoming_customer->getUpcomingCustomerID()?>">
                    <img title="Remove" width="30px" src="<?= $site_url ?>/img/delete-icon.png" />
                </a>
            </td>
        </tr>
        <?php
    }
    ?>	
</tbody>
</table>

<?php
include_once "../footer.php";
?>