<?php

class ControllerCategoryList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список категорий — ' . PROJECT_NAME;
        
        $data['category_list'] = array();
        if ( $this->model->load( 'category_list' ) ) {
            $category_list = $this->model->category_list->get();
            if ( !empty( $category_list ) ) {
                foreach ($category_list as $category_item) {
                    $category = $category_item['category'];
                    
                    $array = array();
                    $array['id'] = $category->getID();
                    $array['name'] = $category->getName();
                    
                    $array['count_books'] = $category_item['count_books'];
                    
                    $data['category_list'][] = $array;
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