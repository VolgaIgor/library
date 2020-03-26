<?php

class ControllerRentedInfo extends Controller {

    public function run() {
        
        if ( !$this->env->auth->isAuth() ) {
            $this->env->response->redirect( '/login' );
        }
        
        $authUser = $this->env->auth->getUser();
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        if ( !$this->model->load( 'model' ) ) {
            $this->env->response->err503();
        }
        
        $rent_info = $this->model->model->getRentInfo( (int)$this->env->context['matches'][1] );

        if ( $rent_info === null ) {
            $this->env->response->err404();
        }
        
        $data['rent_info'] = $rent_info;
        $data['user'] = User::newFromId( $data['rent_info']['client_id'] );
        $data['book_copy'] = BookCopy::newFromID( $data['rent_info']['book_list_id'] );
        $data['book'] = $data['book_copy']->getBook();
        $data['category'] = $data['book']->getCategory();
        
        if ( $authUser->getId() === $data['user']->getId() ) {
            $data['authUser'] = true;
        }
        if ( $authUser->getPermissions()->checkPermission( 'admin_login' ) ) {
            $data['admin'] = true;
        }
        
        if ( empty( $data['rent_info']['date_returned'] ) ) {
            $rent_overdue_day = (int)$this->model->model->getOverdueDay( (int)$this->env->context['matches'][1] );
            if ( $rent_overdue_day !== 0 ) {
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Аренда просрочена на ' . $rent_overdue_day . ' дней. При возврате будет спишен штраф в сумме ' . $rent_overdue_day * $data['category']->getFinePerDay()
                ];
            }
        }
        
        if ( 
            !empty( $this->env->request->post['csrf_token'] ) &&
            empty( $data['rent_info']['date_returned'] )
        ) {
            $this->saveRequest( $data );
            $data['rent_info'] = $this->model->model->getRentInfo( (int)$this->env->context['matches'][1] );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Информация об аренде — ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
        
    }
    
    private function saveRequest( &$data ) {
        $csrf = $this->env->request->post['csrf_token'];
        if ( !$this->env->auth->checkCSRF( $csrf ) ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка аутентификации'
            ];
            
            return;
        }
        
        $this->model->model->returnRent( $data['rent_info']['id'] );
        
        return;
    }
    
}