<?php
include_once "../header.php";
?>

<?php
if (isset($_GET["router_id"])) {

    $routerTools = $dbTools->objRouterTools(intval($_GET["router_id"]));

    $result = $routerTools->doDelete();

    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>

<title>Routers</title>
<div class="page-header">
    <h4>Routers</h4>    
</div>
<a href="create_router.php" class="btn btn-primary">+ Create</a> 

<br><br>
<table id="myTable"  class="display table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Serial Number</th>
    <th>Reseller</th>
    <th>Customer</th>
    <th>Functions</th>
</thead>
<tbody>
    <?php
    $routers = $dbTools->router_query("select * from `routers`");
    foreach ($routers as $router) {
        ?>
        <tr>
            <td style="width: 5%;"><?= $router->getRouterID() ?></td>
            <td style="width: 25%;"><?= $router->getSerialNumber() ?></td>
            <td style="width: 30%;">
                <?php
                if ($router->getReseller() != null)
                    echo $router->getReseller()->getFullName();
                ?>
            </td>
            <td style="width: 30%;">
                <?php
                if ($router->getCustomer() != null) {
                    echo "<a href=\"$site_url/edit_customer.php?customer_id=" . $router->getCustomer()->getCustomerID() . "\">" . $router->getCustomer()->getFullName() . "</a>";
                }
                ?>
            </td>
            <td class="functions" style="width: 12%;">
                <span class="functions">
                    <a href="edit_router.php?router_id=<?= $router->getRouterID() ?>"><img title="Edit" width="30px" src="<?= $site_url ?>/img/edit-icon.png" /></a>
                    <a class="check-alert" href="routers.php?do=delete&router_id=<?= $router->getRouterID() ?>"><img title="Remove" width="30px" src="<?= $site_url ?>/img/delete-icon.png" /></a>
                </span>
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