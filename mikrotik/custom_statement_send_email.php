<?php
include_once "header.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
  // The message
  $message = "Line 1\r\nLine 2\r\nLine 3";

  // In case any of our lines are larger than 70 characters, we should use wordwrap()
  $message = wordwrap($message, 70, "\r\n");

  $headers = 'From: alialsaffar@p3plcpnl0852.prod.phx3.secureserver.net' . "\r\n" .
  'Reply-To: alialsaffar@p3plcpnl0852.prod.phx3.secureserver.net' . "\r\n" .
  'X-Mailer: PHP/' . phpversion();

  // Send
  echo mail('micro_1900@yahoo.com', 'My Subject', $message, $headers);
 */

/*
  $my_file = "invoice_Telnova_1505923932.pdf";
  $my_path = __DIR__ . "/custom_invoices/";
  $my_name = "Olaf Lederer";
  $my_mail = "alialsaffar@p3plcpnl0852.prod.phx3.secureserver.net";
  $my_replyto = "alialsaffar@p3plcpnl0852.prod.phx3.secureserver.net";
  $my_subject = "This is a mail with attachment.";
  $my_message = "Hallo,rndo you like this script? I hope it will help.rnrngr. Olaf";
  mail_attachment($my_file, $my_path, "micro_1900@yahoo.com", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);

  function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $body) {
  $file = $path . $filename;
  $file_size = filesize($file);
  $handle = fopen($file, "r");
  $content = fread($handle, $file_size);
  fclose($handle);
  $content = chunk_split(base64_encode($content));
  $uid = md5(uniqid(time()));


  $eol = PHP_EOL;

  // Basic headers
  $header = "From: " . $from_name . " <" . $from_mail . ">" . $eol;
  $header .= "Reply-To: " . $replyto . $eol;
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"";

  // Put everything else in $message
  $message = "--" . $uid . $eol;
  $message .= "Content-Type: text/html; charset=ISO-8859-1" . $eol;
  $message .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
  $message .= $body . $eol;
  $message .= "--" . $uid . $eol;
  $message .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
  $message .= "Content-Transfer-Encoding: base64" . $eol;
  $message .= "Content-Disposition: attachment; filename=\"" . $filename . "\"" . $eol;
  $message .= $content . $eol;
  $message .= "--" . $uid . "--";

  if (mail($mailto, $subject, $message, $header)) {
  echo "mail send ... OK"; // or use booleans here
  } else {
  echo "mail send ... ERROR!";
  }
  }
 */

/*
  require 'phpmailer/vendor/autoload.php';
  $email = new PHPMailer(true);

  try {

  $email->Host = "relay-hosting.secureserver.net";
  $email->Port = 25;
  $email->SMTPDebug = 0;
  $email->SMTPSecure = "none";
  $email->SMTPAuth = false;
  $email->Username = "";
  $email->Password = "";

  $email->From = 'alialsaffar@p3plcpnl0852.prod.phx3.secureserver.net';
  $email->FromName = 'ali';
  $email->Subject = 'Message Subject';
  $email->Body = "hello";
  $email->AddAddress('micro_1900@yahoo.com');

  //$file_to_attach = __DIR__ . "/custom_invoices/"."invoice_Telnova_1505923932.pdf";
  //$email->AddAttachment( $file_to_attach , 'NameOfFile.pdf' );

  $email->Send();
  echo 'Message has been sent';
  } catch (Exception $e) {
  echo 'Message could not be sent.';
  echo ' Mailer Error: ' . $mail->ErrorInfo;
  }
 */

require_once 'swiftmailer/vendor/autoload.php';

$filename = $_GET["filename"];
$to = $_GET["to"];
$body = $_GET["body"];

// Create the Transport
$transport = (new Swift_SmtpTransport('mail.amprotelecom.com', 25))
        ->setUsername('alialsaffar')
        ->setPassword('zOIq6dX$@Pq44M')
;

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('AmProTelecom INC. - Statement'))
        ->setFrom(['info@amprotelecom.com' => 'AmProTelecom INC.'])
        ->setTo([$to])
        ->setBody($body)
        ->attach(Swift_Attachment::fromPath(__DIR__ . "/custom_statement/".$filename))
;

// Send the message
$result = $mailer->send($message);
if($result == 1){
    ?>
        <script>window.location.href = "custom_statement.php";</script>
    <?php
}
else{
    echo "Error - Did not send.";
}

include_once "footer.php";