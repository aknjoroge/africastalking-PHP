<?php



class transaction{

    protected $dbName= "techkeyco_ussd";
protected $amount, $type;

function __construct($amount,$type)
{
    $this->amount=$amount;
    $this->type=$type;
    
}

function getAmout(){
  return  $this->amount;
}
function getType(){
    return  $this->type;
}

 public function sendMoney( $pdo,$uid, $receiverId, $senderBalance,$receiverBalance ){
     $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,FALSE);
     							

     try {
 //return "Data \n Amout sent is :".$this->getAmout()." \n Type is :".$this->gettype()."\n Uid is :$uid \n receiverid is :$receiverId \n senderbalance is :$senderBalance \n receiverbalance is :$receiverBalance \n ";

        
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO $this->dbName.transaction (amount,uid,receiverid,type) VALUES (:amount, :uid, :receiverid,:type)"); //stmt = statement
        $stmt->bindParam(':amount', $this->getAmout());
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':receiverid', $receiverId);
        $stmt->bindParam(':type', $this->getType());
        //$stmt->bindParam(':registeredOn', $email);
        $stmt->execute();

        $stmt = $pdo->prepare("UPDATE  $this->dbName.user SET balance =:balance WHERE uid = $uid"); //stmt = statement
        $stmt->bindParam(':balance', $senderBalance);
        $stmt->execute();

        $stmt = $pdo->prepare("UPDATE  $this->dbName.user SET balance =:balance WHERE uid = $receiverId"); //stmt = statement
        $stmt->bindParam(':balance', $receiverBalance);
        $stmt->execute();

        $pdo->commit();

        return "END Please wait as we process Your Request\nYou will receive a confirmation message";
        

     } catch (Exception $e) {
         $pdo->rollBack;
         return " END Error occured".$e->getMessage();
         //throw $th;
     }

  }

}

?>