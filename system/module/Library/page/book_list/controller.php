<?php

class ControllerBookList extends Controller {
    
    public function run() {
        
        $data = array();
        $data['title'] = 'Список книг в библиотеке — ' . PROJECT_NAME;
        
        $sort = array();
        
        $sort_button_link = array();
        $sort_button_link['name_up'] = array();
        $sort_button_link['name_down'] = array();
        $sort_button_link['year_up'] = array();
        $sort_button_link['year_down'] = array();
        $sort_button_link['count_up'] = array();
        $sort_button_link['count_down'] = array();
        
        if ( !empty( $this->env->request->get['sort_name'] ) ) {
            if ( $this->env->request->get['sort_name'] == 'up' ) {
                $sort['name'] = 'up';
                
                $sort_button_link['name_down'][] = 'sort_name=down';
                
                $sort_button_link['year_up'][] = 'sort_name=up';
                $sort_button_link['year_down'][] = 'sort_name=up';
                $sort_button_link['count_up'][] = 'sort_name=up';
                $sort_button_link['count_down'][] = 'sort_name=up';
            } else if ( $this->env->request->get['sort_name'] == 'down' ) {
                $sort['name'] = 'down';
                
                $sort_button_link['name_up'][] = 'sort_name=up';
                
                $sort_button_link['year_up'][] = 'sort_name=down';
                $sort_button_link['year_down'][] = 'sort_name=down';
                $sort_button_link['count_up'][] = 'sort_name=down';
                $sort_button_link['count_down'][] = 'sort_name=down';
            } else {
                $sort_button_link['name_down'][] = 'sort_name=down';
                $sort_button_link['name_up'][] = 'sort_name=up';
            }
        } else {
            $sort_button_link['name_down'][] = 'sort_name=down';
            $sort_button_link['name_up'][] = 'sort_name=up';
        }
        
        if ( !empty( $this->env->request->get['sort_year'] ) ) {
            if ( $this->env->request->get['sort_year'] == 'up' ) {
                $sort['year'] = 'up';
                
                $sort_button_link['year_down'][] = 'sort_year=down';
                
                $sort_button_link['name_up'][] = 'sort_year=up';
                $sort_button_link['name_down'][] = 'sort_year=up';
                $sort_button_link['count_up'][] = 'sort_year=up';
                $sort_button_link['count_down'][] = 'sort_year=up';
            } else if ( $this->env->request->get['sort_year'] == 'down' ) {
                $sort['year'] = 'down';
                
                $sort_button_link['year_up'][] = 'sort_year=up';
                
                $sort_button_link['name_up'][] = 'sort_year=down';
                $sort_button_link['name_down'][] = 'sort_year=down';
                $sort_button_link['count_up'][] = 'sort_year=down';
                $sort_button_link['count_down'][] = 'sort_year=down';
            } else {
                $sort_button_link['year_down'][] = 'sort_year=down';
                $sort_button_link['year_up'][] = 'sort_year=up';
            }
        } else {
            $sort_button_link['year_down'][] = 'sort_year=down';
            $sort_button_link['year_up'][] = 'sort_year=up';
        }
        
        if ( !empty( $this->env->request->get['sort_count'] ) ) {
            if ( $this->env->request->get['sort_count'] == 'up' ) {
                $sort['count'] = 'up';
                
                $sort_button_link['count_down'][] = 'sort_count=down';
                
                $sort_button_link['name_up'][] = 'sort_count=up';
                $sort_button_link['name_down'][] = 'sort_count=up';
                $sort_button_link['year_up'][] = 'sort_count=up';
                $sort_button_link['year_down'][] = 'sort_count=up';
            } else if ( $this->env->request->get['sort_count'] == 'down' ) {
                $sort['count'] = 'down';
                
                $sort_button_link['count_up'][] = 'sort_count=up';
                
                $sort_button_link['name_up'][] = 'sort_count=down';
                $sort_button_link['name_down'][] = 'sort_count=down';
                $sort_button_link['year_up'][] = 'sort_count=down';
                $sort_button_link['year_down'][] = 'sort_count=down';
            } else {
                $sort_button_link['count_down'][] = 'sort_count=down';
                $sort_button_link['count_up'][] = 'sort_count=up';
            }
        } else {
            $sort_button_link['count_down'][] = 'sort_count=down';
            $sort_button_link['count_up'][] = 'sort_count=up';
        }
        
        $sort_button_link['name_up'] = implode('&', $sort_button_link['name_up']);
        $sort_button_link['name_down'] = implode('&', $sort_button_link['name_down']);
        $sort_button_link['year_up'] = implode('&', $sort_button_link['year_up']);
        $sort_button_link['year_down'] = implode('&', $sort_button_link['year_down']);
        $sort_button_link['count_up'] = implode('&', $sort_button_link['count_up']);
        $sort_button_link['count_down'] = implode('&', $sort_button_link['count_down']);
        
        $data['sort'] = $sort;
        $data['sort_button_link'] = $sort_button_link;
        
        $data['book_list'] = array();
        if ( $this->model->load( 'book_list' ) ) {
            $book_list = $this->model->book_list->get( $sort );
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