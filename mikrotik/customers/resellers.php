<?php
include_once "../header.php";
?>

<title>Resellers</title>
<div class="page-header">
    <a class="last" href="">Resellers</a>
</div>
<table id="myTable" class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Full Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Customers</th>
    <th>Resellers Statistics</th>
    <th>Statistics</th>
    <th>Edit discount</th>
</thead>
<tbody>
    <?php
    $query="select * from `customers` where `is_reseller` = '1'";
    $queryRecords = mysqli_query($conn_routers, $query);
    //iterate on results row and create new index array of data
    while ($customer = mysqli_fetch_array($queryRecords)) {


        ?>
        <tr>
            <td style="width: 5%;"><?=$customer['customer_id']?></td>
            <td style="width: 40%;"><a href="<?=$site_url?>/edit_customer.php?customer_id=<?=$customer['customer_id']?>"><?=$customer['full_name']?></a></td>
            <td style="width: 15%;"><?=$customer['phone']?></td>
            <td style="width: 25%;"><?=$customer['email']?></td>
            <td style="width: 5%;">
                <a href="reseller_customers.php?reseller_id=<?=$customer['customer_id']?>">Customers</a>
            </td>
            <td style="width: 5%;">
                <a href="<?=$site_url?>/statistics/reseller_child_customers_monthly.php?reseller_id=<?=$customer['customer_id']?>">Monthly</a>
            </td>
            <td style="width: 5%;">
                <a href="<?=$site_url?>/statistics/reseller_customers_monthly_new.php?reseller_id=<?=$customer['customer_id']?>">Monthly</a>
            </td>
            <td style="width: 5%;">
              <a target='_blank' href="<?= $site_url ?>/customers/edit_discount.php?reseller_id=<?=$customer['customer_id']?>"><img src='<?= $site_url ?>/img/edit-icon.png' style='width: 25px;' /></a>
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
