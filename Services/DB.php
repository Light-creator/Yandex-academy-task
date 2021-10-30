<?php

namespace Services;

use \PDO;

class DB_PDO {
    
    public static $pdo;

    public static function connection() {
        $config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/config.json'), true);
        
        try{
            self::$pdo = new PDO('mysql:host='. $config['db']['host'] .';dbname='. $config['db']['db_name'] .'', $config['db']['username'], $config['db']['password'], array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
        } catch(PDOExeception $e) {
            return $e->getMessage();
        }
    }

    public static function close() {
        self::$pdo = null;
    }
}