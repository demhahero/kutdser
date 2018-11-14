<?php
include "./init.php";

//////////////////////////////////
////////// function to get the range of start recurring_date of the year that the requested month for yearly customers in
/////////////////////////////////
 function getRecurringDateForDate($start_active_date_string,$subscription_type)
  {
    $postDate=new DateTime();
    $start_active_date=new DateTime($start_active_date_string);
    $recurring_date=null;
    $interval_format="P1Y";
    if($subscription_type==="yearly")
    {
      $interval_format="P1Y";
      if(((int)$start_active_date->format('d'))>1)
      {
        $recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
        $interval = new DateInterval('P1M');
        $recurring_date->add($interval);
        $interval = new DateInterval('P1Y');
        $recurring_date->add($interval);
      }
      else{
        $recurring_date=new DateTime($start_active_date_string);
        $interval = new DateInterval('P1Y');
        $recurring_date->add($interval);
      }
    }
    else if ($subscription_type==="monthly")
    {
      $interval_format="P1M";
      if(((int)$start_active_date->format('d'))>1)
      {
        $recurring_date = new DateTime($start_active_date->format('Y')."-".$start_active_date->format('m')."-01 00:00:00");
        $interval = new DateInterval('P2M');
        $recurring_date->add($interval);
      }
      else{
        $recurring_date=new DateTime($start_active_date_string);
        $interval = new DateInterval('P1M');
        $recurring_date->add($interval);
      }
    }
    //echo "</br> Post: ".$postDate->format('Y-m-d');
    //echo "</br> Start: ".$start_active_date->format('Y-m-d');
    //echo "</br> End: ".$recurring_date->format('Y-m-d');
    $count=1;
    $returnedDate=array();
    if($postDate >= $start_active_date && $postDate <$recurring_date)
    {
      $returnedDate["count"]=$count;
      $returnedDate["start_date"]=$start_active_date;
      $returnedDate["end_date"]  =$recurring_date;
      return $returnedDate;
    }
    $startDate=new DateTime($recurring_date->format('Y')."-".$recurring_date->format('m')."-".$recurring_date->format('d'));
    $endDate=new DateTime($recurring_date->format('Y')."-".$recurring_date->format('m')."-".$recurring_date->format('d'));
    $interval = new DateInterval($interval_format);
    $endDate->add($interval);
    while(true){
      //echo "</br> Post: ".$postDate->format('Y-m-d');
      //echo "</br> Start: ".$startDate->format('Y-m-d');
      //echo "</br> End: ".$endDate->format('Y-m-d');
      $count=$count+1;
    if($postDate >= $startDate && $postDate<$endDate)
    {
      $returnedDate["count"]=$count;
      $returnedDate["start_date"]=$startDate;
      $returnedDate["end_date"]  =$endDate;
      return $returnedDate;
    }
    $startDate=new DateTime($endDate->format('Y')."-".$endDate->format('m')."-".$endDate->format('d'));
    $interval = new DateInterval($interval_format);
    $endDate->add($interval);
    }
}//////////////// end function



if(isset($_POST["action"]))
{
  if($_POST["action"]==="get_account_info")
  {
    /// get customer account status
      $query="SELECT * FROM `customer_active_status` WHERE `customer_id`=?";
      $stmt1 = $dbTools->getConnection()->prepare($query);
      $param_value=$customer_id;
      $stmt1->bind_param('s',
                        $param_value
                        ); // 's' specifies the variable type => 'string'

      $stmt1->execute();
      $customer_account_status = $stmt1->get_result();

      $orders=[];
      while($order_row = $dbTools->fetch_assoc($customer_account_status))
      {
        if($order_row["status"]==="active")
        {
          $current_date=new DateTime();
          $startEndRecurringDate=getRecurringDateForDate($order_row["start_active_date"],$order_row["product_subscription_type"]);
          $order_row["start_date"]=$startEndRecurringDate["start_date"]->format('Y-m-d');
          $order_row["current_date"]=$current_date->format('Y-m-d');
          $order_row["end_date"]=$startEndRecurringDate["end_date"]->format('Y-m-d');
          $order_row["recurring_no"]=$startEndRecurringDate["count"];
          $order_row["remaining_days"]=$current_date->diff($startEndRecurringDate["end_date"])->days;
        }
        else{
          $current_date=new DateTime();
          $order_row["start_date"]=$order_row["start_active_date"];
          $order_row["current_date"]=$current_date->format('Y-m-d');
          $order_row["end_date"]=$order_row["action_on_date"];
          $order_row["recurring_no"]=0;
          $order_row["remaining_days"]=0;
        }

        array_push($orders,$order_row);
      }

      $orders_json = json_encode($orders);

      echo "{\"orders\":",$orders_json
        , ",\"message\":\"\""
        , ",\"error\":false}";
  }// end get_shopping_products
}
else {
  echo "{\"orders\":[]"
    , ",\"message\":\" you are not authorized\""
    , ",\"error\":true}";
}

?>
