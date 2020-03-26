<?php

class AdminMain extends Admin {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Главная — админпанель ' . PROJECT_NAME;

        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
        $this->env->response->output();
    }
    
}