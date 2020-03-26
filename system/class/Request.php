<?php

class Request {
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $action = null;
    
    public function __construct() {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
        $this->server = $this->clean($_SERVER);
        $this->action = ( ( isset( $this->get['_route_'] ) ) ? $this->get['_route_'] : '' );
    }

    public function clean($data) {
        foreach ($data as $key => $value) {
            if ( is_string($value) ) {
                $data[ $key ] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                /* !!! */
                $data[ $key ] = $value;
            }
        }
        
        return $data;
    }
}