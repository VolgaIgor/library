<?php

class AdminEditCategory extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/categoryList' );
        }
        
        $category_id = (int)$this->env->context['matches'][1];
        $category = Category::newFromId( $category_id );
        if ( $category === null ) {
            $this->env->response->redirect( '/categoryList' );
        }
        
        $data = array();     
        $data['category'] = $category;
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Редактирование категории — админпанель ' . PROJECT_NAME;
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
        
        if ( 
            empty( $this->env->request->post['name'] ) ||
            empty( $this->env->request->post['max_time'] ) ||
            !is_numeric( $this->env->request->post['max_time'] ) ||
            !isset( $this->env->request->post['fine'] )||
            !is_numeric( $this->env->request->post['fine'] )
        ) {
            return;
        }
        
        $name = $this->env->request->post['name'];
        $max_time = (int)$this->env->request->post['max_time'];
        $fine = (int)$this->env->request->post['fine'];
        
        $data['category']->setName( $name );
        $data['category']->setExpirationDay( $max_time );
        $data['category']->setFinePerDay( $fine );
        
        return;
    }
    
}