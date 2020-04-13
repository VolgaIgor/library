<?php

class ModelBookListBookList extends Model {
    
    public function get( $sort = array() ) {
        $sql_sort = array();
        
        if ( !empty( $sort['name'] ) ) {
            if ( $sort['name'] == 'up' ) {
                $sql_sort[] = '`book`.`name` ASC';
            } else {
                $sql_sort[] = '`book`.`name` DESC';
            }
        }
        
        if ( !empty( $sort['year'] ) ) {
            if ( $sort['year'] == 'up' ) {
                $sql_sort[] = '`book`.`year` ASC';
            } else {
                $sql_sort[] = '`book`.`year` DESC';
            }
        }
        
        if ( !empty( $sort['count'] ) ) {
            if ( $sort['count'] == 'up' ) {
                $sql_sort[] = '`count_books` ASC';
            } else {
                $sql_sort[] = '`count_books` DESC';
            }
        }
        
        if ( empty( $sql_sort ) ) {
            $sql_sort = 'ORDER BY `count_books` DESC';
        } else {
            $sql_sort = 'ORDER BY ' . implode(',', $sql_sort);
        }
        
        $sql = "SELECT DISTINCT
  `book`.`id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book`.`id`
GROUP BY `book`.`id` {$sql_sort}";
        
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