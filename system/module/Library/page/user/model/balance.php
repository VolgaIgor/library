<?php

class ModelUserBalance extends Model {
    
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
    
    public function getLog( User $user ) {
        $sql = "SELECT `id`, `amount`, `date` FROM `log_debt` WHERE `user_id` = {$user->getId()} ORDER BY `date` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array( 
                    'id' => $row['id'],
                    'amount' => $row['amount'],
                    'date' => date( 'H:i:s d.m.Y', (int)$row['date'] )
                );
            }
        }
        
        return $array;
    }
    
}