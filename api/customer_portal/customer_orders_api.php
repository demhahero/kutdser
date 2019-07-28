<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'order_id',
    1 => 'product_title',
    2 => 'creation_date',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `customers`.`customer_id`,
                  `full_name`,
                  `orders`.`order_id`,
                  `creation_date`,
                  `product_title`
            FROM `customers`
            INNER JOIN `orders` ON `customers`.`customer_id`=`orders`.`customer_id`
            WHERE `customers`.`customer_id` = ?";
$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( `product_title` LIKE ? ";
    $where .= " OR `creation_date` LIKE ? ";
    $where .= " OR `orders`.`order_id` LIKE ? ) ";
}


//concatenate search sql if value exist
if (isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}


//Orders
if($params['order'][0]['column']<4)
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'];

//Pagination
$sqlRec .= " LIMIT " . $params['start'] . " ," . $params['length'];

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);
if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
  $stmt->bind_param('ssss',
                    $customer_id,
                    $search_value,
                    $search_value,
                    $search_value );

}
else{
  $stmt->bind_param('s',
                    $customer_id);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);
if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
  $stmt1->bind_param('ssss',
                    $customer_id,
                    $search_value,
                    $search_value,
                    $search_value );
}
else{
  $stmt1->bind_param('s',
                    $customer_id);
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
$customer_full_name="";
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {
  $customer_full_name=$row["full_name"];

  $row["displayed_order_id"]=$row["order_id"];
  if ((int) $row["order_id"] > 10380)
      $row["displayed_order_id"] = (((0x0000FFFF & (int) $row["order_id"]) << 16) + ((0xFFFF0000 & (int) $row["order_id"]) >> 16));

    $data[0] = $row["displayed_order_id"];
    $data[1] = $row["product_title"];
    $data[2] = $row['creation_date'];
    $data[3] = '<a class="btn btn-success" href="'.$site_url.'/requests/make_request.php?order_id='.$row['order_id'].'">Make request</a>';

    $all_data[] = $data;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $all_data,   // total data array
    "customer_full_name"=>$customer_full_name
);

echo $json = json_encode($json_data);
?>
