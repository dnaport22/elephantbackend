<?php
require "db_connect.php";
require_once 'user.php';
require_once 'response.php';
require_once 'settings.php';

class registerUser {

  private $my_query = NULL;

  private $name = NULL;
  private $email = NULL;
  private $raw_password = NULL;
  private $activation = NULL;

  public function __construct(dbConnect $my_query, $name, $email, $password) {
    $this->my_query = $my_query;
    $this->name = $name;
    $this->email = $email;
    $this->raw_password = $password;
    $this->activation = md5($this->email . time());
  }

  public function verifyUser() {
    $user = new User($this->my_query);
    if ($user->loadByEmail($this->email)) {
      $response = new Response(0, 'The email already exists.');
      $response->send();
    }
  }

  public function submitDetails() {
    $this->verifyUser();

    $user = new User($this->my_query);
    $user->setName($this->name);
    $user->setEmail($this->email);
    $user->setActivation($this->activation);
    $user->setStatus(0);
    $user->setRole(0);
    if ($user->save($this->raw_password)) {
      $this->emailUser($user);
      $response = new Response(1, 'User successfully registered.');
      $response->send();
    }

    $response = new Response(0);
    $response->send();
  }

  public function emailUser(User $user) {
    $to = $user->getEmail();
    $subject = "The elephant app account confirmation";
    $activation_link = $this->getActivationUrl($user);
    $message = <<<HTML
Hello {$user->getName()},<br/><br/>
To complete the registration, verify your email address by clicking on the link below:<br/>
<a href="{$activation_link}"><b>Click here to verify</b></a><br><br>
Cheers,<br>The elephant team.<br><br><hr>
HTML;
    $header  = "From: no-reply@myelephant.xyz \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";
    return mail($to, $subject, $message, $header) == TRUE;
  }

  protected function getActivationUrl(User $user) {
    $settings = new Settings();
    return 'http://' . $settings->getServerUrl($_SERVER['HTTP_HOST']) . '/www/#/app/activation/' . $user->getActivation();
  }
}

try {
  $user_register = new registerUser($mysql_db, @$_POST['name'], @$_POST['email'], @$_POST['pass']);
  $user_register->submitDetails();
}
catch (Exception $exception) {
  Response::flush(0, $exception->getMessage());
}
