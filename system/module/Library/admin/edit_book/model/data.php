<?php

class ModelEditBookData extends Model {
    
    public function getAllAuthors() {
        $sql = "SELECT `id`, `name` FROM `author`";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = $row;
            }
        }
        
        return $array;
    }
    
    public function getAllPublishers() {
        $sql = "SELECT `id`, `name` FROM `publisher`";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = $row;
            }
        }
        
        return $array;
    }
    
    public function getAllCategories() {
        $sql = "SELECT `id`, `name` FROM `category`";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = $row;
            }
        }
        
        return $array;
    }
    
}