<?php

require_once 'db_connect.php';
require_once 'forgotpass.php';
require_once 'user.php';
require_once 'response.php';

$key = @$_POST['key'];
$forgot_pass = new ForgotPassword($mysql_db);
if ($data = $forgot_pass->verify($key)) {
  $user = new User($mysql_db);
  if ($user->loadByEmail($data['email'])) {
    $user->save(@$_POST['pass']);
    $forgot_pass->remove($key);
    Response::flush(1, 'The password has been updated successfully.');
  }
}
Response::flush(0, 'Unable to reset the password. The key provided is not valid, has expired or has already been used.');