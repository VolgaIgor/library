<?php

class AdminCreateBook extends Admin {

    public function run() {
        
        $data = array();
        
        if ( !empty( $this->env->request->post['csrf_token'] ) ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Добавление книги — админпанель ' . PROJECT_NAME;
        
        if ( $this->model->load( 'data' ) ) {
            $data['authors_list'] = $this->model->data->getAllAuthors();
            $data['publishers_list'] = $this->model->data->getAllPublishers();
            $data['categories_list'] = $this->model->data->getAllCategories();
        }
        
        
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
        
        $name = $this->env->request->post['name'];
        $year = (int)$this->env->request->post['year'];
        $isbn = (int)$this->env->request->post['isbn'];
        
        if ( !empty( $this->env->request->post['desc'] ) ) {
            $desc = $this->env->request->post['desc'];
        } else {
            $desc = '';
        }
        
        if ( 
            !empty( $this->env->request->post['publisher'] ) &&
            is_numeric( $this->env->request->post['publisher'] )
        ) {
            $publisher = Publisher::newFromID( $this->env->request->post['publisher'] );
        } else {
            $publisher = null;
        }
        
        $category = Category::newFromID( $this->env->request->post['category'] );
        if ( $category === null ) {
            return;
        }
        
        $authors = array();
        if ( 
            !empty( $this->env->request->post['authors'] ) &&
            is_array( $this->env->request->post['authors'] )       
        ) {
            foreach ( $this->env->request->post['authors'] as $author_id ) {
                $author = Author::newFromID( $author_id );
                if ( $author !== null ) {
                    $authors[] = $author;
                }
            }
        }
        
        $newBook = Book::create($name, $isbn, $year, $category, $desc, $publisher, $authors);
        if ( $newBook !== false ) {
            $this->env->response->redirect( '/bookInfo/' . $newBook->getID() );
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