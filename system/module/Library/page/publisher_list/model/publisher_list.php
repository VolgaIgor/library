<?php

class ModelPublisherListPublisherList extends Model {
    
    public function get() {
        $sql = "SELECT
  `publisher`.`id` AS `publisher_id`,
  COUNT(`book`.`id`) AS `count_books`
FROM
  `publisher`
LEFT JOIN
  `book` ON `book`.`publisher_id` = `publisher`.`id`
GROUP BY `publisher`.`id`";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'publisher' => Publisher::newFromID( $row['publisher_id'] ),
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        
        return $array;
    }
    
}