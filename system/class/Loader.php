<?php

class Loader {
    
    private static $classList = array();
    private static $moduleList = array();

    public static function loadModule( $name ) {
        $src_setup = SYSTEM_DIR . '/module/' . $name . '/setup.php';
        
        if ( !file_exists( $src_setup ) ) {
            return false;
        }
        
        $module_function = 'Module' . $name . '::load';
        $src_module = '/module/' . $name;
        
        include_once $src_setup;
        
        if ( !is_callable( $module_function ) ) {
            return false;
        }
        
        $module_function( $src_module );
        
        static::$moduleList[] = $name;
        
        return true;
        
    }
    
    public static function isLoadedModule( $name ) {
        return in_array($name, static::$moduleList);
    }
    
    public static function loadClass( $name, $path ) {
        if ( file_exists( SYSTEM_DIR . $path ) ) {
            static::$classList[$name] = $path;
            
            return true;
        }
        
        ///
        
        return false;
    }
    
    public static function getClassPath( $name ) {
        if ( isset( static::$classList[$name] ) ) {
            return static::$classList[$name];
        }
        
        return null;
    }
    
}