<?php

class Theme {
    
    private $env;
    
    public function __construct( Registry $env ) {
        $this->env = $env;
    }
    
    public function getTheme() {
        return MAIN_THEME;
    }
    
    public function render( $template, $data = array() ) {
        if ( file_exists(SYSTEM_DIR . '/theme/' . MAIN_THEME . '/' . $template . '.html') ) {
            if ( $this->env->auth->isAuth() ) {
                $data['csrf'] = $this->env->auth->getCSRF();
            }

            ob_start();

            include SYSTEM_DIR . '/theme/' . MAIN_THEME . '/' . $template . '.html';

            return ob_get_clean();
        } else {
            return null;
        }
    }
    
}