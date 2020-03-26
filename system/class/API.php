<?php

class API {
    public $env;
    public $model;
    
    public function run() {
        return true;
    }
    
    function __construct( $env ) {
        $this->env = $env;
        $this->env->response->addHeader('Content-type: application/json; charset=utf-8');
        
        $this->model = new Model( $this->env );
    }
}