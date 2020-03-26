<?php

class Auth {
    
    public $session = NULL;
    private $env = NULL;
    
    public function __construct( Registry $env ) {
        $this->env = $env;
        
        if ( !empty( $env->request->cookie[SESSION_GLOBAL_NAME] ) && !empty( $env->request->cookie[SESSION_LOCAL_NAME] ) ) {
            $this->session = Session::newFromSID( $env->request->cookie[SESSION_GLOBAL_NAME], $env->request->cookie[SESSION_LOCAL_NAME] );
            if ( $this->session !== null && $this->session->continue() ) {
                $user = User::newFromId( $this->session->getUserId() );
                
                if ( $user === null || !$user->getPermissions()->checkPermission( 'login' ) ) {
                    $this->logout();
                }

            } else {
                $this->logout();
            }
        }
    }
    
    public function login( User $user, GlobalSession $global_session = null ) {
        /* Тут только пользователь задаётся */
        if ( $this->isAuth() )
            return false;
        
        $this->session = Session::create( $user->getId(), $global_session );
        if ( $this->session !== null) {
            setcookie( SESSION_GLOBAL_NAME, $this->session->getGlobalSID(), time() + SESSION_GLOBAL_MAX_LIVE, '/', MAIN_DOMAIN, USE_SSL, true);
            setcookie( SESSION_LOCAL_NAME, $this->session->getSID(), time() + SESSION_GLOBAL_MAX_LIVE, '/', MAIN_DOMAIN, USE_SSL, true);
            
            Hooks::run( 
                'UserLogin', 
                [ 'user' => $user ]
            );
            
            return true;
        } else {
            return false;
        }
        
    }
    
    public function logout() {

        if ( is_object( $this->session ) )
            $this->session->destroy();
        unset( $this->session );

        header_remove('Set-Cookie'); 
        setcookie( SESSION_GLOBAL_NAME, '', time() - 3600, '/', MAIN_DOMAIN, USE_SSL, true);
        setcookie( SESSION_LOCAL_NAME, '', time() - 3600, '/', MAIN_DOMAIN, USE_SSL, true);
    }
    
    public function isAuth() {
        if ( $this->getUser() !== null ) {
            if ( $this->getUser()->getPermissions()->checkPermission( 'login' ) ) {
                return true;
            } else {
                $this->logout();
                
                return false;
            }
        }
        
        return false;
        
    }
    
    public function getSession() {
        return $this->session;
    }
    
    public function getUser() {
        if ( empty( $this->session ) ) {
            return null;
        }
        
        return User::newFromId( $this->session->getUserId() );
    }
    
    public function getCSRF() {
        if ( $this->isAuth() ) {
            return $this->session->getCSRF();
        } else {
            return null;
        }
    }
    
    public function checkCSRF( $csrf ) {
        if ( !$this->isAuth() )
            return false;
        
        if ( $csrf === $this->session->getCSRF() ) {
            return true;
        } else {
            return false;
        }
        
    }
    
}