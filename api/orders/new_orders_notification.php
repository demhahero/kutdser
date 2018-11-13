<?php

//include connection file
include_once "../dbconfig.php";



$sqlTot = "SELECT * from `orders` where `creation_date` >=  DATE_SUB(NOW(), INTERVAL 1 MINUTE) ";

$stmt = $dbTools->getConnection()->prepare($sqlTot);


$stmt->execute();

$result = $stmt->get_result();

$totalRecords = mysqli_num_rows($result);

echo $totalRecords;
?>
