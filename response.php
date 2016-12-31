<?php
/**
 * @file
 * Response handlers.
 */

/**
 * Class Response implements a response object.
 */
class Response implements JsonSerializable {

  /**
   * Status of the request:
   * - 1 successful.
   * - 0 otherwise.
   *
   * @var integer
   */
  private $status;

  /**
   * Message explaining the response.
   *
   * @var string
   */
  private $message;

  /**
   * User information.
   *
   * @var User
   */
  private $user;

  /**
   * Items information.
   *
   * @var Item[]
   */
  private $items;

  /**
   * Response constructor.
   *
   * @param int $status
   * @param string $message
   */
  public function __construct($status, $message = '') {
    $this->setStatus($status);
    $this->setMessage($message);
  }

  /**
   * @{inheritdoc}
   */
  function jsonSerialize() {
    foreach (get_object_vars($this) as $key => $value) {
      if (!$value && !is_integer($value) && !is_array($value)) {
        unset($this->{$key});
      }
    }
    return get_object_vars($this);
  }

  /**
   * Sends a redirect as a response.
   */
  public function sendRedirect() {
    if (isset($_GET['redirect'])) {
      $url = $_GET['redirect'] . '?' . http_build_query([
        'status' => $this->getStatus(),
        'message' => $this->getMessage(),
      ]);
      header("Location: $url");
      exit;
    }
  }

  /**
   * Sends a JSON as a response.
   */
  public function sendJSON() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($this);
    exit;
  }

  /**
   * Executes the sending of the response.
   */
  public function send() {
    $this->sendRedirect();
    $this->sendJSON();
  }

  /**
   * Creates and sends quickly a response.
   *
   * @param int $status
   * @param string $message
   */
  public static function flush($status, $message = '') {
    $response = new static($status, $message);
    $response->send();
  }

  /**
   * @return integer
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param integer $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * @param string $message
   */
  public function setMessage($message) {
    $this->message = $message;
  }

  /**
   * @return User
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * @param User $user
   */
  public function setUser($user) {
    $this->user = $user;
  }

  /**
   * @return Item[]
   */
  public function getItems() {
    return $this->items;
  }

  /**
   * @param mixed $items
   */
  public function setItems($items) {
    $this->items = $items;
  }
}
