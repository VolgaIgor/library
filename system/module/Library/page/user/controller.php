<?php

class ControllerUser extends Controller {
    
    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/' );
        }
        
        $user = User::newFromId( $this->env->context['matches'][1] );
        if ( $user === null ) {
            $this->env->response->err404();
        }
        
        $data = array();
        if ( $this->env->auth->isAuth() ) {
            $authUser = $this->env->auth->getUser();
            if ( $authUser->getId() === $user->getId() ) {
                $data['authUser'] = true;
            }
            if ( $authUser->getPermissions()->checkPermission( 'admin_login' ) ) {
                $data['admin'] = true;
            }
        }
        
        $data['title'] = 'Страница читателя — ' . PROJECT_NAME;
        
        $data['user'] = array();
        $data['user']['id'] = $user->getId();
        $data['user']['login'] = $user->getLogin();
        $data['user']['real_name'] = $user->getRealName();
        $data['user']['register'] = date( 'H:i:s d.m.Y (e)', $user->getRegistration() );
        $data['user']['blocked'] = !$user->getPermissions()->checkPermission( 'login' );
        
        if ( !empty( $data['admin'] ) ) {
            $data['user']['groups'] = implode( ', ', $user->getPermissions()->getGroups() );
        }
        
        if ( 
            !empty( $data['authUser'] ) ||
            !empty( $data['admin'] )   
        ) {
            
            if ( $this->model->load( 'leases' ) ) {
                $data['user_leases'] = $this->model->leases->getLog( $user );
            }
            
            if ( $this->model->load( 'balance' ) ) {
                $data['user_balance_log'] = $this->model->balance->getLog( $user );
                $data['user']['balance'] = $this->model->balance->getBalance( $user );
            }
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
        
    }
    
}