<?php

require_once '../../mikrotik/swiftmailer/vendor/autoload.php';

if (isset($_POST["action"])) {
    if ($_POST["action"] === "edit_resellerportal_notification") {
        include_once "../dbconfig.php";

        $resellerportal_notification = $_POST["resellerportal_notification"];

        $query = "UPDATE `information` SET
                `resellerportal_notification`=?
                WHERE `information_id`=?";


        $information_id = 1;

        $stmt1 = $dbTools->getConnection()->prepare($query);

        $stmt1->bind_param('ss', $resellerportal_notification, $information_id);

        $stmt1->execute();

        $modem = $stmt1->get_result();
        if ($stmt1->errno == 0) {
            echo "{\"edited\" :true,\"error\" :\"null\"}";

            $sqlTot = "SELECT `customer_id`,`full_name`,`phone`,`email`
            FROM `customers`
            WHERE `is_reseller` = '1'";
            mysqli_query($dbTools->getConnection(), "SET CHARACTER SET utf8");
            $stmt = $dbTools->getConnection()->prepare($sqlTot);

            $stmt->execute();

            $result = $stmt->get_result();
            
            if($resellerportal_notification != ""){
                while ($row = mysqli_fetch_array($result)) {
                    sendEmail($row['email'], "Alert", "Dear Reseller,\n\r A new alert has "
                        . "been issued \"" . $resellerportal_notification . "\".\n\r Best,");
                }

                sendEmail("demhahero@gmail.com", "Alert", "Dear Reseller,\n\r A new alert has "
                        . "been issued \"" . $resellerportal_notification . "\".\n\r Best,");
            }
        } else {

            echo "{\"edited\" :\"false\",\"error\" :\"failed to insert value\"}";
        }
    }
} else {
    echo "{\"message\" :", "\"you don't have access to this page\""
    , ",\"error\":true}";
}

function sendEmail($to, $title, $body) {
    try {

        // Create the Transport
        $transport = (new Swift_SmtpTransport('mail.amprotelecom.com', 25))
                ->setUsername('alialsaffar')
                ->setPassword('zOIq6dX$@Pq44M')
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('AmProTelecom INC. - ' . $title))
                ->setFrom(['info@amprotelecom.com' => 'AmProTelecom INC.'])
                ->setTo([$to])
                ->setBody($body);
        ;

        // Send the message
        $result = $mailer->send($message);
    } catch (Exception $e) {
        
    }
}

?>
