<?php

class URL {

    private static $PATH = array();
    private static $PATH_REGEX = array();
    const TYPE_PAGE = 1;
    const TYPE_API = 2;
    const TYPE_ADMIN = 3;
    
    public static function getController( $url ) {
        
        if ( isset( static::$PATH[ $url ] ) ) {
            return static::$PATH[ $url ];
        }
        
        foreach (static::$PATH_REGEX as $key => $value) {
            $preg = str_replace('/', '\/', $key);
            $result = preg_match( '/^' . $preg . '$/u' , $url, $matches );
            if ( $result === 1 ) {
                $value['matches'] = $matches;
                return $value;
            }
        }
        
        return null;
    }
    
    public static function addPage( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'Controller' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH[$url] = array(
                'type' => URL::TYPE_PAGE,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
    
    public static function addPageRegex( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'Controller' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH_REGEX[$url] = array(
                'type' => URL::TYPE_PAGE,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
    
    public static function addAPI( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'API' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH[ '/api' .  $url ] = array(
                'type' => URL::TYPE_API,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
    
    public static function addAPIRegex( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'API' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH_REGEX[ '/api' .  $url ] = array(
                'type' => URL::TYPE_API,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
    
    public static function addAdmin( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'Admin' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH[ '/admin' .  $url ] = array(
                'type' => URL::TYPE_ADMIN,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
    
    public static function addAdminRegex( $url, $dir, $target = null ) {
        $controller = explode('/', $dir);
        $controller = array_pop( $controller );
        $controller = explode('_', $controller);
        foreach ( $controller as &$value ) {
            $value = ucfirst( $value );
        }
        $controller = 'Admin' . implode( $controller );
        
        if ( Loader::loadClass( $controller, $dir . '/controller.php' ) ) {
            static::$PATH_REGEX[ '/admin' .  $url ] = array(
                'type' => URL::TYPE_ADMIN,
                'class' => $controller,
                'dir' => $dir,
                'target' => $target
            );
            
            return true;
        }
        
        return false;
    }
}