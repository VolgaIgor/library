<?php
class Category {

    private static $instance = array();
    
    private $id = NULL;
    private $name = NULL;
    private $expiration_day = NULL;
    private $fine_per_day = NULL;
    
    public static function newFromID( int $id ) {
        if ( isset( static::$instance[$id] ) ) {
            return static::$instance[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        $result = DB::query("SELECT * FROM `category` WHERE `id` = $id");
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$instance[$id] = new Category( $result );
            
            return static::$instance[$id];
        }
        
        return null;
    }
    
    public static function create( string $name, int $expiration_day, int $fine_per_day ) {
        
        $name = strip_tags( $name );
        $name = htmlentities( html_entity_decode( $name, ENT_NOQUOTES ), ENT_NOQUOTES );
        $name = mb_substr( $name, 0, 200 );
        if ( empty( $name ) ) {
            return false;
        }
        $name = DB::escapeString($name);
        
        $result = DB::query("INSERT INTO `category`(`id`, `name`, `expiration_day`, `fine_per_day`) "
                          . "VALUES (NULL,'{$name}', {$expiration_day}, {$fine_per_day})");
        
        if ( $result ) {
            $category_id = DB::insertId();
            $category = static::newFromID( $category_id );
            
            if ( $category === null ) {
                return false;
            }
            
            return $category;
        } else {
            return false;
        }
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getExpirationDay() {
        return $this->expiration_day;
    }
    
    public function getFinePerDay() {
        return $this->fine_per_day;
    }
    
    public function setName( string $name ) {
        $name = strip_tags( $name );
        $name = htmlentities( html_entity_decode( $name, ENT_NOQUOTES ), ENT_NOQUOTES );
        $name = mb_substr( $name, 0, 200 );
        if ( empty( $name ) ) {
            return false;
        }
        
        if ( $name === $this->name ) {
            return true;
        }
        
        $name = DB::escapeString($name);
        
        if ( DB::query("UPDATE `category` SET `name`='{$name}' WHERE `id`={$this->id}") ) {
            $this->name = $name;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setExpirationDay( int $days ) {
        if ( $days === $this->expiration_day ) {
            return true;
        }
        
        if ( DB::query("UPDATE `category` SET `expiration_day`=$days WHERE `id`={$this->id}") ) {
            $this->expiration_day = $days;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setFinePerDay( int $fine ) {
        if ( $fine === $this->fine_per_day ) {
            return true;
        }
        
        if ( DB::query("UPDATE `category` SET `fine_per_day`=$fine WHERE `id`={$this->id}") ) {
            $this->fine_per_day = $fine;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function delete() {
        if ( DB::query("DELETE FROM `category` WHERE `id`={$this->id}") ) {
            unset( static::$instance[$this->id] );
                    
            $this->id = null;
            $this->name = null;
            $this->expiration_day = null;
            $this->fine_per_day = null;
            
            return true;
        } else {
            return false;
        }
    }
    
    private function __construct( $data ) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->expiration_day = $data['expiration_day'];
        $this->fine_per_day = $data['fine_per_day'];
    }
}
