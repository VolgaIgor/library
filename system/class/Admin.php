<?php

class Admin {
    public $env;
    public $view;
    public $model;
    
    public function run() {
        return true;
    }
    
    function __construct( $env ) {
        $this->env = $env;
        $this->env->response->addHeader('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        
        if (
            !$this->env->auth->isAuth() ||
            !$this->env->auth->getUser()->getPermissions()->checkPermission( 'admin_login' )
        ) {
            $this->env->response->err404();
        }
        
        $this->view = new View( $this->env->context['dir'] );
        $this->model = new Model( $this->env );
    }
}