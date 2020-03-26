<?php
class DB {

    private static $mysql = null;
    
    public static function get() {
        if ( self::$mysql !== null ) {
            return self::$mysql; 
        }
        
        self::$mysql = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if (self::$mysql->connect_errno) {
            if ( ini_get('display_errors') == true ) {
                echo 'Ошибка подключения к базе данных: (' . self::$mysql->connect_errno . ') ' . self::$mysql->connect_error;
            }
            self::$mysql = null;
            exit();
        }
        
        return self::$mysql;
    }
    
    public static function query( $sql ) {
        $sql = str_replace('%PREFIX%', PROJECT_PREFIX, $sql);
        
        return DB::get()->query( $sql );
    }
    
    public static function insertId() {
        return DB::get()->insert_id;
    }
    
    public static function escapeString( $string ) {
        return self::$mysql->real_escape_string( $string );
    }
    
    private function __construct() {}
    
    private function __clone() {}

    private function __wakeup() {}
    
}