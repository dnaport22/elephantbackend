<?php

require_once 'db_connect.php';
require_once 'forgotpass.php';
require_once 'response.php';

$forgot_pass = new ForgotPassword($mysql_db);
if ($forgot_pass->removeRequest()) {
  Response::flush(1, 'Request deleted.');
}
Response::flush(0, 'Error occured.');
