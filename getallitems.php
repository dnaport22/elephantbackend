<?php

require_once 'item.php';
require_once 'response.php';

$filter = @$_GET['filter'];
$list = ($filter) ? Item::getAdminFilteredList(@$_GET['offset'], @$_GET['limit'], $filter) : Item::getAdminList(@$_GET['offset'], @$_GET['limit']);
$response = new Response(1);
$response->setItems($list);
$response->send();
