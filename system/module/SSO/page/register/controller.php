<?php

class ControllerRegister extends Controller {

    public function run() {
        
        $isJSON = ( !empty( $this->env->request->get['format'] ) && $this->env->request->get['format'] == 'json' ) ? true : false;
        
        $data = array();
        
        if ( !empty( $this->env->request->get['retpath'] ) ) {
            $retpath = parse_url( urldecode( $this->env->request->get['retpath'] ) );
            if ( $retpath !== false && !empty( $retpath['host'] ) && isset( SSO_TRUST_URL[ $retpath['host'] ] ) ) {
                $data['retpath'] = $this->env->request->get['retpath'];
            } else {
                $data['retpath'] = getURL();
            }
        } else {
            $data['retpath'] = getURL();
        }
        
        if ( $isJSON ) {
            // JSON //
            $this->env->response->addHeader('Content-type: application/json; charset=utf-8');
        
            if ( $this->env->auth->isAuth() ) {
                $data['status'] = 'auth';

                $this->env->response->setOutput( json_encode( $data ) );

                $this->env->response->output();

                exit();
            }
        } else {
            // HTML //
            if ( $this->env->auth->isAuth() ) {
                $this->env->response->redirect( $data['retpath'], 302, true );
            }
        }
        
        if ( !empty( $this->env->request->get['backpath'] ) ) {
            $backpath = parse_url( urldecode( $this->env->request->get['backpath'] ) );
            if ( $backpath !== false && !empty( $backpath['host'] ) && isset( SSO_TRUST_URL[ $backpath['host'] ] ) ) {
                $data['backpath'] = $this->env->request->get['backpath'];
            }
        }
        
        if ( 
            !empty( $this->env->request->post['login'] ) && 
            !empty( $this->env->request->post['pass1'] ) && 
            !empty( $this->env->request->post['pass2'] ) 
        ) {
            if ( !User::validateLogin( $this->env->request->post['login'] ) ) {
                $data['status'] = 'err';
                $data['err'] = 'Логин может состоять только из латинских букв, цифр, а также символов _ и -';
            } else {
                $checkLogin = User::newFromLogin( $this->env->request->post['login'] );
                if ( $checkLogin ) {
                    $data['status'] = 'err';
                    $data['err'] = 'Логин занят. <a href="/recover">Восстановить пароль?</a>';
                } else {
                    $data['login'] = htmlentities( $this->env->request->post['login'] );
                }
            }
            
            if ( $this->env->request->post['pass1'] !== $this->env->request->post['pass2'] ) {
                $data['status'] = 'err';
                $data['err'] = 'Пароли не совпадают';
            }
            
            if ( empty( $data['err'] ) ) {
                $userData = [
                    'pass' => $this->env->request->post['pass1']
                ];
                
                $user = User::create( $this->env->request->post['login'], $userData );
                if ( $user !== false ) {
                    
                    if ( $this->model->load( 'create_table' ) ) {
                        $this->model->create_table->create( $user );
                    }
                    
                    if ( $this->env->auth->login( $user ) ) {
                        $data['status'] = 'ok';
                        
                        $data['retpath'] = getURL();
                    } else {
                        $data['status'] = 'err';
                        $data['err'] = 'Неизвестная ошибка (2)';
                    }
                } else {
                    $data['status'] = 'err';
                    $data['err'] = 'Неизвестная ошибка (1)';
                }
                
            }
        }
        
        $data['title'] = 'Регистрация — ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        if ( $isJSON ) {
            $this->env->response->setOutput( json_encode( $data ) );
        } else {
            $this->env->response->setOutput( $this->env->theme->render( 'login', $data ) );
        }
        
        $this->env->response->output();
    }
    
}