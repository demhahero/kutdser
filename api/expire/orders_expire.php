<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if(isset($_POST["delete_id"]))
{
	include_once "../dbconfig.php";
	$query = "UPDATE `order_expiration_notify`
						 		SET `seen`='yes'
						WHERE `order_expiration_notify_id`=?";


 	    $stmt1 = $dbTools->getConnection()->prepare($query);

 	    $stmt1->bind_param('s',
 	                      $_POST["delete_id"]
 	                      );


 	    $stmt1->execute();

 	    $modem = $stmt1->get_result();
 	    if ($stmt1->errno==0) {
 	      echo "{\"deleted\" :true}";
 	    }
 	    else{
 	      echo "{\"deleted\" :false}";
 	    }
}
else{
	include_once "../dbconfig.php";


	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array(
	    0 => 'order_expiration_notify_id',
	    1 => 'customer_name',
	    2 => 'reseller_name',
	    3 => 'expiration_date',
	    4 => 'remaining_days',
	    5 => 'functions'

	);

	$where = $sqlTot = $sqlRec = "";



	$sqlTot = "SELECT
							`order_expiration_notify_id`,
							`order_expiration_notify`.`order_id`,
							`order_expiration_notify`.`expiration_date`,
							`orders`.`reseller_id`,
							`orders`.`customer_id`,
							resellers.full_name as 'reseller_name',
							`customers`.`full_name` as 'customer_name',
							`orders`.`product_title`,
							`orders`.`product_category`,
							`orders`.`product_subscription_type`
						FROM `order_expiration_notify`
								inner JOIN `orders` on `order_expiration_notify`.`order_id`= `orders`.`order_id`
								inner JOIN `customers` on `orders`.`customer_id`=`customers`.`customer_id`
								INNER JOIN `customers` resellers on resellers.`customer_id` = `orders`.`reseller_id`
							WHERE seen='no'
							";

	$sqlRec = $sqlTot;




	// check search value exist
	if (!empty($params['search']['value'])) {
	    $where .= " WHERE ";
	    $where .= " ( customers.full_name LIKE ? ";
	    $where .= " OR resellers.full_name LIKE ? ";
	    $where .= " OR order_expiration_notify_id LIKE ? ) ";
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
	$stmt->bind_param('sss',
	                  $search_value,
	                  $search_value,
	                  $search_value );

	}

	$stmt->execute();

	$result = $stmt->get_result();

	$queryTot = $result;

	$totalRecords = mysqli_num_rows($queryTot);


	$stmt1 = $dbTools->getConnection()->prepare($sqlRec);
	if (isset($where) && $where != '') {
	  $search_value="%".$params['search']['value']."%";
	$stmt1->bind_param('sss',
	                  $search_value,
	                  $search_value,
	                  $search_value
	                  ); // 's' specifies the variable type => 'string'
	}


	$stmt1->execute();

	$result1 = $stmt1->get_result();

	$queryRecords = $result1;
	$all_data=[];
	//iterate on results row and create new index array of data
	while ($row = mysqli_fetch_array($queryRecords)) {
		$currentDate=new DateTime();
		$expireDate=new DateTime($row["expiration_date"]);
		$remaining_days=$expireDate->diff($currentDate)->days;
		if($currentDate>$expireDate)
			$remaining_days*=-1;
		$row["remaining_days"]=$remaining_days;

	    $data[0] = $row['order_expiration_notify_id'];
	    $data[1] = $row['customer_name'];
	    $data[2] = $row['reseller_name'];
	    $data[3] = $row['expiration_date'];
	    $data[4] = $row['remaining_days'];
	    $data[5] = '<button class="btn btn-primary noted" data-id='.$row['order_expiration_notify_id'].'>Noted</button>';
	    $all_data[] = $data;
	}

	$json_data = array(
	    "draw" => intval($params['draw']),
	    "recordsTotal" => intval($totalRecords),
	    "recordsFiltered" => intval($totalRecords),
	    "data" => $all_data   // total data array
	);

	echo $json = json_encode($json_data);


}

?>
