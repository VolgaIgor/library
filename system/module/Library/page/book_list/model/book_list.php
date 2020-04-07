<?php

class ModelBookListBookList extends Model {
    
    public function get( $sort = 'count_down' ) {
        switch ( $sort ) {
            case 'name_up':
                $sortSql = 'ORDER BY `book`.`name` ASC';
                break;
            case 'name_down':
                $sortSql = 'ORDER BY `book`.`name` DESC';
                break;
            case 'year_up':
                $sortSql = 'ORDER BY `book`.`year` ASC';
                break;
            case 'year_down':
                $sortSql = 'ORDER BY `book`.`year` DESC';
                break;
            case 'count_up':
                $sortSql = 'ORDER BY `count_books` ASC';
                break;
            case 'count_down':
                $sortSql = 'ORDER BY `count_books` DESC';
                break;
            default:
                $sortSql = 'ORDER BY `count_books` DESC';
        }
        
        $sql = "SELECT DISTINCT
  `book`.`id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book`.`id`
GROUP BY `book`.`id` {$sortSql}";
        
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