<?php

class ModuleSSO {
    
    public static function load( $src ) {
        
        Loader::loadClass( 'SSOAuthServer', $src . '/class/SSOAuthServer.php' );
        
        URL::addPage('/logout', $src . '/page/logout');
        URL::addPage('/login', $src . '/page/login');
        URL::addPage('/register', $src . '/page/register');
        
        URL::addAPI('/loginAvailable', $src . '/api/login_available');
        
    }
    
}
    