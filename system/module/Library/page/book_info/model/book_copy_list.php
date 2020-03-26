<?php

class ModelBookInfoBookCopyList extends Model {
    
    public function getCountAvailable( Book $book ) {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `book_list` WHERE `book_id` = {$book->getID()} AND `available` = 1";
        
        $result = DB::query( $sql );
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            
            return $row['count'];
        }
        
        return 0;
    }
    
    public function getList( Book $book ) {
        $sql = "SELECT `id` FROM `book_list` WHERE `book_id` = {$book->getID()}";
        
        $array = array();
        
        $result = DB::query( $sql );
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = BookCopy::newFromID( $row['id'] );
            }
        }
        
        return $array;
    }
    
}