<?php

class Controller {

    public $env;
    public $view;
    public $model;
    
    public function run() {
        return true;
    }
    
    function __construct( $env ) {
        $this->env = $env;
        $this->view = new View( $this->env->context['dir'] );
        $this->model = new Model( $this->env );
    }
    
}