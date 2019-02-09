<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'invoice_item_id',
    1 => 'item_name',
    2 => 'item_price',
    3 => 'item_type',
    4 => 'item_duration_price',

);

$where = $sqlTot = $sqlRec = "";

$invoice_id=(isset($_POST["invoice_id"])?$_POST["invoice_id"]:0);

$sqlTot = "SELECT
          `invoice_item_id`
          ,`item_name`
          ,`item_price`
          ,`item_type`
          ,`item_duration_price`
          FROM `invoice_items`
          WHERE `invoice_id`=?";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( item_name LIKE ? ";
    $where .= " OR invoice_item_id LIKE ? ";
    $where .= " OR item_type LIKE ? ) ";
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
                  $invoice_id,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt->bind_param('s',
                    $invoice_id);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt1->bind_param('ssss',
                  $invoice_id,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt1->bind_param('s',
                    $invoice_id);
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = $row['invoice_item_id'];
    $data[1] = $row['item_name'];
    $data[2] = round((double)$row['item_price'], 2);
    $data[3] = $row['item_type'];
    $data[4] = round((double)$row['item_duration_price'], 2);
    $all_data[] = $data;
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $all_data   // total data array
);

echo $json = json_encode($json_data);
?>
