
Editing:  
/home/alialsaffar/public_html/mikrotik/index.php
 Encoding:       

<?php
include_once "header.php";
?>

<?php
if (isset($_GET["router_id"])) {
    $result = mysql_query("DELETE FROM `users` WHERE `router_id` = '" . $_GET["router_id"] . "'");
    if ($result)
        echo "<div class='alert alert-success'>done</div>";
}
?>
<title>Routers</title>
<div class="page-header">
    <h4>Routers</h4>    
</div>

<a href="create_router.php" class="btn btn-primary">Generate</a><br><br>
<table id="myTable" class="display  table table-striped table-bordered">
    <thead>
    <th>ID</th>
    <th>Username</th>
    <th>Password</th>
    <th>MAC</th>
    <th>Functions</th>
</thead>
<tbody>
    <?php
    $query = mysql_query("select * from `users`");
    while ($row = mysql_fetch_array($query)) {
        ?>
        <tr>
            <td style="width: 8%;"><?php echo "router_" . $row["router_id"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo $row["password"]; ?></td>
            <td style="width: 19%;"><?php echo $row["mac"]; ?></td>
            <td style="width: 10%;">
                <span class="functions">
                <a href="edit_router.php?router_id=<?php echo $row["router_id"]; ?>"><img title="Edit" width="30px" src="img/edit-icon.png" /></a>
                <a class="check-alert" href="index.php?do=delete&router_id=<?php echo $row["router_id"]; ?>"><img title="Remove" width="30px" src="img/delete-icon.png" /></a>
                </span>
            </td>

        </tr>
        <?php
    }
    ?>	
</tbody>
</table>

<?php
include_once "footer.php";
?>
