<?php

class Hooks {
    
    private static $handlers = array();
    
    public static function register( $name, $function_name ) {
        if ( is_callable( $function_name ) ) {
            static::$handlers[$name][] = $function_name;
            
            return true;
        }
        
        return false;
    }
    
    public static function run( $name, $args = array() ) {
        /* Если завершается с неправильным кодом можно логить */
        if ( !empty( static::$handlers[$name] ) ) {
            foreach ( static::$handlers[$name] as $func ) {
                $func( $args );
            }
        }
    }
    
}