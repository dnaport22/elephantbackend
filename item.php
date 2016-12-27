<?php

require_once 'db_connect.php';
require_once 'user.php';

/**
 * Class Item implements a item manager.
 */
class Item implements JsonSerializable {

  /**
   * Database connection.
   *
   * @var dbConnect
   */
  private $db;

  /**
   * Properties of an item.
   *
   * @var int|string
   */
  private $uid;
  private $id;
  private $name;
  private $description;
  private $image;
  private $postDate;
  private $status;
  /**
   * @var int|string additional objects required by admin
   */
  private $useremail;
  private $username;

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
   * Loads the item data for users.
   *
   * @param array $data
   * @return bool
   */
  protected function load($data) {
    if (is_array($data)) {
      $this->setUid($data['user_id']);
      $this->setId($data['itemID']);
      $this->setName($data['item_name']);
      $this->setDescription($data['description']);
      $this->setImage($data['image_src']);
      $this->setStatus($data['status']);
      $this->setPostDate($data['post_date']);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Loads the item data for admin.
   *
   * @param array $data
   * @return bool
   */
  protected function loadAdmin($data) {
    if (is_array($data)) {
      $this->setUid($data['user_id']);
      $this->setId($data['itemID']);
      $this->setName($data['item_name']);
      $this->setDescription($data['description']);
      $this->setImage($data['image_src']);
      $this->setStatus($data['status']);
      $this->setPostDate($data['post_date']);
      $this->setUserEmail($data['email']);
      $this->setUsername($data['name']);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Saves the item.
   *
   * @return bool
   */
  public function save() {
    $query = <<<SQL
      INSERT INTO items (user_id, itemID, item_name, description, image_src, post_date, status)
				VALUES (:uid, :itemid, :name, :description, :image, :postdate, :status)
			ON DUPLICATE KEY UPDATE
			  user_id = VALUES(user_id),
			  item_name = VALUES(item_name),
			  description = VALUES(description),
			  image_src = VALUES(image_src),
			  post_date = VALUES(post_date),
			  status = VALUES(status)
SQL;

    $result = $this->db->query($query, [
      ':uid' => $this->getUid(),
      ':itemid' => $this->getId(),
      ':name' => $this->getName(),
      ':description' => $this->getDescription(),
      ':image' => $this->getImage(),
      ':postdate' => $this->getPostDate() ?: date('Y-m-d'),
      ':status' => $this->getStatus(),
    ]);
    return (bool) $result->rowCount();
  }

  /**
   * Gets a list of items.
   *
   * @param int $offset
   *   Offset to apply to the list.
   *
   * @param int $limit
   *   Number of items to return.
   *
   * @return Item[]
   */
  public static function getList($offset, $limit) {
    global $mysql_db;
    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast('SELECT * FROM items WHERE status = :status
     ORDER BY itemID DESC LIMIT :limit OFFSET :offset', [
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
      ':status' => '1',
    ]);
    return self::loadList($results);
  }

  /**
   * Gets a list of items for admin.
   *
   * @param int $offset
   *   Offset to apply to the list.
   *
   * @param int $limit
   *   Number of items to return.
   *
   * @return Item[]
   */
  public static function getAdminList($offset, $limit) {
    global $mysql_db;
    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast('SELECT items.user_id, items.itemID, items.item_name, items.description, items.image_src, items.post_date, user_profiles.name, user_profiles.email, items.status FROM items INNER JOIN user_profiles ON items.user_id = user_profiles.uid
                                     ORDER BY itemID DESC LIMIT :limit OFFSET :offset', [
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
    ]);
    return self::loadAdminList($results);
  }

  /**
   * Gets a list of items for users.
   *
   * @param int $offset
   *   Offset to apply to the list.
   *
   * @param int $limit
   *   Number of items to return.
   *
   * @return Item[]
   */
  public static function getListFiltered($offset, $limit, $filter) {
    global $mysql_db;
    $query = <<<SQL
      SELECT * FROM items WHERE
        (CONCAT(' ', LOWER(item_name), ' ') LIKE LOWER(:filter) AND
        status = :status) OR
        (CONCAT(' ', LOWER(description), ' ') LIKE LOWER(:filter) AND
        status = :status)
      ORDER BY itemID DESC LIMIT :limit OFFSET :offset
SQL;



    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast($query, [
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
      ':filter' => '%' . $filter . '%',
      ':status' => '1',
    ]);
    return self::loadList($results);
  }

  /**
   * Gets a list of items for admin.
   *
   * @param int $offset
   *   Offset to apply to the list.
   *
   * @param int $limit
   *   Number of items to return.
   *
   * @return Item[]
   */
  public static function getAdminListFiltered($offset, $limit, $filter) {
    global $mysql_db;
    $query = <<<SQL
      SELECT * FROM items WHERE
        CONCAT(' ', LOWER(item_name), ' ') LIKE LOWER(:filter) AND
        CONCAT(' ', LOWER(description), ' ') LIKE LOWER(:filter)
      ORDER BY itemID DESC LIMIT :limit OFFSET :offset
SQL;



    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast($query, [
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
      ':filter' => '%' . $filter . '%',
    ]);
    return self::loadAdminList($results);
  }

  public static function getUserList(User $user, $offset, $limit) {
    global $mysql_db;
    /** @var PDOStatement $results */
    $results = $mysql_db->queryCast('SELECT * FROM items WHERE user_id = :uid AND status = :status ORDER BY itemID DESC LIMIT :limit OFFSET :offset', [
      ':uid' => $user->getUid(),
      ':offset' => (int) $offset ?: 0,
      ':limit' => (int) $limit ?: 10,
      ':status' => '1'
    ]);
    return self::loadList($results);
  }

  /**
   * Load result list for users
   *
   * @param PDOStatement $results
   * @return array
   */
  protected static function loadList(PDOStatement $results) {
    global $mysql_db;
    $list = [];
    while ($data = $results->fetch(PDO::FETCH_ASSOC)) {
      $item = new static($mysql_db);
      $item->load($data);
      $list[] = $item;
    }
    return $list;
  }

  /**
   * Load result list for admin
   *
   * @param PDOStatement $results
   * @return array
   */
  protected static function loadAdminList(PDOStatement $results) {
    global $mysql_db;
    $list = [];
    while ($data = $results->fetch(PDO::FETCH_ASSOC)) {
      $item = new static($mysql_db);
      $item->loadAdmin($data);
      $list[] = $item;
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

  /**
   * Loads item data given an item id.
   *
   * @param integer $itemId
   * @return bool
   */
  public function loadByItemId($itemId) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM items WHERE itemID = :itemid', [
      ':itemid' => $itemId,
    ]));
  }

  /**
   * Loads item data given an item name.
   *
   * @param string $name
   * @return bool
   */
  public function loadByItemName($name) {
    return $this->loadFromPDO($this->db->query('SELECT * FROM items WHERE item_name = :name', [
      ':name' => $name,
    ]));
  }

  /**
   * @return integer
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * @return User
   */
  public function getUser() {
    $user = new User($this->db);
    $user->loadByUid($this->getUid());
    return $user;
  }

  /**
   * @param integer $uid
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param integer $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * @param string $image
   */
  public function setImage($image) {
    $this->image = $_SERVER['HTTP_HOST'] . $image;
  }

  /**
   * @return string
   */
  public function getPostDate() {
    return $this->postDate;
  }

  /**
   * @param string $postDate
   */
  public function setPostDate($postDate) {
    $this->postDate = $postDate;
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
   * @param mixed $useremail
   */
  public function setUseremail($userEmail) {
    $this->useremail = $userEmail;
  }

  /**
   * @param string $username
   */
  public function setUsername($username) {
    $this->username = $username;
  }

}
