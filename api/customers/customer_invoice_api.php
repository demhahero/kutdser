<?php

//include connection file
include_once "../dbconfig.php";

function getStartDate($row) {


    if ($row["product_category"] == "internet") {

        if ($row["cable_subscriber"] == "yes") {
            $cancellation_date = $row["cancellation_date"];
            $start_date = new DateTime($cancellation_date);
        } else {
            $installation_date_1 = $row["installation_date_1"];
            $start_date = new DateTime($installation_date_1);
        }
    } else if ($row["product_category"] == "phone") {
        $creation_date = $row["creation_date"];
        $start_date = new DateTime($creation_date);

    }

    return $start_date;
}
function getRecurringStartDate($row) {
    $start_date = getStartDate($row);

    $SubscriptionType = $row["product_subscription_type"];

    //Find out 1st recurring date
    if ($SubscriptionType == "yearly") { //If yearly payment
        if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
            $start_date->add(new DateInterval('P1Y'));
        } else { // if not 1st day, add 1 year plus one month
            $start_date->add(new DateInterval('P1Y'));
            $start_date->add(new DateInterval('P1M'));
            $start_date->modify('first day of this month');
        }
    } else { // if payment monthly
        if ($start_date->format('d') == "01") { //if start date = 1st day of month, add one year only
            $start_date->add(new DateInterval('P1M'));
        } else { // if not 1st day, add 2 months
            $start_date->modify('first day of this month'); // Fixed february issue
            $start_date->add(new DateInterval('P2M'));
            $start_date->modify('first day of this month');
        }
    }

    return $start_date;
}

$customer_id=0;
if(isset($_POST["data_id"]))
  $customer_id = intval($_POST["data_id"]);
// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'installation_date_1',

);

$where = $sqlTot = $sqlRec = "";



$sqlTot = "SELECT `customers`.`full_name`,
              `orders`.`product_subscription_type`,
              `orders`.`product_category`,
              `order_options`.`cable_subscriber`,
              `order_options`.`cancellation_date`,
              `order_options`.`installation_date_1`,
              `orders`.`creation_date`
            FROM `orders`
              INNER JOIN `customers` ON `orders`.`customer_id`=`customers`.`customer_id`
              INNER JOIN `order_options` ON `orders`.`order_id`= `order_options`.`order_id`
            WHERE `customers`.`customer_id`=?
                ";
$sqlRec = $sqlTot;




// check search value exist
if (!empty($params['search']['value'])) {
    // $where .= " AND ";
    // $where .= " ( full_name LIKE ? ";
    // $where .= " OR `phone` LIKE ? ";
    // $where .= " OR `email` LIKE ? ";
    // $where .= " OR `customer_id` LIKE ? ) ";
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


$stmt->bind_param('s',
                  $customer_id );

$stmt->execute();

$result = $stmt->get_result();

$queryTot = $result;

$totalRecords = mysqli_num_rows($queryTot);


$stmt1 = $dbTools->getConnection()->prepare($sqlRec);

$stmt1->bind_param('s',
                  $customer_id
                  ); // 's' specifies the variable type => 'string'



$stmt1->execute();

$result1 = $stmt1->get_result();

$queryRecords = $result1;
$all_data=[];
$customer_full_name="";

$rows=[];
while ($row = mysqli_fetch_array($queryRecords)) {
  $rows[] = $row;
}
//iterate on results row and create new index array of data

$start_active_date=null;
if($stmt1->affected_rows>1)
{
  $order1_start_active_date= getRecurringStartDate($rows[0]);
  $order2_start_active_date= getRecurringStartDate($rows[1]);
  $start_active_date= $order1_start_active_date> $order1_start_active_date? $order1_start_active_date:$order2_start_active_date;
}
else if($stmt1->affected_rows==1){
  $order1_start_active_date= getRecurringStartDate($rows[0]);
  $start_active_date= $order1_start_active_date;
}
else{
  $json_data = array(
      "draw" => intval($params['draw']),
      "recordsTotal" => 0,
      "recordsFiltered" => 0,
      "data" => [],   // total data array
      "customer_full_name"=>""
  );

  echo $json = json_encode($json_data);
  exit();
}
$row=$rows[0];
$customer_full_name=$row["full_name"];
if ($row["product_subscription_type"] == "monthly") {
  $start = $start_active_date->modify('first day of this month');
  $end = (new DateTime())->modify('first day of this month');
  $interval = DateInterval::createFromDateString('1 month');
  $period = new DatePeriod($start, $interval, $end);

  foreach ($period as $dt) {
    $data[0] = $dt->format("Y-m");
    $data[1] = '<a target="_blank" href="'.$api_url.'customers/print_customer_recurring_invoice.php?month='. $dt->format("m") .'&year='. $dt->format("Y") .'&customer_id='.$customer_id .'">
        Print
    </a>';
    $all_data[] = $data;
  }
}
else {
    $start = getRecurringStartDate($row)->modify('first day of this month');
    $end = (new DateTime())->modify('first day of this month');
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
      $data[0] = $dt->format("Y-m");
      $data[1] = '<a target="_blank" href="'.$api_url.'customers/print_customer_recurring_invoice.php?month='. $dt->format("m") .'&year='. $dt->format("Y") .'&customer_id='.$customer_id .'">
          Print
      </a>';
      $all_data[] = $data;
    }
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
