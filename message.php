<?php
header('Access-Control-Allow-Origin: *');

require_once "db_connect.php";
require_once "user.php";
require_once "response.php";

class requestItem
{
  private $my_query = NULL;

  private $reciever = NULL;
  private $sender = NULL;
  private $requestMsg = NULL;
  public function __construct(dbConnect $my_query,$requestMsg,$reciever, $sender, $senderName, $itemName)
  {
    $this->my_query = $my_query;
    $this->reciever = $reciever;
    $this->sender = $sender;
    $this->senderName = $senderName;
    $this->itemName = $itemName;
    $this->requestMsg = $requestMsg;
  }
  public function findtoUserEmail() {
    $user = new User($this->my_query);
    $user->loadByUid($this->reciever);
    $recieverEmail = $user->getEmail();

    return $recieverEmail;
  }
  public function findtoUserName() {
    $user = new User($this->my_query);
    $user->loadByUid($this->reciever);
    $recieverName = $user->getName();

    return $recieverName;
  }
  public function generateDefaultMessageBody($toName) {
    $message =  "Hi <b>" . $this->findtoUserName() . "</b>, <br><br> The elephant app is sending you this email on the behalf of <b> " . $this->senderName . " </b> for your item<b> " . $this->itemName . " </b> The message is below:<hr>" . $this->requestMsg;
    return $message;
  }
  public function sendMsg($message, $toEmail)
  {
    $to = $toEmail;
    $subject= "The elephant app item request - " . $this->itemName;
    $body = "<html>".
         "<body>".
         "$message <br><br><br>";
         "</body>";
        "</html>";
    $from_user = 'elephant app User';
    $headers = "From: \"$from_user\" <$this->sender>\r\n".
               "Sender: itemrequestemail@myelephant.xyz" . "\r\n" .
               "MIME-Version: 1.0" . "\r\n" .
               "Content-type: text/html; charset=UTF-8" . "\r\n";
        if(mail($to,$subject,$body,$headers)==True){
          echo '1';
        }else{
          echo '0';
        }
  }
}
$msg = $_POST['msg'];
$to = $_POST['toUser'];
$from = $_POST['fromUser'];
$fromUsername = $_POST['fromUsername'];
$itemName = $_POST['itemName'];
$item_request = new requestItem($mysql_db, $msg, $to, $from, $fromUsername, $itemName);
$toEmail = $item_request->findtoUserEmail();
$toName = $item_request->findtoUserName();
$message = $item_request->generateDefaultMessageBody($toName);
$item_request->sendMsg($message, $toEmail);
