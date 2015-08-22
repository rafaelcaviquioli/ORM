<?php

class ConnectionPDO {

    static $instance;
    static $host = "localhost";
    static $db = "orm";
    static $username = "root";
    static $password = "";

    static function getConnection() {
        try {
            if (!isset(self::$instance)) {
                self::$instance = new PDO("mysql:host=" . self::$host . "; dbname=" . self::$db . ";", self::$username, self::$password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
            }
            return self::$instance;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
