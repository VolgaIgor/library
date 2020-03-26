<?php

class APILoginAvailable extends API {
    
    public function run() {
        
        $data = array();
        $data['available'] = true;
        if ( !empty( $this->env->request->get['login'] ) ) {
            if ( !User::validateLogin( $this->env->request->get['login'] ) ) {
                $data['available'] = false;
            } else if ( User::newFromLogin( $this->env->request->get['login'] ) !== null ) {
                $data['available'] = false;
            }
        }
        
        $this->env->response->setOutput( json_encode($data) );
        
        $this->env->response->output();
        
    }
    
}