<?php

class ModelAuthorListAuthorList extends Model {
    
    public function get() {
        $sql = "SELECT
  `author`.`id` AS `author_id`,
  COUNT(`book_authors`.`book_id`) AS `count_books`
FROM
  `author`
LEFT JOIN
  `book_authors` ON `author`.`id` = `book_authors`.`author_id`
GROUP BY `author`.`id`
ORDER BY `count_books` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'author' => Author::newFromID( $row['author_id'] ),
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        
        return $array;
    }
    
}