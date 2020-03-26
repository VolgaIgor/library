<?php
class Author {

    private static $instance = array();
    
    private $id = NULL;
    private $name = NULL;
    private $description = NULL;
    
    public static function newFromID( int $id ) {
        if ( isset( static::$instance[$id] ) ) {
            return static::$instance[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        $result = DB::query("SELECT * FROM `author` WHERE `id` = $id");
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$instance[$id] = new Author( $result );
            
            return static::$instance[$id];
        }
        
        return null;
    }
    
    public static function create( string $name, string $description = '' ) {
        
        $name = strip_tags( $name );
        $name = htmlentities( html_entity_decode( $name, ENT_NOQUOTES ), ENT_NOQUOTES );
        $name = mb_substr( $name, 0, 200 );
        if ( empty( $name ) ) {
            return false;
        }
        $name = DB::escapeString($name);
        
        if ( empty( $description ) ) {
            $description = 'NULL';
        } else {
            $description = strip_tags( $description );
            $description = htmlentities( html_entity_decode( $description, ENT_NOQUOTES ), ENT_NOQUOTES );
            if ( empty( $description ) ) {
                $description = 'NULL';
            } else {
                $description = "'" . DB::escapeString($description) . "'";
            }
        }
        
        $result = DB::query("INSERT INTO `author`(`id`, `name`, `description`) "
                          . "VALUES (NULL,'{$name}',{$description})");
        
        if ( $result ) {
            $author_id = DB::insertId();
            $author = static::newFromID( $author_id );
            
            if ( $author === null ) {
                return false;
            }
            
            return $author;
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
    
    public function getDescription() {
        return $this->description;
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
        
        if ( DB::query("UPDATE `author` SET `name`='{$name}' WHERE `id`={$this->id}") ) {
            $this->name = $name;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setDescription( string $description ) {
        $description = strip_tags( $description );
        $description = htmlentities( html_entity_decode( $description, ENT_NOQUOTES ), ENT_NOQUOTES );
        if ( empty( $description ) ) {
            $description = null;
            $sql = 'NULL';
        } else {
            $description = DB::escapeString($description);
            $sql = "'" . $description . "'";
        }
        
        if ( DB::query("UPDATE `author` SET `description`=$sql WHERE `id`={$this->id}") ) {
            $this->description = $description;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function delete() {
        if ( DB::query("DELETE FROM `author` WHERE `id`={$this->id}") ) {
            unset( static::$instance[$this->id] );
                    
            $this->id = null;
            $this->name = null;
            $this->description = null;
            
            return true;
        } else {
            return false;
        }
    }
    
    private function __construct( $data ) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
    }
}
