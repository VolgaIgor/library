<?php

class ModelRentBookRent extends Model {
    
    public function rent( User $user, Book $book ) {
        $sql = "CALL RENT_BOOK({$book->getID()},{$user->getId()},@out_rented_book_copy_id)";

        $result = DB::query( $sql );

        if ( !empty( $result ) ) {
            return true;
        }
        
        return false;
    }
    
}