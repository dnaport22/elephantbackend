<?php

require_once 'db_connect.php';
require_once 'user.php';
require_once 'item.php';
require_once 'response.php';

function emailUser() {
  $to_email = (@$_POST['useremail']);
  $to_username = (@$_POST['username']);
  $itemname = (@$_POST['itemname']);
  $subject = "The elephant app item approved";
  $message = <<<HTML
<b>This is an automated email sent by the elephant app:</b><hr>
Hello {$to_username},<br/><br/>
Your request for posting {$itemname} has been approved. Your item is now posted on the elephant app.<br><br>
Cheers,<br>The elephant team.<br><br><hr>
HTML;
  $header  = "Sender: no-reply@myelephant.xyz \r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";
  return mail($to_email, $subject, $message, $header) == TRUE;
}
$user = User::authorize();
$item = new Item($mysql_db);
$item->loadByItemId(@$_POST['itemId']);
// if ($item->authorize($user)) {
// 	$item->setStatus(1);
// 	$item->save();
// 	Response::flush(1, 'Item activated successfully');
// }
$item->setStatus(1);
if ($item->save()) {
  emailUser();
  Response::flush(1, 'Item activated successfully');
}
