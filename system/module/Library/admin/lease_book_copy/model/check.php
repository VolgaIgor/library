<?php

class ModelLeaseBookCopyCheck extends Model {
    
    public function haveBookCopy( Book $book ) {
        $sql = "SELECT COUNT(`id`) AS `count` FROM `book_list` WHERE `book_id` = {$book->getID()} AND `available` = 1";
        
        $result = DB::query( $sql );
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            
            if ( $row['count'] > 0 ) {
                return true;
            } else {
                return false;
            }
        }
        
        return false;
    }
    
}