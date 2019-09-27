<?php

require_once '../../mikrotik/swiftmailer/vendor/autoload.php';

include_once "../dbconfig.php";

$query = "SELECT * from `settings` where `setting_id` = ?";


$stmt1 = $dbTools->getConnection()->prepare($query);

$param_value = "1";
$stmt1->bind_param('s', $param_value
); // 's' specifies the variable type => 'string'


$stmt1->execute();

$result1 = $stmt1->get_result();
$result = $dbTools->fetch_assoc($result1);
if ($result) {
    $json = json_encode($result);
    echo "{\"settings\" :", $json
    , ",\"error\":false}";
} else {
    echo "{\"settings\" :", "{}"
    , ",\"error\":true}";
}
?>
