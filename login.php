<?php
require_once "db_connect.php";
require_once "user.php";
require_once "response.php";

class userLogin
{
	private $my_query = NULL;
	private $email = NULL;
	private $raw_password = NULL;
	private $status = NULL;
	
	public function __construct(dbConnect $my_query, $email, $pass)
	{
		$this->my_query = $my_query;
		$this->email = $email;
		$this->raw_password = $pass;
	}

	public function userAuth()
	{
		$user = new User($this->my_query);
		if ($user->authenticate($this->email, $this->raw_password)) {
			$response = new Response(1);
			$response->setUser($user);
			$response->send();
		}

		Response::flush(0);
	}
}

try {
	$user_login = new userLogin($mysql_db, @$_POST['email'], @$_POST['pass']);
	$user_login->userAuth();
}
catch (Exception $exception) {
	Response::flush(0, $exception->getMessage());
}
