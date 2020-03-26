<?php

class AdminLeaseBookCopy extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $book = Book::newFromId( $this->env->context['matches'][1] );
        if ( $book === null ) {
            $this->env->response->err404();
        }
        
        if ( !$this->model->load( 'rent' ) ) {
            $this->env->response->err503();
        }
        
        $data = array();
        $data['book'] = $book;
        $data['category'] = $book->getCategory();
        
        if ( $this->model->load( 'check' ) ) {
            if ( !$this->model->check->haveBookCopy( $book ) ) {
                $data['disabled'] = true;
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Свободных копий данной книги в библиотеке нет'
                ];
            }
        }
        
        $data['reader_list'] = $this->model->rent->getReaderList( $book );
        
        if ( 
            !empty( $this->env->request->post['csrf_token'] ) &&
            empty( $data['disabled'] )
        ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Выдать читателю книгу — админпанель ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
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
        
        if ( 
            empty( $this->env->request->post['user'] ) ||
            !is_numeric( $this->env->request->post['user'] )
        ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Выберите пользователя кому выдать книгу'
            ];
            
            return;
        }
        
        $user = User::newFromId( $this->env->request->post['user'] );
        if ( $user === null ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Пользователя не существует'
            ];
            
            return;
        }
        
        if ( !$user->getPermissions()->checkPermission( 'can_lease' ) ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'У пользователя нет прав на аренду книги'
            ];
            
            return;
        }
        
        if ( $this->model->rent->rent( $user, $data['book'] ) ) {
            $this->env->response->redirect( '/user/' . $user->getId() );
        } else {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка аренды'
            ];
            
            return;
        }
        
        return;
    }
    
}