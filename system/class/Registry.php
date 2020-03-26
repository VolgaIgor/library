<?php

class Registry {
    private $registry = array();

    public function get($key) {
        return (isset($this->registry[$key]) ? $this->registry[$key] : null);
    }

    public function __get($key) {
        return $this->get($key);
    }
    
    public function set($key, $value) {
        $this->registry[$key] = $value;
    }
    
    public function __set($key, $value) {
        $this->set($key, $value);
    }
}