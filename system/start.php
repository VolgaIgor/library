<?php
    define( 'VERSION', '1.0.0' );
    
    spl_autoload_register( function ($class_name) {
        $class_path = str_replace('\\', '/', $class_name);
        if (file_exists(SYSTEM_DIR . '/class/' . $class_path . '.php')) { 
            require_once SYSTEM_DIR . '/class/' . $class_path . '.php'; 
            return true; 
        } else if ( Loader::getClassPath($class_name) ) {
            require_once SYSTEM_DIR . Loader::getClassPath($class_name); 
            return true; 
        }
        return false;
    });
    
    require_once SYSTEM_DIR . '/confing.php';
    require_once SYSTEM_DIR . '/globalFunction.php';
    
    $context = new Registry();
    $context->request = new Request();
    $context->response = new Response();
    
    $context->auth = new Auth( $context );
    $context->theme = new Theme( $context );

    Route::start( $context );