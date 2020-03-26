<?php
class Book {

    private static $instance = array();
    
    private $id = NULL;
    private $publisher_id = NULL;
    private $category_id = NULL;
    private $name = NULL;
    private $description = NULL;
    private $isbn = NULL;
    private $year = NULL;
    
    private $author_id_list = NULL;
    
    public static function newFromID( int $id ) {
        if ( isset( static::$instance[$id] ) ) {
            return static::$instance[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        $result = DB::query("SELECT * FROM `book` WHERE `id` = $id");
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$instance[$id] = new Book( $result );
            
            return static::$instance[$id];
        }
        
        return null;
    }
    
    public static function create( string $name, int $isbn, int $year, Category $category, string $description = '', $publisher = null, array $authors = array() ) {
        
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
        
        if ( $publisher instanceof Publisher ) {
            $publisher = $publisher->getID();
        } else {
            $publisher = 'NULL';
        }
        
        if ( $category instanceof Category ) {
            $category = $category->getID();
        } else {
            $category = 'NULL';
        }
        
        $result = DB::query("INSERT INTO `book`(`id`, `name`, `description`, `isbn`, `year`, `publisher_id`, `category_id`) "
                          . "VALUES (NULL,'{$name}',{$description},{$isbn},{$year},{$publisher},{$category})");
        
        if ( $result ) {
            $book_id = DB::insertId();
            $book = static::newFromID( $book_id );
            
            if ( $book === null ) {
                return false;
            }
            
            foreach ( $authors as $author ) {
                if ( $author instanceof Author ) {
                    $book->addAuthor( $author );
                }
            }
            
            return $book;
        } else {
            return false;
        }
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getAuthorIDList() {
        return $this->author_id_list;
    }
    
    public function getAuthorList() {
        $authors = array();
        
        foreach ( $this->author_id_list as $author_id ) {
            $authors[] = Author::newFromID($author_id);
        }
        
        return $authors;
    }
    
    public function getPublisherID() {
        return $this->publisher_id;
    }
    
    public function getPublisher() {
        if ( $this->publisher_id !== null ) {
            return Publisher::newFromID( $this->publisher_id );
        } 
        
        return null;
    }
    
    public function getCategoryID() {
        return $this->category_id;
    }
    
    public function getCategory() {
        if ( $this->category_id !== null ) {
            return Category::newFromID( $this->category_id );
        } 
        
        return null;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getISBN() {
        return $this->isbn;
    }
    
    public function getYear() {
        return $this->year;
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
        
        if ( DB::query("UPDATE `book` SET `name`='{$name}' WHERE `id`={$this->id}") ) {
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
        
        if ( DB::query("UPDATE `book` SET `description`=$sql WHERE `id`={$this->id}") ) {
            $this->description = $description;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setISBN( int $isbn ) {
        if ( $isbn === $this->isbn ) {
            return true;
        }
        
        if ( DB::query("UPDATE `book` SET `isbn`=$isbn WHERE `id`={$this->id}") ) {
            $this->isbn = $isbn;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setYear( int $year ) {
        if ( $year === $this->year ) {
            return true;
        }
        
        if ( DB::query("UPDATE `book` SET `year`=$year WHERE `id`={$this->id}") ) {
            $this->year = $year;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setCategory( Category $category ) {
        if ( $category->getID() === $this->category_id ) {
            return true;
        }
        
        if ( DB::query("UPDATE `book` SET `category_id`={$category->getID()} WHERE `id`={$this->id}") ) {
            $this->category_id = $category;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setPublisher( $publisher ) {
        if ( $publisher instanceof Publisher ) {
            $publisher = $publisher->getID();
        } else {
            $publisher = 'NULL';
        }
        
        if ( DB::query("UPDATE `book` SET `publisher_id`={$publisher} WHERE `id`={$this->id}") ) {
            $this->publisher_id = $publisher;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function addAuthor( Author $author ) {
        if ( in_array( $author->getID(), $this->author_id_list ) ) {
            return true;
        }
        
        if ( 
            DB::query("INSERT INTO `book_authors`(`book_id`, `author_id`)"
                    . "VALUES ({$this->id},{$author->getID()})")
        ) {
            $this->author_id_list[] = $author->getID();
            
            return true;
        } else {
            return false;
        }
    }
    
    public function deleteAuthor( Author $author ) {
        if ( !in_array( $author->getID(), $this->author_id_list ) ) {
            return true;
        }
        
        if ( 
            DB::query("DELETE FROM `book_authors`"
                    . "WHERE `book_id`={$this->id} AND `author_id`={$author->getID()}")
        ) {
            unset( $this->author_id_list[ array_search($author->getID(), $this->author_id_list) ] );
            
            return true;
        } else {
            return false;
        }
    }
    
    public function deleteAllAuthors() {
        if ( 
            DB::query("DELETE FROM `book_authors`"
                    . "WHERE `book_id`={$this->id}")
        ) {
            $this->author_id_list = array();
            
            return true;
        } else {
            return false;
        }
    }
    
    public function delete() {
        if ( DB::query("DELETE FROM `book` WHERE `id`={$this->id}") ) {
            unset( static::$instance[$this->id] );
                    
            $this->id = null;
            $this->publisher_id = null;
            $this->category_id = null;
            $this->name = null;
            $this->description = null;
            $this->isbn = null;
            $this->year = null;
            $this->author_id_list = array();
            
            return true;
        } else {
            return false;
        }
    }
    
    private function __construct( $data ) {
        $this->id = $data['id'];
        $this->publisher_id = $data['publisher_id'];
        $this->category_id = $data['category_id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->isbn = $data['isbn'];
        $this->year = $data['year'];
        
        $this->author_id_list = array();
        
        $result = DB::query("SELECT `author_id` FROM `book_authors` WHERE `book_id` = {$this->id}");
        if ( $result !== null && $result->num_rows >= 1 ) {
            while ( $row = $result->fetch_assoc() ) {
                $this->author_id_list[] = $row['author_id'];
            }
        }
    }
}
