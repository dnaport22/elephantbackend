<?php

require_once 'db_connect.php';
require_once 'password.php';

/**
 * Class User implements a user handler.
 */
class User implements JsonSerializable {

  /**
   * Database connection.
   *
   * @var dbConnect
   */
  private $db;

  /**
   * User properties.
   *
   * @var string|int
   */
  private $uid;
  private $name;
  private $email;
  private $password;
  private $activation;
  private $status;
  private $role;
  private $registerDate;

  /**
   * User constructor.
   *
   * @param dbConnect $db
   */
  public function __construct(dbConnect $db) {
    $this->db = $db;
  }

  function jsonSerialize() {
    unset($this->db, $this->password);
    return get_object_vars($this);
  }

  protected function load($data) {
    if (is_array($data)) {
      $this->setUid($data['uid']);
      $this->setName($data['name']);
      $this->setEmail($data['email']);
      $this->setActivation($data['activation']);
      $this->setStatus($data['status']);
      $this->setPassword($data['password']);
      $this->setRole($data['role']);
      $this->setRegisterDate($data['date']);
      return TRUE;
    }
    return FALSE;
  }

  public function save($password = '') {
    $query = <<<SQL
      INSERT INTO user_profiles (uid, name, email, password, activation, status, role, registerDate)
				VALUES (:uid, :name, :email, :password, :activation, :status, :role, :registerDate)
			ON DUPLICATE KEY UPDATE
			  name = VALUES(name),
			  email = VALUES(email),
			  password = VALUES(password),
			  activation = VALUES(activation),
			  status = VALUES(status),
        role = VALUES(role),
        registerDate = VALUES(registerDate),
SQL;

    $result = $this->db->query($query, [
      ':uid' => $this->getUid(),
      ':name' => $this->getName(),
      ':email' => $this->getEmail(),
      ':password' => ($password) ? Password::getInstance()->getHash($password) : $this->getPassword(),
      ':activation' => $this->getActivation(),
      ':status' => $this->getStatus(),
      ':role' => $this->getRole(),
      ':registerDate' => $this->getRegisterDate() ?: date('Y-m-d'),
    ]);
    return (bool) $result->rowCount();
  }

  protected function loadFromPDO(PDOStatement $result) {
    return $this->load($result->fetch(PDO::FETCH_ASSOC));
  }

  public function loadByUid($uid) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM user_profiles WHERE uid = :uid', [
      ':uid' => $uid,
    ]));
  }

  public function loadByEmail($email) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM user_profiles WHERE email = :email', [
      ':email' => $email,
    ]));
  }

  public function loadByCode($code) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM user_profiles WHERE activation = :code', [
      ':code' => $code,
    ]));
  }

  public function authenticate($email, $password) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM user_profiles WHERE email = :email AND password = :password', [
      ':email' => $email,
      ':password' => Password::getInstance()->getHash($password),
    ]));
  }

  public static function authorize() {
    global $mysql_db;
    $user = new static($mysql_db);
    if (!$user->loadByCode($_REQUEST['code'])) {
      Response::flush(0, 'You are not authorized to perform this action.');
    }
    return $user;
  }

  public function isActive() {
    return $this->getStatus() == 1;
  }

  public function isInactive() {
    return !$this->isActive();
  }

  /**
   * @return mixed
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * @param mixed $uid
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * @return mixed
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return mixed
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * @return mixed
   */
  public function getActivation() {
    return $this->activation;
  }

  /**
   * @param mixed $activation
   */
  public function setActivation($activation) {
    $this->activation = $activation;
  }

  /**
   * @return mixed
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param mixed $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * @return mixed
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * @param mixed $password
   */
  public function setPassword($password) {
    $this->password = $password;
  }

  /*
   * @param mixed $role
   */
  public function setRole($role) {
    $this->role = $role;
  }

  /*
   * @param mixed $role
   */
  public function getRole() {
    return $this->role;
  }

  /**
   * @return string
   */
  public function getRegisterDate() {
    return $this->postDate;
  }

  /**
   * @param string $postDate
   */
  public function setRegisterDate($registerDate) {
    $this->registerDate = $registerDate;
  }

}
