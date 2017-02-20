<?php

require_once 'db_connect.php';
require_once 'user.php';
require_once 'item.php';
require_once 'response.php';

$user = User::authorize();
$item = new Item($mysql_db);
$item->loadByItemId(@$_POST['itemId']);
if ($item->authorize($user)) {
	$item->setStatus(-1);
	$item->save();
	Response::flush(1, 'Item marked as given away');
}