<?php

class AdminDeletePublisher extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/publisherList' );
        }
        
        $publisher_id = (int)$this->env->context['matches'][1];
        $publisher = Publisher::newFromId( $publisher_id );
        if ( $publisher === null ) {
            $this->env->response->redirect( '/publisherList' );
        }
        
        $data = array();
        $data['publisher'] = $publisher;
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Удаление издателя — админпанель ' . PROJECT_NAME;
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
        
        $data['publisher']->delete();
        
        $this->env->response->redirect( '/publisherList' );
        
        return;
    }
    
}