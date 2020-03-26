<?php

class ControllerBookInfo extends Controller {
    
    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/' );
        }
        
        $book = Book::newFromId( $this->env->context['matches'][1] );
        if ( $book === null ) {
            $this->env->response->err404();
        }
        
        $data = array();
        $data['title'] = 'Книга ' . $book->getName() . ' — ' . PROJECT_NAME;
        
        $data['book'] = array();
        $data['book']['id'] = $book->getID();
        $data['book']['name'] = $book->getName();
        $data['book']['desc'] = $book->getDescription();
        $data['book']['isbn'] = $book->getISBN();
        $data['book']['year'] = $book->getYear();
        
        $category = $book->getCategory();
        if ( $category !== null ) {
            $data['book']['category_name'] = $category->getName();
            $data['book']['category_id'] = $category->getID();
        }
        
        $publisher = $book->getPublisher();
        if ( $publisher !== null ) {
            $data['book']['publisher_name'] = $publisher->getName();
            $data['book']['publisher_id'] = $publisher->getID();
        }
        
        $authors = $book->getAuthorList();
        if ( !empty( $authors ) ) {
            $data['book']['authors'] = array();
            foreach ( $authors as $author ) {
                $data['book']['authors'][] = array(
                    'name' => $author->getName(),
                    'id' => $author->getID()
                );
            }
        }
        
        if ( $this->model->load( 'book_copy_list' ) ) {
            $data['book']['count_available'] = $this->model->book_copy_list->getCountAvailable( $book );
        }
        
        if ( $this->env->auth->isAuth() ) {
            $user = $this->env->auth->getUser();
            if ( $user->getPermissions()->checkPermission( 'admin_login' ) ) {
                $data['admin'] = true;
            }
        }
        
        if ( !empty( $data['admin'] ) ) {
            if ( $this->model->load( 'book_copy_list' ) ) {
                $book_copy_list = $this->model->book_copy_list->getList( $book );
                if ( !empty( $book_copy_list ) ) {
                    $data['book']['copies'] = array();
                    foreach ( $book_copy_list as $book_copy ) {
                        $data['book']['copies'][] = array(
                            'id' => $book_copy->getID(),
                            'place' => $book_copy->getPlace(),
                            'available' => $book_copy->isAvailable()
                        );
                    }
                }
            }
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
        
    }
    
}