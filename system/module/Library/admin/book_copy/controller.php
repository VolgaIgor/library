<?php

class AdminBookCopy extends Admin {

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
        $data['book_id'] = $book->getID();
        $data['book_name'] = $book->getName();
        $data['book_copy_id'] = $book_copy->getID();
        $data['book_copy_place'] = $book_copy->getPlace();
        $data['book_copy_available'] = $book_copy->isAvailable();
        
        $data['title'] = 'Экземпляр книги — админпанель ' . PROJECT_NAME;

        if ( $this->model->load( 'leases' ) ) {
            $data['book_copy_leases'] = $this->model->leases->getLog( $book_copy->getID() );
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'admin', $data ) );
        
        $this->env->response->output();
    }
}