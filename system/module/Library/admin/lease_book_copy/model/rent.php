<?php

class ModelLeaseBookCopyRent extends Model {
    
    public function rent( User $user, Book $book ) {
        $sql = "CALL RENT_BOOK({$book->getID()},{$user->getId()},@out_rented_book_copy_id)";

        $result = DB::query( $sql );

        if ( !empty( $result ) ) {
            return true;
        }
        
        return false;
    }
    
    public function getReaderList() {
        $sql = "SELECT `global_user_account`.`user_id` AS `id`, `global_user_account`.`user_login` AS `login` FROM `global_user_account`
LEFT JOIN
  `global_user_group` ON `global_user_group`.`user_id` = `global_user_account`.`user_id`
WHERE `global_user_group`.`group_name` = 'user'";

        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'id' => $row['id'],
                    'login' => $row['login']
                );
            }
        }
        
        return $array;
    }
    
}