<?php

class ControllerError extends Controller {

    public function run() {
        
        $data = array();
        
        switch ( $this->env->context['target'] ) {
            case 404:
                $data['title'] = 'Страница не найдена — ' . PROJECT_NAME;
                $data['content'] = $this->view->render( '404', $data );
                break;
            case 500:
                $data['title'] = 'Внутренняя ошибка сервера — ' . PROJECT_NAME;
                $data['content'] = $this->view->render( '500', $data );
                break;
            default:
                $this->env->response->err404();
                break;
        }
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
    }
    
}