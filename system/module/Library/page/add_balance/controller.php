<?php

class ControllerAddBalance extends Controller {

    public function run() {
        
        if ( !$this->env->auth->isAuth() ) {
            $this->env->response->redirect( '/' );
        }
        
        $user = $this->env->auth->getUser();
        
        $data = array();
        $data['user'] = $user;
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Внести платёж — ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
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
        if ( $sum <= 0 ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Введите корректную сумму'
            ];
            
            return;
        }
        
        if ( $this->model->load( 'add' ) ) {
            if ( $this->model->add->add( $data['user'], $sum ) ) {
                $this->env->response->redirect( '/user/' . $data['user']->getId() );
            } else {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Ошибка 2'
                ];

            return;
            }
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