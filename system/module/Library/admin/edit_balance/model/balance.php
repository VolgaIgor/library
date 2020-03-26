<?php

class ModelEditBalanceBalance extends Model {
    
    public function setDiff( User $user, int $sum ) {
        if ( $sum == 0 ) {
            return true;
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
    
    public function getBalance( User $user ) {
        $sql = "SELECT `amount` FROM `user_balance` WHERE `user_id` = {$user->getId()}";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            return $row['amount'];
        }
        
        return $array;
    }
    
}