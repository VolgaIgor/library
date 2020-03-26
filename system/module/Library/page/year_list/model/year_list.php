<?php

class ModelYearListYearList extends Model {
    
    public function get() {
        $sql = "SELECT
  `book`.`year` AS `year`,
  COUNT(`book`.`id`) AS `count_books`
FROM
  `book`
GROUP BY `book`.`year`
ORDER BY `book`.`year` DESC";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'year' => (int)$row['year'],
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        
        return $array;
    }
    
}