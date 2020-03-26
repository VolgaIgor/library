<?php

class ControllerAuthor extends Controller {
    
    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/' );
        }
        
        $author = Author::newFromId( $this->env->context['matches'][1] );
        if ( $author === null ) {
            $this->env->response->err404();
        }
        
        $data = array();
        $data['title'] = 'Автор ' . $author->getName() . ' — ' . PROJECT_NAME;
        
        $data['author_id'] = $author->getID();
        $data['author_name'] = $author->getName();
        $data['author_desc'] = $author->getDescription();
        
        $data['book_list'] = array();
        if ( $this->model->load( 'book_list' ) ) {
            $book_list = $this->model->book_list->get( $author->getID() );
            if ( !empty( $book_list ) ) {
                foreach ($book_list as $book_item) {
                    $book = $book_item['book'];
                    
                    $array = array();
                    $array['id'] = $book->getID();
                    $array['name'] = $book->getName();
                    $array['isbn'] = $book->getISBN();
                    $array['year'] = $book->getYear();
                    
                    $category = $book->getCategory();
                    if ( $category !== null ) {
                        $array['category_name'] = $category->getName();
                    }

                    $publisher = $book->getPublisher();
                    if ( $publisher !== null ) {
                        $array['publisher_name'] = $publisher->getName();
                    }

                    $authors = $book->getAuthorList();
                    if ( !empty( $authors ) ) {
                        $array['author_list'] = array();
                        foreach ( $authors as $author ) {
                            $array['author_list'][] =  $author->getName();
                        }
                    }
                    
                    $array['count_available'] = $book_item['available'];
                    
                    $data['book_list'][] = $array;
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