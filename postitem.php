<?php
require "db_connect.php";
require "item.php";
require "response.php";
require_once "user.php";

/**
 * Class postItem implements a post item manager.
 */
class postItem
{
	/**
	 * Database connection.
	 *
	 * @var dbConnect
	 */
	private $my_query = NULL;

	/**
	 * Folder where the images will be saved.
	 *
	 * @var string
	 */
	private $savefolder = 'images/';

	/**
	 * postItem constructor.
	 *
	 * @param dbConnect $my_query
	 */
	public function __construct(dbConnect $my_query) {
		$this->my_query = $my_query;
	}

	private function createFolderIfNecessary() {
		if (!file_exists($this->savefolder)) {
			mkdir($this->savefolder, 0755, TRUE);
		}
	}

	/**
	 * Uploads the image received where it belongs.
	 *
	 * @return string
	 *   Path to the uploaded image.
	 */
	public function uploadImage() {
		$imagesrc = $_FILES["file"]["name"];
		$postimage = $_FILES["file"]["tmp_name"];

		$this->createFolderIfNecessary();

		$target_file = $this->savefolder . time() . '_' . basename($imagesrc);
		$access_path = time() . '_' . basename($imagesrc);
		if (move_uploaded_file($postimage, $target_file) == True) {
			return $access_path;
		}
		Response::flush(0, 'Unable to upload the image. Please try again in few minutes or contact an administrator.');
	}

	/**
	 * Insert the item info.
	 *
	 * @param User $user
	 *   User performing the action.
	 *
	 * @param $image_source
	 *   Source image generated.
	 */
	public function insertInfo(User $user, $image_source)
	{
		$item = new Item($this->my_query);
		$item->setUid($user->getUid());
		$item->setName($_POST['itemName']);
		$item->setDescription($_POST['desc']);
		$item->setImage($image_source);
		$item->setStatus(0);
		if ($item->save()) {
			//$this->sendEmail($user);
			Response::flush(1, 'The email could not be sent.');
		}
		Response::flush(0, 'An error ocurred while trying to post your item. Please try again in few minutes or contact an administrator.');
	}

	/**
	 * Sends an email notifying of the addition of the object.
	 */
	public function sendEmail(User $user) {
		$name = $user->getName();
		$subject= "Item upload request!";
		$message = <<<HTML
Post request by: {$name}<br/>
Your item will be posted within sustainability app soon after approval.<br/>
Thank you.<br/>
Regards, Sustainability all Team.
HTML;

		$header  = "From:no-reply@myelephant.xyz \r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
		if (mail($user->getEmail(), $subject, $message, $header) == True) {
			Response::flush(1, 'Item posted successfully');
		}
	}
}

try {
	$user = User::authorize();
	$post_item = new postItem($mysql_db);
	$image_source = $post_item->uploadImage();
	$post_item->insertInfo($user, $image_source);
}
catch (Exception $exception) {
	Response::flush(0, $exception->getMessage());
}
