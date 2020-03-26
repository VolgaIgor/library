<?php
class User {
    
    private static $static_users = array();
    
    const SALT = 'gvhbjnjgnkjgvjhbn';
    
    private $user_id = NULL;
    private $user_login = NULL;
    private $user_real_name = NULL;
    private $user_registration = NULL;
    private $user_pass = NULL;
    
    public $permissions = NULL;
    public $settings = NULL;
    public $emails = NULL;
    public $codes = NULL;

    public static function newFromId( int $id ) {
        if ( isset( static::$static_users[$id] ) ) {
            return static::$static_users[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        $result = DB::query("SELECT * FROM `global_user_account` WHERE `user_id` = $id");
        
        if ( $result !== null && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$static_users[$id] = new User( $result );
            
            return static::$static_users[$id];
        }
        
        return null;
    }
    
    public static function newFromLogin( $login ) {
        if ( !static::validateLogin( $login ) ) {
            return null;
        }
        
        /* TODO strlower */
        foreach ( static::$static_users as $id => $value ) {
            if ( $value->getLogin() === $login ) {
                return static::$static_users[$id];
            }
        }
        
        $login = strtolower( $login );
        
        $result = DB::query("SELECT * FROM `global_user_account` WHERE LOWER(`user_login`) = '$login'");
                
        if ( $result !== false && $result->num_rows === 1 ) {
            $result = $result->fetch_assoc();
            
            static::$static_users[ (int)$result['user_id'] ] = new User( $result );
        
            return static::$static_users[ (int)$result['user_id'] ];
        }
        
        return null;
    }
    
    public static function create( $login, $data = array() ) {
        if ( static::validateLogin( $login ) === false || static::newFromLogin( $login ) !== null ) {
            return false;
        }
        
        $time = time();
        
        if ( DB::query("INSERT INTO `global_user_account`(`user_id`, `user_login`, `user_pass`, `user_registration`, `user_real_name`) "
                . "VALUES (NULL,'{$login}',NULL,{$time},NULL)") ) {
            
            $uid = DB::insertId();
            $user = static::newFromId( $uid );
            
            if ( $user === null ) {
                return false;
            }
            
            $user->getPermissions()->setGroup( 'user' );
            
            if ( !empty( $data['pass'] ) ) {
                $user->setPass( $data['pass'] );
            }
            
            return $user;
        } else {
            return false;
        }
    }
    
    public function getId() {
        return $this->user_id;
    }
    
    public function getRealName() {
        return $this->user_real_name;
    }
    
    public function getLogin() {
        return $this->user_login;
    }
    
    public function getRegistration() {
        return $this->user_registration;
    }
    
    public function getPermissions() {
        if ( $this->permissions === null ) {
            $this->permissions = UserPermission::newFromUID( $this->user_id );
        }
        
        return $this->permissions;
    }
    
    public function checkPass( $pass ) {
        $pass = sha1( $pass . self::SALT );
        return $this->user_pass === $pass;
    }
    
    public function setRealName( string $real_name ) {
        $real_name = strip_tags( $real_name );
        $real_name = htmlentities( html_entity_decode( $real_name, ENT_NOQUOTES ), ENT_NOQUOTES );
        $real_name = mb_substr( $real_name, 0, 100 );
        
        if ( $real_name === $this->user_real_name ) {
            return true;
        }
        
        $real_name = DB::escapeString($real_name);
        
        if ( DB::query("UPDATE `global_user_account` SET `user_real_name`='{$real_name}' WHERE `user_id`={$this->user_id}") ) {
            $this->user_real_name = $real_name;
            
            return true;
        } else {
            return false;
        }
    }
    
    public function setPass( $pass ) {
        $pass = (string)$pass;
        
        if ( $this->user_pass == $pass ) {
            return false;
        }
        
        if ( empty($pass) ) {
            return false;
        }
        
        $newPass = sha1( $pass . self::SALT );
        
        if ( DB::query("UPDATE `global_user_account` SET `user_pass`='$newPass' WHERE `user_id`={$this->user_id}") ) {
            $this->user_pass = $newPass;
            
            return true;
        } else {
            return false;
        }
    }
    
    public static function validateLogin( $login ) {
        if ( preg_match('/^[0-9A-Za-z_-]{4,32}$/', $login) ) {
            return true;
        } else {
            return false;
        }
    }
    
    private function __construct( $data ) {
        $this->user_id = (int)$data['user_id'];
        $this->user_login = $data['user_login'];
        $this->user_registration = $data['user_registration'];
        $this->user_real_name = $data['user_real_name'];
        $this->user_pass = $data['user_pass'];
    }
    
}