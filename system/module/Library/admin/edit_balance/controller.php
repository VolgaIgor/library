<?php

class AdminEditBalance extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/userList' );
        }
        
        $user_id = (int)$this->env->context['matches'][1];
        $user = User::newFromId( $user_id );
        if ( $user === null ) {
            $this->env->response->redirect( '/userList' );
        }
        
        $data = array();
        $data['user'] = $user;
        
        if ( !$this->model->load( 'balance' ) ) {
            $this->env->response->err500();
        }
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['user_balance'] = $this->model->balance->getBalance( $user );
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Изменить баланс пользователя — ' . PROJECT_NAME;
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
            empty( $this->env->request->post['sum'] ) ||
            !is_numeric( $this->env->request->post['sum'] )
        ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Введите корректную сумму'
            ];
            
            return;
        }
        
        $sum = (int)$this->env->request->post['sum'];
        $diff = $sum - $this->model->balance->getBalance( $data['user'] );
        
        if ( $this->model->balance->setDiff( $data['user'], $diff ) ) {
            $data['msg'][] = [
                'type' => 'ok',
                'text' => 'Сумма успешно изменена'
            ];
            
            return;
        } else {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка 1'
            ];
            
            return;
        }
        
        return;
    }
    
}