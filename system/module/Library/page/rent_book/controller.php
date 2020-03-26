<?php

class ControllerRentBook extends Controller {

    public function run() {
        
        if ( !$this->env->auth->isAuth() ) {
            $this->env->response->redirect( '/login' );
        }
        
        $user = $this->env->auth->getUser();
        
        if ( empty( $this->env->context['matches'][1] ) ) {
            $this->env->response->redirect( '/bookList' );
        }
        
        $book = Book::newFromId( $this->env->context['matches'][1] );
        if ( $book === null ) {
            $this->env->response->err404();
        }
        
        $data = array();
        $data['user'] = $user;
        $data['book'] = $book;
        $data['category'] = $book->getCategory();
        
        if ( !$user->getPermissions()->checkPermission( 'can_lease' ) ) {
            $data['disabled'] = true;
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'У вас нет прав для аренды книг'
            ];
        }
        
        if ( $this->model->load( 'check' ) ) {
            if ( !$this->model->check->haveBookCopy( $book ) ) {
                $data['disabled'] = true;
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Свободных копий данной книги в библиотеке нет'
                ];
            }
            
            if ( $this->model->check->haveDuplicateRented( $user, $book ) ) {
                $data['disabled'] = true;
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Вы не можете арендовать эту книгу. У вас на руках уже есть такая же.'
                ];
            }
            
            if ( $this->model->check->haveRentDelinquency( $user ) ) {
                $data['disabled'] = true;
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Вы не можете арендовать книги. У вас есть просроченная и не сданная книга'
                ];
            }
            
            if ( $this->model->check->haveNegativeBalance( $user ) ) {
                $data['disabled'] = true;
                $data['msg'][] = [
                    'type' => 'error',
                    'text' => 'Вы не можете арендовать книги. У вас отрицательный баланс на счёте'
                ];
            }
        }
        
        if ( 
            !empty( $this->env->request->post['csrf_token'] ) &&
            empty( $data['disabled'] )
        ) {
            $this->saveRequest( $data );
        }
        
        $data['csrf'] = $this->env->auth->getCSRF();
        
        $data['title'] = 'Арендовать книгу — ' . PROJECT_NAME;
        $data['content'] = $this->view->render( 'view', $data );
        
        $this->env->response->setOutput( $this->env->theme->render( 'main', $data ) );
        
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
        
        if ( !$this->model->load( 'rent' ) ) {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка 1'
            ];
            
            return;
        }
        
        if ( $this->model->rent->rent( $data['user'], $data['book'] ) ) {
            $this->env->response->redirect( '/user/' . $data['user']->getId() );
        } else {
            $data['msg'][] = [
                'type' => 'error',
                'text' => 'Ошибка аренды'
            ];
            
            return;
        }
        
        return;
    }
    
}