<?php

class ControllerMain extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Главная — ' . PROJECT_NAME;

        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
    }
    
}