<?php

class ModelCategoryListCategoryList extends Model {
    
    public function get() {
        $sql = "SELECT
  `category`.`id` AS `category_id`,
  COUNT(`book`.`id`) AS `count_books`
FROM
  `category`
LEFT JOIN
  `book` ON `book`.`category_id` = `category`.`id`
GROUP BY `category`.`id`";
        
        $result = DB::query( $sql );
        
        $array = array();
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $array[] = array(
                    'category' => Category::newFromID( $row['category_id'] ),
                    'count_books' => (int)$row['count_books']
                );
            }
        }
        
        return $array;
    }
    
}