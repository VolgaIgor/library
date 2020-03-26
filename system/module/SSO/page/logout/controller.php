<?php

class ControllerLogout extends Controller {

    public function run() {
        
        $this->env->auth->logout();
        $this->env->response->redirect( '/login' );
        
    }
    
}