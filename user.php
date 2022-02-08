<?php

define("db_Name","techkeyco_ussd");




class currentUser{
    protected $dbName ="techkeyco_ussd";

    protected $name;
    protected $phone;
    protected $pin;
    protected $balance;

     function __construct($phone)
    {
        $this->phone =$phone;
    }
    public function getphone()
    {
       return $this->phone;
    }

    public function setname($name)
    {
        $this->name =$name;
    }
    public function getname()
    {
       return $this->name;
    }

    public function setpin($pin)
    {
        $this->pin =$pin;
    }
    public function getpin()
    {
       return $this->pin;
    }

    public function setbalance($balance)
    {
        $this->balance =$balance;
    }
    public function getbalance()
    {
       return $this->balance;
    }

    public function register($pdo){
        try{
            $hashedpin = password_hash($this->getpin(),PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO $this->dbName.user (name,pin,phone,balance) VALUES (:name, :pin, :phone,:balance)"); //stmt = statement
            $stmt->bindParam(':name', $this->getname());
            $stmt->bindParam(':pin', $hashedpin);
            $stmt->bindParam(':phone', $this->getphone());
            $stmt->bindParam(':balance', $this->getbalance());
            //$stmt->bindParam(':registeredOn', $email);

            $stmt->execute();
            
            echo "END Successfully Registered";
        }
        catch(PDOException $e){
            echo "END ".$e->getMessage();

        }

    }

    public function isUserRegister($pdo){
        try{
            $userPhone = $this->getphone();
       //stmt = statement
        $stmt = $pdo->prepare("SELECT * FROM  $this->dbName.user WHERE phone =  $userPhone");
        $stmt->execute();

        if( count( $stmt->fetchAll() ) > 0 ){
            return true;
        }
        else{
            return false;
        }
       }
       catch(PDOException $e) {
        echo "END Error: Invalid Input ";
      }
        
    }

    public function readName($pdo){
        $phone= $this->getphone();
        $stmt = $pdo->prepare("SELECT * FROM  $this->dbName.user WHERE phone = $phone"); //stmt = statement
        $stmt->execute();
        $row= $stmt->fetch();

        return $row['name'];

        
    }

    public function readUserId($pdo){
        $phone= $this->getphone();
        $stmt = $pdo->prepare("SELECT * FROM  $this->dbName.user WHERE phone = $phone"); //stmt = statement
        $stmt->execute();
        $row= $stmt->fetch();

        return $row['uid'];
        
    }

    public function correctPin($pdo,$phone){

        try{
            $phone= $this->getphone();

            $stmt = $pdo->prepare("SELECT * FROM  $this->dbName.user WHERE phone = phone"); //stmt = statement
            $stmt->execute();
            $row= $stmt->fetch();
    
            return $row['pin'];
       }
       catch(PDOException $e) {
        echo "END Error: Invalid Input ";
      }
      
      
    }

    public function checkBalance($pdo){

        try{

            $phone= $this->getphone();
        $stmt = $pdo->prepare("SELECT * FROM  $this->dbName.user WHERE phone = $phone"); //stmt = statement
            $stmt->execute();
            $row= $stmt->fetch();
    
            return $row['balance'];

       }
       catch(PDOException $e) {
        echo "END Error: Invalid Input ";
      }
        
    }

   

}



?>