<?php

class ControllerLogin extends Controller {

    public function run() {
        
        $isJSON = ( !empty( $this->env->request->get['format'] ) && $this->env->request->get['format'] == 'json' ) ? true : false;
        
        $data = array();
        
        if ( !empty( $this->env->request->get['retpath'] ) ) {
            $retpath = parse_url( urldecode( $this->env->request->get['retpath'] ) );
            if ( 
                $retpath !== false && !empty( $retpath['host'] ) && 
                isset( SSO_TRUST_URL[ $retpath['host'] ] ) 
            ) {
                $data['retpath'] = $this->env->request->get['retpath'];
                $data['retname'] = SSO_TRUST_URL[ $retpath['host'] ]['name'];
                $data['returl'] = SSO_TRUST_URL[ $retpath['host'] ]['url'];
                /* ??? */
                $currentTarget = SSO_TRUST_URL[ $retpath['host'] ];
            } else {
                $data['retpath'] = getURL();
                $data['retname'] = current( SSO_TRUST_URL )['name'];
                $data['returl'] = current( SSO_TRUST_URL )['url'];
            }
        } else {
            $data['retpath'] = getURL();
            $data['retname'] = current( SSO_TRUST_URL )['name'];
            $data['returl'] = current( SSO_TRUST_URL )['url'];
        }
        
        if ( !empty( $this->env->request->get['backpath'] ) ) {
            $backpath = parse_url( urldecode( $this->env->request->get['backpath'] ) );
            if ( $backpath !== false && !empty( $backpath['host'] ) && isset( SSO_TRUST_URL[ $backpath['host'] ] ) ) {
                $data['backpath'] = $this->env->request->get['backpath'];
            }
        }
        
        if ( $isJSON ) {
            $this->env->response->addHeader('Content-type: application/json; charset=utf-8');
        }
        
        if ( $this->env->auth->isAuth() ) {
            if ( $isJSON ) {
                $this->env->response->setOutput( json_encode( $data ) );
                $this->env->response->output();

                exit();
            } else {
                $this->env->response->redirect( $data['retpath'], 302, true );
            }
        }
        
        
        if ( !empty( $this->env->request->post['login'] ) && !empty( $this->env->request->post['pass'] ) ) {
            $newUser = null;
            
            if ( User::validateLogin( $this->env->request->post['login'] ) ) {
                $newUser = User::newFromLogin( $this->env->request->post['login'] );
            } else {
                $data['status'] = 'err';
                $data['err'] = 'Логин или пароль введены неверно';
                $data['login'] = htmlentities( $this->env->request->post['login'] );
            }
            
            if ( $newUser === null ) {
                $data['status'] = 'err';
                $data['err'] = 'Логин или пароль введены неверно';
                $data['login'] = htmlentities( $this->env->request->post['login'] );
            } else {
                if ( !$newUser->checkPass( $this->env->request->post['pass'] ) ) {
                    $data['status'] = 'err';
                    $data['err'] = 'Логин или пароль введены неверно';
                    $data['login'] = htmlentities( $this->env->request->post['login'] );
                } else if ( !$newUser->getPermissions()->checkPermission( 'login' ) ) {
                    $data['status'] = 'err';
                    $data['err'] = 'Данный аккаунт заблокирован';
                    $data['login'] = htmlentities( $this->env->request->post['login'] );
                } else {
                    if ( $this->env->auth->login( $newUser ) ) {
                        $data['status'] = 'ok';

                        if ( !$isJSON ) {
                            $this->env->response->redirect( $data['retpath'], 302, true );
                        }
                    } else {
                        /* Непонятность */
                        $data['status'] = 'err';
                        $data['err'] = 'Неизвесная ошибка (2)';
                        $data['login'] = htmlentities( $this->env->request->post['login'] );
                    }
                }
            }
        }
        
        $data['title'] = 'Вход — ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        if ( $isJSON ) {
            $this->env->response->setOutput( json_encode( $data ) );
        } else {
            $this->env->response->setOutput( $this->env->theme->render( 'login', $data ) );
        }
        
        $this->env->response->output();
    }
    
}