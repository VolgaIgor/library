<?php

class Response {
    private $headers = array();
    private $output = '';

    public function addHeader($header) {
        $this->headers[] = $header;
    }
    
    public function redirect($url, $status = 302, $external = false) {
        if ( !$external ) {
            $domain = ( (USE_SSL) ? 'https://' : 'http://' ) . MAIN_DOMAIN;
        } else {
            $domain = '';
        }
        header('Location: ' . $domain . $url, true, $status);
        exit();
    }
    
     /* В одну */
    public function err403() {
        $this->redirect( '/404' );
    }
    
    public function err404() {
        $this->redirect( '/404' );
    }
    
    public function err500() {
        $this->redirect( '/500' );
    }
    
    public function getOutput() {
        return $this->output;
    }
    
    public function setOutput($output) {
        $this->output .= $output;
    }
    
    /*
    private function compress($data, $level = 0) {
        if ( !extension_loaded('zlib') ) {
            return $data;
        }
        
        if ( !isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
            return $data;
        }
        
        if ( strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false ) {
            $encoding = 'gzip';
        } else if ( strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false ) {
            $encoding = 'x-gzip';
        }
        
        if (!isset($encoding) || ($level < -1 || $level > 9)) {
            return $data;
        }
        
        $this->addHeader('Content-Encoding: ' . $encoding);
        
        return gzencode($data, (int)$level);
    }*/
    
    public function output() {
        if ( connection_aborted() == 1 ) {
            return false;
        }
        
        // $output = ( USE_GZIP_COMPRESS ? $this->compress($this->output, GZIP_COMPRESS_LEVEL) : $this->output );
        
        foreach ($this->headers as $header) {
            header($header, true);
        }
        
        echo $this->output;
        
        return true;
    }
}