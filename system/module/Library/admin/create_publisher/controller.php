<?php

class AdminCreatePublisher extends Admin {

    public function run() {
        
        $data = array();
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Добавление издателя — админпанель ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
        $this->env->response->output();
        
    }
    
    private function saveRequest( &$data ) {
        $csrf = $this->env->request->post['csrf_token'];
        if ( !$this->env->auth->checkCSRF( $csrf ) ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка аутентификации'
            ];
            
            return;
        }
        
        if ( empty( $this->env->request->post['name'] ) ) {
            return;
        }
        
        $name = $this->env->request->post['name'];
        if ( !empty( $this->env->request->post['desc'] ) ) {
            $desc = $this->env->request->post['desc'];
        } else {
            $desc = '';
        }
        
        $publisher = Publisher::create($name, $desc);
        if ( $publisher !== false ) {
            $this->env->response->redirect( '/publisher/' . $publisher->getID() );
        }
        
        return;
    }
    
}