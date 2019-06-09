<?php

//include connection file
include_once "../dbconfig.php";

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => '`invoices`.`invoice_id`',
    1 => '`invoices`.`valid_date_from`',
    2 => '`invoices`.`valid_date_to`',
    3 => '`invoice_types`.`type_name`',
    4 => 'items'
);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT invoices.*, `invoice_types`.`type_name` from `invoices` "
        . "INNER JOIN `invoice_types` on `invoice_types`.`invoic_type_id` = `invoices`.`invoice_type_id` "
        . "WHERE `invoices`.`order_id`='" . $_GET["order_id"] . "'";


$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {

}

//concatenate search sql if value exist
if (isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}

//Orders
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'];

//Pagination
$sqlRec .= " LIMIT " . $params['start'] . " , " . $params['length'];

mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");

$stmt = $dbTools->getConnection()->prepare($sqlTot);
if (isset($where) && $where != '') {
    $search_value = "%" . $params['search']['value'] . "%";
    $stmt->bind_param('ssssss', $search_value, $search_value, $search_value, $search_value, $search_value, $search_value);
}

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);
if (isset($where) && $where != '') {
    $search_value = "%" . $params['search']['value'] . "%";
    $stmt1->bind_param('ssssss', $search_value, $search_value, $search_value, $search_value, $search_value, $search_value
    ); // 's' specifies the variable type => 'string'
}


$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data = [];
//iterate on results row and create new index array of data
while ($row = mysqli_fetch_array($queryRecords)) {
    $from = explode(" ", $row['valid_date_from']);
    $to = explode(" ", $row['valid_date_to']);
    $data[0] = $row['invoice_id'];
    $data[1] = $from[0];
    $data[2] = $to[0];
    $data[3] = $row['type_name'];
    
    $invoice_items_stmt = $dbTools->getConnection()->prepare("select * from `invoice_items` where `invoice_id` = ?");
    $invoice_items_stmt->bind_param('s',$row['invoice_id']);
    $invoice_items_stmt->execute();
    $invoice_items_result = $invoice_items_stmt->get_result();
    $data[4] = "<table class=\"table table-dark\" style='background-color:#615050; color:white;'  >"
            . "<thead class=\"clickable\" data-toggle=\"collapse\" data-target=\"#accordion-".$row['invoice_id']."\">"
            . "<tr>"
            . "<th>Name</th><th>Price</th><th>Duration</th><th>Type</th>"
            . "</tr>"
            . "</thead>"
            . "<tbody class='collapse' id='accordion-".$row['invoice_id']."'>";
    while ($invoice_item_row = mysqli_fetch_array($invoice_items_result)) {
        $data[4] = $data[4]."<tr>";
        $data[4] = $data[4]."<td>".$invoice_item_row['item_name']."</td>";
        $data[4] = $data[4]."<td>".$invoice_item_row['item_price']."$</td>";
        $data[4] = $data[4]."<td>".$invoice_item_row['item_duration_price']."$</td>";       
        $data[4] = $data[4]."<td>".$invoice_item_row['item_type']."</td>";        
        $data[4] = $data[4]."</tr>";
    }
    $data[4] = $data[4]."</tbody></table>";
    
    
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
