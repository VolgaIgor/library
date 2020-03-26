<?php

class ControllerSearch extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Поиск — ' . PROJECT_NAME;
        
        if ( !empty( $this->env->request->get['q'] ) ) {
            $query = $this->env->request->get['q'];
        } else {
            $query = '';
        }
        $data['query'] = $query;
        
        if ( !empty( $query ) ) {
            if ( !$this->model->load( 'patterns' ) ) {
                $this->env->response->err503();
            }

            $books_by_name = $this->model->patterns->findBookByName( $query );
            if ( !empty( $books_by_name ) ) {
                $data['books_by_name'] = array();
                foreach ($books_by_name as $book_item) {
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

                    $array['count_available'] = $book_item['count_books'];

                    $data['books_by_name'][] = $array;
                }
            }

            if ( preg_match('/^[0-9]{1,4}$/', $query) ) {
                $books_by_year = $this->model->patterns->findByYear( (int)$query );

                if ( !empty( $books_by_year ) ) {
                    $data['books_by_year'] = array();
                    foreach ($books_by_year as $book_item) {
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

                        $array['count_available'] = $book_item['count_books'];

                        $data['books_by_year'][] = $array;
                    }
                }
            }

            $find_author = $this->model->patterns->findAuthorsByName( $query );
            if ( !empty( $find_author ) ) {
                $data['author_list'] = array();
                foreach ($find_author as $author_item) {
                    $author = $author_item['author'];

                    $array = array();
                    $array['id'] = $author->getID();
                    $array['name'] = $author->getName();

                    $array['count_books'] = $author_item['count_books'];

                    $data['author_list'][] = $array;
                }
            }

            $find_publisher = $this->model->patterns->findPublishersByName( $query );
            if ( !empty( $find_publisher ) ) {
                $data['publisher_list'] = array();
                foreach ($find_publisher as $publisher_item) {
                    $publisher = $publisher_item['publisher'];

                    $array = array();
                    $array['id'] = $publisher->getID();
                    $array['name'] = $publisher->getName();

                    $array['count_books'] = $publisher_item['count_books'];

                    $data['publisher_list'][] = $array;
                }
            }
        }
        
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
        $this->env->response->output();
        
    }
    
}