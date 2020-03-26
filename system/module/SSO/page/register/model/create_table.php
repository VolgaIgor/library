<?php

class ModelRegisterCreateTable extends Model {
    
    public function create( User $user ) {
        $sql = "INSERT INTO `user_balance`(`user_id`, `amount`) VALUES ({$user->getId()},0)";
        
        $result = DB::query( $sql );
        
        if ( $result === true ) {
            return true;
        } else {
            return false;
        }
    }
    
}