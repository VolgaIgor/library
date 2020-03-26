<?php

class ModelPublisherBookList extends Model {
    
    public function get( int $publisher_id ) {
        $sql = "SELECT
  `book`.`id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book`.`id`
WHERE
  `publisher_id` = {$publisher_id}
GROUP BY `book`.`id`
ORDER BY `count_books` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'book' => Book::newFromID( $row['book_id'] ),
                    'available' => (int)$row['count_books']
                );
            }
        }
        
        return $array;
    }
    
}