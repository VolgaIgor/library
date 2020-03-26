<?php

class View {
    
    private $dir = '';
    
    public function render( $template, $data = array() ) {
        if ( file_exists( SYSTEM_DIR . $this->dir . $template . '.html' ) ) {
            ob_start();
            
            include SYSTEM_DIR . $this->dir . $template . '.html';
            
            return ob_get_clean();
        } else {
            return null;
        }
    }
    
    function __construct( $dir ) {
        $this->dir = $dir . '/view/';
    }
    
}