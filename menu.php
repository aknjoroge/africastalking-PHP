<?php



include_once "env.php";
include_once "user.php";
include_once "transaction.php";
include_once "message.php";

class menu
{

    public $userName = "";
    protected $text, $sessionId;

    /*
    function __construct($receivedText, $receivedSessionId){
        $this->text = $receivedText;
        $this->sessionId = $receivedSessionId;
        
    }
    */
    function __construct()
    {
    }

    function menuForRegisteredUser($name)
    {
        $response  = "CON Welcome $name, to PHP USSD service\n";
        $response .= "1. My Account \n";
        $response .= "2. Send Money \n";
        $response .= "3. Account Balance\n";
        $response .= "4. News Letters \n";
        $response .= "5. Blogging Services \n";
        $response .= "6. Development Services \n";
        $response .= "7. Client Assistance \n";
        $response .= "8. Daily Updates \n";

        $response .= " &copy; PHP";
        echo $response;
    }
    function menuForUnregisteredUser()
    {
        $response  = "CON PHP USSD \n Please register to continue with the servcie \n";
        $response .= "1. Register \n";
        $response .= "2. Exit \n";
        $response .= " &copy; PHP\n";
        echo $response;
    }
    function registerMenu($textArray, $pdo)
    {
        $level = count($textArray);
        if ($level == 1) {
            $response  = "CON Enter Phone Number To Register  \n";
            echo $response;
        } else if ($level == 2) {
            $response  = "CON Set a Password  \n";
            echo $response;
        } else if ($level == 3) {
            $response  = "CON Confirm The Password  \n";
            echo $response;
        } else if ($level == 4) {
            $response  = "CON Enter a Name  \n";
            echo $response;
        } else if ($level == 5) {
            //getting the data submitted
            //$phone = $textArray[1];
            $phone = $this->addCountryCode($textArray[1]);
            $password = $textArray[2];
            $confirmPAssword = $textArray[3];
            $name = $textArray[4];
            if ($password != $confirmPAssword) {
                echo "END Passwords Do not Match Try again !!";
            } else {
                $user = new currentUser($phone);
                $user->setname($name);
                $user->setpin($password);
                $user->setbalance(util::$userInitialBalance);
                $user->register($pdo);

                //  echo "END  Succesfully registered user $name \n Phone : $phone with password : $password";
            }
        } else {
            echo "END  Try Again :(";
        }
    }

    //Main Menu

    function account($phone, $textArray)
    {
        $response  = "CON Account Details \n";
        $response .= "1. Name " . $this->userName . "\n";
        $response .= "2. Phone " . $phone . "\n";
        $response .= "3. Password : your password \n";
        $response .= "4. ID : Y656-7767-1111 \n";
        echo $response;
    }

