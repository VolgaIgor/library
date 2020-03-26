<?php

class Route {

    public static function start( Registry $env ) {
        $controller_info = URL::getController( '/' . $env->request->action );
        
        if ( $controller_info === null ) {
            $env->response->err404();
        }
        
        $controller_class = $controller_info['class'];
        
        if ( !class_exists( $controller_class ) ) {
            if ( ini_get('display_errors') == true ) {
                echo 'Класс ' . $env->request->action . ' не найден!';
                exit();
            } else {
                $env->response->err500();
            }

        }
        
        $env->context = $controller_info;
        
        $controller = new $controller_class( $env );
        $controller->run();
        
    }
    
}