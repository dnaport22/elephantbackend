<?php

require_once 'db_connect.php';
require_once 'user.php';
require_once 'item.php';
require_once 'response.php';

function emailUser() {
  $to_email = $_POST['useremail'];
  $to_username = $_POST['username'];
  $itemname = $_POST['itemname'];
  $subject = "The elephant app item declined";
  $message = <<<HTML
<b>This is an automated email sent by the elephant app:</b><hr>
Hello {$to_username},<br/><br/>
Your request for posting {$itemname} was declined. It may have been declined for one of the following reasons:
<ul><li>poor image quality</li><li>title or description does not match the image</li>
<li>image, title or description is inaccurate or offensive</li></ul><br><br>
Please visit the elephant app to delete your item from your 'My items' page and then reupload, taking the above points
into consideration.<br><br>
Cheers,<br>The elephant team<br><br><hr>
HTML;
  $header  = "From: no-reply@myelephant.xyz \r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";
  return mail($to_email, $subject, $message, $header) == TRUE;
}

$user = User::authorize();
$item = new Item($mysql_db);
$item->loadByItemId(@$_POST['itemId']);
// if ($item->authorize($user)) {
// 	$item->setStatus(2);
// 	$item->save();
// 	Response::flush(1, 'Item dismissed successfully');
// }
$item->setStatus(2);
$item->save();
if (emailUser()) {
  Response::flush(1, 'Item dismissed successfully');
}