    function sendMoney($textArray, $pdo, $phone, $sessionId)
    {
        $receiver = null;
        $nameOfReceiver = null;

        $level = count($textArray);
        if ($level == 1) {
            $response  = "CON Send Money \n";
            $response .= "Enter Phone Number \n";
            echo $response;
        } else if ($level == 2) {
            $response = "CON Enter Amount to send \n";
            echo $response;
        } else if ($level == 3) {
            $response = "CON Enter pin \n";
            echo $response;
        } else if ($level == 4) {

            $receiverPhone = $this->addCountryCode($textArray[1]);
            $receiverUser =  new currentUser($receiverPhone);
            if ($receiverUser->isUserRegister($pdo)) {
                //receiver is registered
                $receiverName = $receiverUser->readName($pdo);
                $tosendAmount = $textArray[2];
                $response  = "CON Send Amount : $tosendAmount To $receiverName \n 1. Confirm\n";
                $response .= "2. Cancel \n";
                $response .= util::$backOption . ". Back \n"; //accessing a class in another php file
                $response .= util::$mainMenu . ". Main Menu \n";
                echo $response;
            } else {
                echo "END Receiver is Not registred";
            }
        } else if ($level == 5 && $textArray[4] == 1) {
            try {
                $enteredPin = $textArray[3];
                $user = new currentUser($phone);
                $user->correctPin($pdo, $phone);
                if (password_verify($enteredPin, $user->correctPin($pdo, $phone))) {

                    //confirm you have money

                    if (($user->checkBalance($pdo) - $textArray[2] - util::$transactionFee) > 0) {

                        //connect to receiver
                        $receiverPhone = $this->addCountryCode($textArray[1]);
                        $receiverUser =  new currentUser($receiverPhone);
                        $tosendAmount = $textArray[2];
                        //initial balances
                        $senderBalance = $user->checkBalance($pdo);
                        $receiverBalance = $receiverUser->checkBalance($pdo);
                        $transactionType = "send";
                        $transaction = new transaction($tosendAmount, $transactionType);
                        $transaction_code =  $transaction->sendMoney(
                            $pdo,
                            $user->readUserId($pdo),
                            $receiverUser->readUserId($pdo),
                            ($senderBalance - $tosendAmount - util::$transactionFee),
                            ($receiverBalance + $tosendAmount)
                        );

                        echo $transaction_code;
                        //  echo "END Successfully sent Money";

                    } else {
                        echo "END Your Balance is Low \nnote transaction cost is " . util::$transactionFee . "/=";
                    }
                } else {
                    echo "END  Error Wrong Pin";
                }
            } catch (Exception $e) {
                echo "END" . $e->getMessage();
            }
        } else if ($level == 5 && $textArray[4] == 2) {
            echo "END Transaction Cancelled";
        } else if ($level == 5 && $textArray[4] == util::$backOption) {
            echo "END Go back";
        } else if ($level == 5 && $textArray[4] == util::$mainMenu) {
            echo "END OPEN menu";
        } else {
            echo "END invalid";
        }
    }
    function accoutBalance($textArray, $pdo, $phone)
    {
        $level = count($textArray);
        if ($level == 1) {
            $response  = "CON Balance \n";
            $response .= "Enter Pin \n";
            echo $response;
        } else if ($level == 2) {

            $enteredPin = $textArray[1];
            $user = new currentUser($phone);
            $user->correctPin($pdo, $phone);
            if (password_verify($enteredPin, $user->correctPin($pdo, $phone))) {


                /*
                $response="END your current Balance is ".$user->checkBalance($pdo);

                $sms = new sms($phone);
                $result = $sms->sendSms($response);

                if ($result['status'] == "Success"){
                    $response = "END your current Balance is ".$user->checkBalance($pdo)."\n
                    You will Receive a message shortly ";
                    echo $response;
                }else{
                    echo "END your current Balance is ".$user->checkBalance($pdo);
                }
                */

                $sending = new sms();
                $sending->sendSms($phone);
            } else {
                echo "END  Wrong Pin";
            }
        }
    }

    function newsLetters($textArray)
    {
        $response  = "CON Get Our Daily News \n";
        $response .= "1. Subscribe \n";
        $response .= "2. Exit \n";
        echo $response;
    }

    function blogging($textArray)
    {
        $response  = "CON Blogging and services \n";
        $response .= "Hallo " . $this->userName . " \n";
        $response .= "Our Blogging service is available online, where \n You can post your own content free on our website\n Or You can get access to our content creators work \n please visit www.blog.PHP.co.ke\n";
        $response .= "1. If you Would like to join our creators group reply with 1\n";
        $response .= "2. Exit";
        echo $response;
    }
    function development($textArray)
    {
        $response  = "CON Select a PHP service\n";
        $response .= "1. Web and Software Development \n";
        $response .= "2. Branding \n";
        $response .= "3. Digital Marketting \n";
        $response .= "4. Photography \n";
        echo $response;
    }

    function clientAssistance($textArray)
    {
        $response  = "CON Client Menu\n";
        $response .= "1. Request a Job Redo \n";
        $response .= "2. Contact Help \n";
        $response .= "3. Order a service \n";
        echo $response;
    }
    function dailyUpdates($textArray)
    {
        $response  = "CON Daily Updates Menu\n";
        $response .= "1. Subscribe to our news Letter \n";
        $response .= "2. Exit \n";

        echo $response;
    }

    public  function middleway($text)
    { //check if the incoming text has a 98/99 to show user tried to go back
        return $this->goback($this->goToMenu($text));
    }

    public function goback($text)
    {  //searches for 98
        $explodedtext = explode("*", $text);
        while (array_search(util::$backOption, $explodedtext) != false) {
            $firstIndex = array_search(util::$backOption, $explodedtext);
            array_splice($explodedtext, $firstIndex - 1, 2);
        }
        return join("*", $explodedtext);
    }

    public function goToMenu($text)
    {  //searches for 99

        $explodedtext = explode("*", $text);
        while (array_search(util::$mainMenu, $explodedtext) != false) {
            $firstIndex = array_search(util::$mainMenu, $explodedtext);
            $explodedtext = array_slice($explodedtext, $firstIndex + 1);
        }

        return join("*", $explodedtext);
    }


    public function addCountryCode($phone)
    {
        $data = $phone;

        return util::$countryCode . +substr($data, 1);
    }
}
