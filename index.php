<?php



$sessionId   = $_POST["sessionId"];  
$serviceCode = $_POST["serviceCode"]; 
$phoneNumber = $_POST["phoneNumber"];  
$text        = $_POST["text"]; 
include_once "menu.php";
$menu = new menu();
 
$phoneNumber = $menu->addCountryCode($phoneNumber);


$user = new currentUser($phoneNumber);

$con = new dbConnector();
$pdo=$con->connectToDb(); //connection to the database

$registered =$user->isUserRegister($pdo);


$text= $menu->middleway($text); //processing the text and searching for back/menu number

if( $text == "" && $registered){
    $name = $user->readName($pdo);
    $menu->menuForRegisteredUser($name);
    //if text is empty and user is registered

}else if( $text=="" && !$registered){
     //if text is empty and user is unregistered
    $menu->menuForUnregisteredUser();
 

}else if(!$registered){
    //if text is not empty and user is unregistered
    $textArray = explode("*",$text);
    switch($textArray[0]){
        case 1:
            $menu->registerMenu($textArray,$pdo);
            break;
            case 2: 
                echo "END GoodBye \n &copy; TechKey";
                break;   
            default:
           echo "END invalid choice try again";
    }


}else{

    $textArray = explode("*",$text);
    switch($textArray[0]){
        case 1:
            $menu->account($phoneNumber,$textArray);
            break;
            case 2: 
                $menu->sendMoney($textArray,$pdo,$phoneNumber,$sessionId);
                break; 
                case 3: 
                    $menu->accoutBalance($textArray,$pdo,$phoneNumber);
                    break;   
                    case 4: 
                        $menu->newsLetters($textArray);
                        break; 
                        case 5: 
                            $menu->blogging($textArray);
                            break; 
                            case 6: 
                                $menu->development($textArray);
                                break; 
                                case 7: 
                                    $menu->clientAssistance($textArray);
                                    break; 
                                    case 8: 
                                        $menu->clientAssistance($textArray);
                                        break; 
            default:
           echo "END invalid choice try again";
    }
}


?>




