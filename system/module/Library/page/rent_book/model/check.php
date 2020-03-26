<?php

class ModelRentBookCheck extends Model {
    
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
    
    public function haveDuplicateRented( User $user, Book $book ) {
        $sql = "SELECT
  COUNT(`log_lease`.`id`) AS `count`
FROM
  `log_lease`
LEFT JOIN
  `book_list` ON `book_list`.`id` = `log_lease`.`book_list_id`
WHERE
  `book_list`.`book_id` = {$book->getID()} AND `log_lease`.`client_id` = {$user->getId()} AND `log_lease`.`date_returned` IS NULL";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows == 1 ) {
            $row = $result->fetch_assoc();
            
            if ( $row['count'] == 0 ) {
                return false;
            } else {
                return true;
            }
        }
        
        return true;
    }
    
    public function haveRentDelinquency( User $user ) {
        $sql = "SELECT
  COUNT(`log_lease`.`id`) AS `count`
FROM
  `log_lease`
LEFT JOIN
  `book_list` ON `book_list`.`id` = `log_lease`.`book_list_id`
LEFT JOIN
  `book` ON `book`.`id` = `book_list`.`book_id`
LEFT JOIN
  `category` ON `category`.`id` = `book`.`category_id`
WHERE
  ( `log_lease`.`client_id` = {$user->getId()} ) AND ( `log_lease`.`date_returned` IS NULL ) AND ( `log_lease`.`date_create` + `category`.`expiration_day` * 86400 < UNIX_TIMESTAMP() )";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows == 1 ) {
            $row = $result->fetch_assoc();
            
            if ( $row['count'] == 0 ) {
                return false;
            } else {
                return true;
            }
        }
        
        return true;
    }
    
    public function haveNegativeBalance( User $user ) {
        $sql = "SELECT `amount` FROM `user_balance` WHERE `user_id` = {$user->getId()}";
        
        $result = DB::query( $sql );
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            if ( $row['amount'] < 0 ) {
                return true;
            } else {
                return false;
            }
        }
        
        return true;
    }
    
}