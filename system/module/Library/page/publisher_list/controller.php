<?php

class ControllerPublisherList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список издателей — ' . PROJECT_NAME;
        
        $data['publisher_list'] = array();
        if ( $this->model->load( 'publisher_list' ) ) {
            $publisher_list = $this->model->publisher_list->get();
            if ( !empty( $publisher_list ) ) {
                foreach ($publisher_list as $publisher_item) {
                    $publisher = $publisher_item['publisher'];
                    
                    $array = array();
                    $array['id'] = $publisher->getID();
                    $array['name'] = $publisher->getName();
                    
                    $array['count_books'] = $publisher_item['count_books'];
                    
                    $data['publisher_list'][] = $array;
                }
            }
        }
        
        if ( $this->env->auth->isAuth() ) {
            $user = $this->env->auth->getUser();
            if ( $user->getPermissions()->checkPermission( 'admin_login' ) ) {
                $data['admin'] = true;
            }
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
        
    }
    
}