<?php

class AdminEditBookCopy extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $book_copy_id = (int)$this->env->context['matches'][1];
        $book_copy = BookCopy::newFromId( $book_copy_id );
        if ( $book_copy === null ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $data = array();
        $book = $book_copy->getBook();
        
        $data['book'] = $book;
        $data['book_copy'] = $book_copy;
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['book_id'] = $book->getID();
        $data['book_name'] = $book->getName();
        $data['book_copy_id'] = $book_copy->getID();
        $data['book_copy_place'] = $book_copy->getPlace();
        $data['book_copy_available'] = $book_copy->isAvailable();
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Редактирование копии книги — админпанель ' . PROJECT_NAME;
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
        
        if ( !empty( $this->env->request->post['place'] ) ) {
            $data['book_copy']->setPlace( (int)$this->env->request->post['place'] );
        }
        
        if ( !empty( $this->env->request->post['available'] ) ) {
            $data['book_copy']->setAvailable( true );
        } else {
            $data['book_copy']->setAvailable( false );
        }
        
        return;
    }
    
}