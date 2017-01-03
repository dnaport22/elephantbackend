<?php
/**
 * @file
 * Password manager.
 */

/**
 * Class Password implements a password policy manager.
 */
class Password {

  /**
   * Salt to use to generate the hash.
   *
   * @var string
   */
  private $salt = 'ab-ksg#hk;j&/a;kl%le,.l';

  /**
   * Instance of the class.
   *
   * @var Password
   */
  private static $instance;

  /**
   * Password constructor.
   */
  private function __construct() {
  }

  /**
   * Gets a unique instance of the class.
   *
   * @return Password
   */
  public static function getInstance() {
    if (!self::$instance) {
      self::$instance = new static();
    }
    return self::$instance;
  }

  /**
   * Gets the hash of a given password.
   *
   * @param string $password
   *   plain password.
   *
   * @return string
   */
  public function getHash($password) {
    return sha1($this->salt . $password);
  }
}