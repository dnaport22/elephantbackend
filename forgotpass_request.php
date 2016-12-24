<?php

require_once 'db_connect.php';
require_once 'forgotpass.php';
require_once 'response.php';

$forgot_pass = new ForgotPassword($mysql_db);
$email = @$_POST['email'];
if ($key = $forgot_pass->create($email)) {
  if ($forgot_pass->sendEmail($email, $key)) {
    Response::flush(1, 'An email has been sent to your email address with instructions to reset your password.');
  }
}
Response::flush(0, 'The password could not be reset. Please try again in few minutes or contact an administrator.');
