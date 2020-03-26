<?php

class ModelBookCopyLeases extends Model {
    
    public function getLog( int $book_copy_id ) {
        $sql = "SELECT `id`, `client_id`, `date_create`, `date_returned` FROM `log_lease` WHERE `book_list_id` = {$book_copy_id} ORDER BY `date_create` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array( 
                    'id' => $row['id'],
                    'client' => User::newFromID( $row['client_id'] ),
                    'date_create' => date( 'H:i:s d.m.Y', (int)$row['date_create'] ),
                    'date_returned' => ( !empty( $row['date_returned'] ) ) ? date( 'H:i:s d.m.Y', (int)$row['date_returned'] ) : 0
                );
            }
        }
        
        return $array;
    }
    
}