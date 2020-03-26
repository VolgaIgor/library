<?php

class ControllerCategory extends Controller {
    
    public function run() {
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/' );
        }
        
        $category = Category::newFromId( $this->env->context['matches'][1] );
        if ( $category === null ) {
            $this->env->response->err404();
        }
        
        $data = array();
        $data['title'] = 'Категория ' . $category->getName() . ' — ' . PROJECT_NAME;
        
        $data['category_id'] = $category->getID();
        $data['category_name'] = $category->getName();
        $data['expiration_day'] = $category->getExpirationDay();
        $data['fine_per_day'] = $category->getFinePerDay();
        
        $data['book_list'] = array();
        if ( $this->model->load( 'book_list' ) ) {
            $book_list = $this->model->book_list->get( $category->getID() );
            if ( !empty( $book_list ) ) {
                foreach ($book_list as $book_item) {
                    $book = $book_item['book'];
                    
                    $array = array();
                    $array['id'] = $book->getID();
                    $array['name'] = $book->getName();
                    $array['isbn'] = $book->getISBN();
                    $array['year'] = $book->getYear();
                    
                    $array['category_name'] = $category->getName();

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