<?php

require_once "db_connect.php";
require_once "user.php";
require_once "response.php";

class chat implements JsonSerializable {
  private $db;

  private $reciever; //to_user uid
  private $sender; //from_user uid
  private $requestMsg;
  public function __construct(dbConnect $my_query)
  {
    $this->db = $my_query;
  }

  /**
   * User constructor.
   *
   * @param dbConnect $db
   */
  public function __construct(dbConnect $db) {
    $this->db = $db;
  }

  function jsonSerialize() {
    unset($this->db);
    return get_object_vars($this);
  }

  /**
   * Checks if a user is authorized to manage this item.
   *
   * @param User $user
   *
   * @return bool
   *   TRUE if the user is authorized, false otherwise.
   */
  public function authorize(User $user) {
    return $this->getUid() === $user->getUid();
  }

  /**
   * Loads the item data.
   *
   * @param array $data
   * @return bool
   */
  protected function load($data) {
    if (is_array($data)) {
      $this->setToUid($data['to_user']);
      $this->setFromUId($data['from_user']);
      $this->setMessage($data['user_message']);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Saves message
   *
   */
  public function saveMessage()
  {
    $query = <<<SQL
      INSERT INTO message (to_uid, from_uid, message, time)
        VALUE (:to_uid, :from_uid, :message, :time)
SQL;
    $result = $this->db->query($query, [
      ':to_uid' => $this->getToUid(),
      ':from_uid' => $this->getFromUid(),
      ':message' => $this->getMessage(),
      ':time' => $this->getTime(),
    ]);
    return (bool) $result->rowCount();
  }

  public function getMessageByUserId(User $user, $limit, $offset) {
    global $mysql_db;
    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast('SELECT * FROM messages WHERE user_id = :uid ORDER BY date_time DESC LIMIT :limit', [
      ':uid' => $user->getUid(),
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
    ]);
    return self::loadList($results);
  }

  protected static function loadList(PDOStatement $results) {
    global $mysql_db;
    $list = [];
    while ($data = $results->fetch(PDO::FETCH_ASSOC)) {
      $message = new static($mysql_db);
      $message->load($data);
      $list[] = $message;
    }
    return $list;
  }

  /**
   * Loads object from PDO result.
   *
   * @param PDOStatement $result
   * @return bool
   */
  protected function loadFromPDO(PDOStatement $result) {
    return $this->load($result->fetch(PDO::FETCH_ASSOC));
  }

  public function getToUid() {
    return $this->reciever;
  }
  public function getFromUid() {
    return $this->sender;
  }
  public function getMessage() {
    return $this->requestMsg;
  }
  public function getTime() {
    return time();
  }
  public function setToUid($uid) {
    $this->reciever = $uid;
  }
  public function setFromUid($uid) {
    $this->sender = $uid;
  }
  public function setMessage($msg) {
    $this->requestMsg = $msg;
  }


}
