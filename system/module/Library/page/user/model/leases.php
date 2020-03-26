<?php

class ModelUserLeases extends Model {
    
    public function getLog( User $user ) {
        $sql = "SELECT `id`, `book_list_id`, `date_create`, `date_returned` FROM `log_lease` WHERE `client_id` = {$user->getId()} ORDER BY `date_create` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array( 
                    'id' => $row['id'],
                    'book_copy' => BookCopy::newFromID( $row['book_list_id'] ),
                    'date_create' => date( 'H:i:s d.m.Y', (int)$row['date_create'] ),
                    'date_returned' => ( !empty( $row['date_returned'] ) ) ? date( 'H:i:s d.m.Y', (int)$row['date_returned'] ) : 0
                );
            }
        }
        
        return $array;
    }
    
}