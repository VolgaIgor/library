<?php

class AdminCreateCategory extends Admin {

    public function run() {
        
        $data = array();
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Создание категории — админпанель ' . PROJECT_NAME;
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
        
        $category = Category::create($name, $max_time, $fine);
        if ( $category !== false ) {
            $this->env->response->redirect( '/category/' . $category->getID() );
        }
        
        return;
    }
    
}