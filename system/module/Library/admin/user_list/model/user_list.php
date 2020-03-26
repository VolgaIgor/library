<?php

class ModelUserListUserList extends Model {
    
    public function get() {
        $sql = "SELECT `user_id` FROM `global_user_account` ORDER BY `user_registration` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = User::newFromID( $row['user_id'] );
            }
        }
        
        return $array;
    }
    
}