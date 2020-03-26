<?php

class AdminUserSetting extends Admin {

    public function run() {
        global $USER_RIGHT;
        
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
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Настройки пользователя — админпанель ' . PROJECT_NAME;
        
        $data['groups'] = array();
        foreach ( $USER_RIGHT as $group => $param ) {
            $data['groups'][] = $group;
        }
        $data['user_groups'] = $user->getPermissions()->getGroups();
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
        $this->env->response->output();
        
    }
    
    private function saveRequest( &$data ) {
        global $USER_RIGHT;
        
        $csrf = $this->env->request->post['csrf_token'];
        if ( !$this->env->auth->checkCSRF( $csrf ) ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка аутентификации'
            ];
            
            return;
        }
        
        if ( 
            !empty( $this->env->request->post['general'] )
        ) {
            if ( isset( $this->env->request->post['name'] ) ) {
                if ( !$data['user']->setRealName( $this->env->request->post['name'] ) ) {
                    $data['msg'][] = [
                        'type' => 'error',
                        'text' => 'Ошибка изменения имени'
                    ];

                    return;
                }
            }
            
            if ( 
                !empty( $this->env->request->post['groups'] ) &&
                is_array( $this->env->request->post['groups'] )
            ) {
                foreach ( $USER_RIGHT as $group => $param ) {
                    if ( in_array($group, $this->env->request->post['groups'] ) ) {
                        $data['user']->getPermissions()->setGroup( $group );
                    } else {
                        $data['user']->getPermissions()->deleteGroup( $group );
                    }
                }
            }
            
            $data['msg'][] = [
                'type' => 'ok',
                'text' => 'Настройки успешно изменены'
            ];

            return;
        } else if ( 
            !empty( $this->env->request->post['password'] ) &&
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