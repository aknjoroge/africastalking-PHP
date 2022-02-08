<?php



require 'vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;

include_once "env.php";




class sms
{
    function sendSms($phone)
    {
        // Set your app credentials
        $username   = util::$username;
        $apiKey     = util::$apiKey;
        // Initialize the SDK
        $AT         = new AfricasTalking($username, $apiKey);
        // Get the SMS service
        $sms        = $AT->sms();
        // Set the numbers you want to send to in international format
        $recipients = "$phone";
        // Set your message
        $message    = "I'm a lumberjack and its ok, I sleep all night and I work all day";
        // Set your shortCode or senderId
        $from       = util::$shortCode;
        try {
            // Thats it, hit send and we'll take care of the rest
            $result = $sms->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => $from
            ]);

            print_r($result);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
