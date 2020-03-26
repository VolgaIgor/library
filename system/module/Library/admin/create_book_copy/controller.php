<?php

class AdminCreateBookCopy extends Admin {

    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $book_id = (int)$this->env->context['matches'][1];
        $book = Book::newFromId( $book_id );
        if ( $book === null ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $data = array();
        $data['book'] = $book;
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Добавление копии книги — админпанель ' . PROJECT_NAME;
        
        $data['book_id'] = $book->getID();
        $data['book_name'] = $book->getName();
        
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
            empty( $this->env->request->post['place'] ) ||
            !is_numeric( $this->env->request->post['place'] )
        ) {
            return;
        }
        
        $place = (int)$this->env->request->post['place'];
        $available = ( !empty($this->env->request->post['available']) ) ? true : false;
        
        $newBook = BookCopy::create( $data['book'], $place, $available );
        if ( $newBook !== false ) {
            $this->env->response->redirect( '/admin/bookCopy/' . $newBook->getID() );
        } else {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка создания, повторите попытку'
            ];
            
            return;
        }
        
        return;
    }
    
}