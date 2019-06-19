<?php

class DBConn{

    private function __construct(){}

    public static function connect(){
        $dsn = 'mysql:host=localhost;dbname=test;port=3306';
        $usr = 'root';


        try{
            $pdo = new PDO($dsn, $usr);
        } catch (PDOException $exc) {
            echo "Hiba: Sikertelen kapcsolódás. " . $exc->getMessage();
        }

        return $pdo;
    }

}