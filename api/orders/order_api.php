<?php

include_once "../dbconfig.php";

$order_id = intval($_GET['order_id']);

$query = "SELECT `merchantrefs`.`merchantref`, `merchantrefs`.`order_id`,
    `merchantrefs`.`is_credit`, `orders`.order_id,`orders`.creation_date,`orders`.status,`orders`.reseller_id,
    `orders`.customer_id,orders.product_title,orders.product_category,orders.product_subscription_type,
    resellers.full_name as 'reseller_name',`customers`.`full_name` as 'customer_name', `order_options`.`modem_mac_address`,
    `order_options`.`cable_subscriber`,`customers`.`address`,`customers`.`phone`,`customers`.`note` ,`customers`.`email`,
    `order_options`.`completion`,`customers`.`city`,`customers`.`address_line_1`,`customers`.`address_line_2`,
    `customers`.`postal_code`, `order_options`.`installation_date_1`, `order_options`.`installation_date_2`,
    `order_options`.`installation_time_1`,`order_options`.`installation_time_2`,`order_options`.`installation_time_3`,
    `order_options`.`installation_date_3`, `order_options`.`cancellation_date`, `order_options`.`plan`,
    `order_options`.`modem`, `order_options`.`router`, `order_options`.`current_cable_provider`,
    `order_options`.`actual_installation_time_from`, `order_options`.`actual_installation_time_to`,
    `order_options`.`actual_installation_date`, `order_options`.`current_phone_number`, `order_options`.`adapter`
    , `order_options`.`additional_service`
FROM `orders`
left JOIN `order_options` on `order_options`.`order_id`= `orders`.`order_id`
left JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
left JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
left JOIN `merchantrefs` on `merchantrefs`.`customer_id` = `orders`.`customer_id` and type!='payment'
where `orders`.`order_id`=?";

        $stmt1 = $dbTools->getConnection()->prepare($query);


        $stmt1->bind_param('s',
                          $order_id
                          ); // 's' specifies the variable type => 'string'


        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $result = $dbTools->fetch_assoc($result1);
        $result["displayed_order_id"]=$result["order_id"];
        if ((int) $result["order_id"] > 10380)
            $result["displayed_order_id"] = (((0x0000FFFF & (int) $result["order_id"]) << 16) + ((0xFFFF0000 & (int) $result["order_id"]) >> 16));

$json = json_encode($result);
echo $json;
?>
