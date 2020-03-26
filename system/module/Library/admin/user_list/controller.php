<?php

class AdminUserList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список пользователей — админпанель ' . PROJECT_NAME;
        
        $data['user_list'] = array();
        if ( $this->model->load( 'user_list' ) ) {
            $user_list = $this->model->user_list->get();
            if ( !empty( $user_list ) ) {
                foreach ($user_list as $user) {
                    $array = array();
                    $array['id'] = $user->getId();
                    $array['login'] = $user->getLogin();
                    $array['real_name'] = $user->getRealName();
                    $array['register'] = date( 'H:i:s d.m.Y (e)', $user->getRegistration() );
                    
                    $data['user_list'][] = $array;
                }
            }
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
        $this->env->response->output();
        
    }
    
}