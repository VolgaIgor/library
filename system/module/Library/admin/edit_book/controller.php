<?php

class AdminEditBook extends Admin {

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
        
        $data['title'] = 'Редактирование книги — админпанель ' . PROJECT_NAME;
        
        if ( $this->model->load( 'data' ) ) {
            $data['authors_list'] = $this->model->data->getAllAuthors();
            $data['publishers_list'] = $this->model->data->getAllPublishers();
            $data['categories_list'] = $this->model->data->getAllCategories();
        }
        
        $data['book'] = array();
        $data['book']['id'] = $book->getID();
        $data['book']['name'] = $book->getName();
        $data['book']['desc'] = $book->getDescription();
        $data['book']['isbn'] = $book->getISBN();
        $data['book']['year'] = $book->getYear();
        $data['book']['category'] = $book->getCategoryID();
        $data['book']['publisher'] = $book->getPublisherID();
        $data['book']['authors'] = $book->getAuthorIDList();
        
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
            empty( $this->env->request->post['name'] ) ||
            empty( $this->env->request->post['year'] ) ||
            !is_numeric( $this->env->request->post['year'] ) ||
            empty( $this->env->request->post['isbn'] ) ||
            !is_numeric( $this->env->request->post['isbn'] ) ||
            empty( $this->env->request->post['category'] ) ||
            !is_numeric( $this->env->request->post['category'] )
        ) {
            return;
        }
        
        $data['book']->setName( $this->env->request->post['name'] );
        $data['book']->setYear( (int)$this->env->request->post['year'] );
        $data['book']->setISBN( (int)$this->env->request->post['isbn'] );
        
        if ( isset( $this->env->request->post['desc'] ) ) {
            $data['book']->setDescription( $this->env->request->post['desc'] );
        }
        
        if ( 
            isset( $this->env->request->post['publisher'] ) &&
            is_numeric( $this->env->request->post['publisher'] )
        ) {
            $publisher = Publisher::newFromID( $this->env->request->post['publisher'] );
        } else {
            $publisher = null;
        }
        $data['book']->setPublisher( $publisher );
        
        $category = Category::newFromID( $this->env->request->post['category'] );
        if ( $category !== null ) {
            $data['book']->setCategory( $category );
        }
        
        $data['book']->deleteAllAuthors();
        if ( 
            !empty( $this->env->request->post['authors'] ) &&
            is_array( $this->env->request->post['authors'] )       
        ) {
            foreach ( $this->env->request->post['authors'] as $author_id ) {
                $author = Author::newFromID( $author_id );
                if ( $author !== null ) {
                    $data['book']->addAuthor( $author );
                }
            }
        }
        
        return;
    }
    
}