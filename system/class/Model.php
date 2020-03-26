<?php

class Model {
    private $env = null;
    private $models = array();
    
    public function load( $name ) {
        if ( isset( $this->models[$name] ) ) {
            return true;
        }
        
        $path = SYSTEM_DIR . $this->env->context['dir'] . '/model/' . $name . '.php';
        if ( file_exists( $path ) ) {
            
            include_once $path;
            
            $model_class = explode('_', $name);
            foreach ( $model_class as &$value ) {
                $value = ucfirst( $value );
            }
            
            if ( strpos( $this->env->context['class'], 'Controller' ) === 0 ) {
                $base_class = substr( $this->env->context['class'], 10 );
            } else if ( strpos( $this->env->context['class'], 'API' ) === 0 ) {
                $base_class = substr( $this->env->context['class'], 3 );
            } else if ( strpos( $this->env->context['class'], 'Admin' ) === 0 ) {
                $base_class = substr( $this->env->context['class'], 5 );
            } else {
                $base_class = $this->env->context['class'];
            }
            
            $model_class = 'Model' . $base_class . implode( $model_class );
            
            if ( !class_exists( $model_class, false ) ) {
                /* OSHIBKA */
                return false;
            }
            
            $this->models[$name] = new $model_class( $this->env );

            return true;
            
        } else {
            return false;
        }
    }
    
    public function __get($name) {
        return ( ( isset( $this->models[$name] ) ) ? $this->models[$name] : null );
    }
    
    public function __construct( $env ) {
        $this->env = $env;
    }
}