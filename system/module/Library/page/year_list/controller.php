<?php

class ControllerYearList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список выпуска книг по годам — ' . PROJECT_NAME;
        
        $data['year_list'] = array();
        if ( $this->model->load( 'year_list' ) ) {
            $data['year_list'] = $this->model->year_list->get();
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