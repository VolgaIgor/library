<?php

class ModelSearchPatterns extends Model {
    
    public function findBookByName( string $name ) {
        $sql = "SELECT
  `book`.`id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book`.`id`
WHERE `book`.`name` LIKE '%{$name}%'
GROUP BY `book`.`id`
LIMIT 3";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'book' => Book::newFromID( $row['book_id'] ),
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        return $array;
    }
    
    public function findByISBN( int $isbn ) {
        $sql = "";
        
        $result = DB::query( $sql );
        
        $array = array();
        
        return $array;
    }
    
    public function findByYear( int $year ) {
        $sql = "SELECT
  `book`.`id` AS `book_id`,
  SUM(`book_list`.`available`) AS `count_books`
FROM
  `book`
LEFT JOIN
  `book_list` ON `book_list`.`book_id` = `book`.`id`
WHERE `book`.`year` = {$year}
GROUP BY `book`.`id`
LIMIT 3";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'book' => Book::newFromID( $row['book_id'] ),
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        return $array;
    }
    
    public function findAuthorsByName( string $name ) {
        $sql = "SELECT
  `author`.`id` AS `author_id`,
  COUNT(`book_authors`.`book_id`) AS `count_books`
FROM
  `author`
LEFT JOIN
  `book_authors` ON `author`.`id` = `book_authors`.`author_id`
WHERE `author`.`name` LIKE '%{$name}%'
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
    
    public function findPublishersByName( string $name ) {
        $sql = "SELECT
  `publisher`.`id` AS `publisher_id`,
  COUNT(`book`.`id`) AS `count_books`
FROM
  `publisher`
LEFT JOIN
  `book` ON `book`.`publisher_id` = `publisher`.`id`
WHERE `publisher`.`name` LIKE '%{$name}%'
GROUP BY `publisher`.`id`
ORDER BY `count_books` DESC";
        
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