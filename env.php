<?php
/*
error_reporting(E_ALL);
    ini_set('display_errors', 'On');*/


define("dbName", "YOUR_DATABASE_NAME");
define("dbUSer", "YOUR_DATABASE_USER");
define("dbPass", "YOUR_DATABASE_PASSWORD");
define("serverName", "localhost");
class util
{
    static $backOption = 98, //NAVIGATION
        $mainMenu = 99, //NAVIGATION
        $userInitialBalance = 0,
        $countryCode = "+254", //YOUR_COUNTRY_CODE
        $transactionFee = 27, $username = "sandbox",
        $apiKey = "YOUR_API_KEY", //12926f9aefb02XXXXXX831d5d904862ccXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        $shortCode = "YOUR_SHORTCODE", //83XX
        $companyName = "YOUR_COMPANY_NAME"; // TechKey Cybernetics

}


class dbConnector
{
    var $pdo;
    function __construct()
    {
        $dsn = "mysql:host=" . serverName . ";dbname" . dbName . "";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {

            $this->pdo = new PDO($dsn, dbUSer, dbPass, $options);
            //echo "Connection success";

        } catch (PDOException $e) {
            echo "END" . $e->getMessage();
        }
    }

    public function connectToDb()
    {
        return $this->pdo;
    }
    public function closeDb()
    {
        $this->pdo = null;
    }
}
