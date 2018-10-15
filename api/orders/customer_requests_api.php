<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once "../dbconfig.php";


$query = "SELECT requests.request_id,requests.product_price,requests.product_title,requests.verdict,requests.note,orders.order_id,customers.customer_id,customers.full_name,requests.action,requests.action_value,requests.action_on_date,requests.creation_date,requests.modem_id,resellers.full_name as 'reseller_name',resellers.customer_id as 'reseller_id',admins.admin_id,admins.username FROM requests
          INNER JOIN `customers` resellers on resellers.`customer_id` = requests.reseller_id
          INNER JOIN orders on orders.order_id = requests.order_id
          INNER JOIN customers on customers.customer_id=orders.customer_id
          left JOIN admins on requests.admin_id = admins.admin_id
          Where orders.`order_id` = ? ORDER BY requests.request_id ";
$stmt1 = $dbTools->getConnection()->prepare($query);

$param_value=$_GET['order_id'];
$stmt1->bind_param('s',
                  $param_value
                  ); // 's' specifies the variable type => 'string'


$stmt1->execute();

$result1 = $stmt1->get_result();

$requests=[];
while($result = $dbTools->fetch_assoc($result1))
{
  array_push($requests,$result);
}

$json = json_encode($requests);
echo "{\"requests\" :", $json, "}";
