<?php

class ControllerAuthorList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список авторов — ' . PROJECT_NAME;
        
        $data['author_list'] = array();
        if ( $this->model->load( 'author_list' ) ) {
            $author_list = $this->model->author_list->get();
            if ( !empty( $author_list ) ) {
                foreach ($author_list as $author_item) {
                    $author = $author_item['author'];
                    
                    $array = array();
                    $array['id'] = $author->getID();
                    $array['name'] = $author->getName();
                    
                    $array['count_books'] = $author_item['count_books'];
                    
                    $data['author_list'][] = $array;
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