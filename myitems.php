<?php

require_once 'user.php';
require_once 'item.php';
require_once 'response.php';

$user = User::authorize();
$list = Item::getUserList($user, @$_GET['offset'], @$_GET['limit']);
$response = new Response(1);
$response->setItems($list);
$response->send();
