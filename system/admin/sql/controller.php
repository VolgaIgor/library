<?php

class AdminSQL extends Admin {

    public function run() {
        
        $data = array();
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Ввод SQL запроса — админпанель ' . PROJECT_NAME;
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
        
        if ( empty( $this->env->request->post['sql'] ) ) {
            return;
        }
        
        $sql = $this->env->request->post['sql'];
        
        if ( !DB::query($sql) ) {
            $errno = DB::get()->errno;
            $error = DB::get()->error;
            $data['msg'][] = [
                'type' => 'error',
                'text' => "Ошибка ({$errno}) — {$error}"
            ];
            
            return;
        } else {
            $data['msg'][] = [
                'type' => 'ok',
                'text' => 'Запрос успешно выполнен'
            ];
            
            return;
        }
        
        return;
    }
    
}