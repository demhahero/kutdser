<?php
include_once "../dbconfig.php";

if(isset($_POST['invoice_item_id'])){
    $query = "UPDATE `invoice_items` SET
                 `item_duration_price`=?,
                 `update_date`=CURTIME(),
                 `admin_id`=?
               WHERE `invoice_item_id`=?";

    $stmt1 = $dbTools->getConnection()->prepare($query);

    $stmt1->bind_param('sss', $_POST['item_duration_price'], $admin_id, $_POST['invoice_item_id']);

    $stmt1->execute();
    if ($stmt1->errno == 0) {
        echo "{\"updated\" :true,\"error\" :null}";
    } else {
        echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";
    }
} else{
    echo "{\"updated\" :\"no\",\"error\" :\"updated failed please refresh the page\"}";
}