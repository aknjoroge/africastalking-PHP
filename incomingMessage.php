<?
include_once "menu.php";

$phone = $_POST['from'];
$text = $_POST['text'];

$user = new currentUser($phone);

//connection to the database
$con = new dbConnector();
$pdo=$con->connectToDb();

$text = explode(" ",$text);

$user->setname($text[0]);
$user->setpin($text[1]);
$user->setbalance(util::$userInitialBalance);

$user->register($pdo);


?>