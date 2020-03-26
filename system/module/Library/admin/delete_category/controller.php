<?php

class AdminDeleteCategory extends Admin {

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
        
        $data['title'] = 'Удаление категории — админпанель ' . PROJECT_NAME;
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
        
        $data['category']->delete();
        
        $this->env->response->redirect( '/categoryList' );
        
        return;
    }
    
}