<?php
class BookCopy {

    private static $instance = array();
    
    private $id = NULL;
    private $book_id = NULL;
    private $place = NULL;
    private $available = NULL;
    
    public static function newFromID( int $id ) {
        if ( isset( static::$instance[$id] ) ) {
            return static::$instance[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        $result = DB::query("SELECT * FROM `book_list` WHERE `id` = $id");
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$instance[$id] = new BookCopy( $result );
            
            return static::$instance[$id];
        }
        
        return null;
    }
    
    /* СПИСОК ПО КНИГЕ */
    
    public static function create( Book $book, int $place, bool $available ) {
        
        $available = (int)$available;
        
        $result = DB::query("INSERT INTO `book_list`(`id`, `book_id`, `place`, `available`) "
                          . "VALUES (NULL,{$book->getID()},{$place},{$available})");
        
        if ( $result ) {
            $book_copy_id = DB::insertId();
            $book_copy = static::newFromID( $book_copy_id );
            
            if ( $book_copy === null ) {
                return false;
            }
            
            return $book_copy;
        } else {
            return false;
        }
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getBook() {
        return Book::newFromID( $this->book_id );
    }
    
    public function getBookId() {
        return $this->book_id;
    }
    
    public function getPlace() {
        return $this->place;
    }
    
    public function isAvailable() {
        return $this->available;
    }
    
    public function setPlace( int $place ) {
        if ( $place === $this->place ) {
            return true;
        }
        
        if ( DB::query("UPDATE `book_list` SET `place`=$place WHERE `id`={$this->id}") ) {
            $this->place = $place;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setAvailable( bool $available ) {
        if ( $available === $this->available ) {
            return true;
        }
        
        $available = (int)$available;
        
        if ( DB::query("UPDATE `book_list` SET `available`=$available WHERE `id`={$this->id}") ) {
            $this->available = $available;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function delete() {
        if ( DB::query("DELETE FROM `book_list` WHERE `id`={$this->id}") ) {
            unset( static::$instance[$this->id] );
                    
            $this->id = null;
            $this->book_id = null;
            $this->place = null;
            $this->available = null;
            
            return true;
        } else {
            return false;
        }
    }
    
    private function __construct( $data ) {
        $this->id = $data['id'];
        $this->book_id = $data['book_id'];
        $this->place = $data['place'];
        $this->available = (bool)$data['available'];
    }
}
