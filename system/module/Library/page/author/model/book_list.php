<?php

class ModelAuthorBookList extends Model {
    
    public function get( int $author_id ) {
        $sql = "SELECT
  `book_authors`.`book_id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book_authors`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book_authors`.`book_id`
WHERE
  `author_id` = {$author_id}
GROUP BY `book_authors`.`book_id`";
        
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