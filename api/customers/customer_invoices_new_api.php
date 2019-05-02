<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'invoice_id',
    1 => 'type_name',
    2 => 'valid_date_from',
    3 => 'valid_date_to',
    4 => 'order_id',

);

$where = $sqlTot = $sqlRec = "";
$dateNow=new DateTime();
$customer_id=(isset($_POST["customer_id"])?$_POST["customer_id"]:0);

$year=(isset($_POST["year"])?$_POST["year"]:$dateNow->format("Y"));

$month=(isset($_POST["month"])?$_POST["month"]:$dateNow->format("m"));


$sqlTot = "SELECT `invoice_id`,`invoice_types`.`type_name`,date(`valid_date_from`) AS `valid_date_from`,date(`valid_date_to`) AS `valid_date_to`,`order_id` FROM `invoices` INNER JOIN `invoice_types` ON `invoice_types`.`invoic_type_id` = `invoices`.`invoice_type_id`
WHERE `customer_id`=? AND year(`valid_date_from`) = ? AND month(`valid_date_from`) = ?
            ";

$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " AND ";
    $where .= " ( invoice_id LIKE ? ";
    $where .= " OR type_name LIKE ? ";
    $where .= " OR customer_id LIKE ? ) ";
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
$stmt->bind_param('ssssss',
                  $customer_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt->bind_param('sss',
                    $customer_id,
                    $year,
                    $month);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

if (isset($where) && $where != '') {
  $search_value="%".$params['search']['value']."%";
$stmt1->bind_param('ssssss',
                  $customer_id,
                  $year,
                  $month,
                  $search_value,
                  $search_value,
                  $search_value );

}
else{
  $stmt1->bind_param('sss',
                    $customer_id,
                    $year,
                    $month);
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {

    $data[0] = '<a href="invoice_items.php?invoice_id='.$row['invoice_id'].'" >'.$row['invoice_id'].'</a>';
    $data[1] = $row['order_id'];
    $data[2] = $row['type_name'];
    $data[3] = $row['valid_date_from'];
    $data[4] = $row['valid_date_to'];
    $data[5] = '<a href="'.$api_url.'\print\print_invoice.php?invoice_id='.$row['invoice_id'].'" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-file-archive-o"></i> Print </a>';
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
