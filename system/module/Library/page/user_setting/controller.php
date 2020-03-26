<?php

class ControllerUserSetting extends Controller {

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
        
        $data['title'] = 'Настройки пользователя — ' . PROJECT_NAME;
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
            !empty( $this->env->request->post['general'] ) &&
            isset( $this->env->request->post['name'] )
        ) {
            if ( $data['user']->setRealName( $this->env->request->post['name'] ) ) {
                $data['msg'][] = [
                    'type' => 'ok',
                    'text' => 'Настройки успешно изменены'
                ];

                return;
            } else {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Ошибка изменения настроек'
                ];

                return;
            }
        } else if ( 
            !empty( $this->env->request->post['password'] ) &&
            !empty( $this->env->request->post['old_pass'] ) &&
            !empty( $this->env->request->post['new_pass'] ) &&
            !empty( $this->env->request->post['new_pass_2'] )
        ) {
            if ( $this->env->request->post['new_pass'] !== $this->env->request->post['new_pass_2'] ) {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Новые пароли не совпадают'
                ];

                return;
            }
            
            if ( !$data['user']->checkPass( $this->env->request->post['old_pass'] ) ) {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Старый пароль неверен'
                ];

                return;
            }
            
            if ( $data['user']->setPass( $this->env->request->post['new_pass'] ) ) {
                $data['msg'][] = [
                    'type' => 'ok',
                    'text' => 'Пароль успешно изменён'
                ];

                return;
            } else {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Ошибка изменения пароля'
                ];

                return;
            }
        }
        
        return;
    }
    
}