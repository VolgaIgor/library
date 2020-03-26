<?php

class ModelAddBalanceAdd extends Model {
    
    public function add( User $user, int $sum ) {
        if ( $sum <= 0 ) {
            return false;
        }
        
        $time = time();
        
        $sql = "INSERT INTO `log_debt`(`id`, `user_id`, `amount`, `date`) VALUES (NULL,{$user->getId()},{$sum},{$time})";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result === true ) {
            return true;
        } else {
            return false;
        }
    }
    
}