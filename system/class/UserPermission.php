<?php

class UserPermission {
    
    private static $static_permissions = array();
    
    private $user_id = NULL;
    
    private $user_groups = array();
    private $user_permissions = array();

    public static function newFromUID( $id ) {
        $id = (int)$id;
        
        if ( isset( static::$static_permissions[$id] ) ) {
            return static::$static_permissions[$id];
        }
        
        if ( $id <= 0 )
            return null;
        
        static::garbageCollector();
        
        $result = DB::query("SELECT * FROM `global_user_group` WHERE `user_id` = $id");
        
        if ( $result === null ) {
            return null;
        }
            
        static::$static_permissions[$id] = new UserPermission( $id, $result );
            
        return static::$static_permissions[$id];
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getGroups() {
        return $this->user_groups;
    }
    
    public function getPermissions() {
        return $this->user_permissions;
    }
    
    public function checkGroup( $name ) {
        return in_array($name, $this->user_groups);
    }
    
    public function checkPermission( $name ) {
        return ( ( isset($this->user_permissions[$name]) ) ? $this->user_permissions[$name] : false );
    }
    
    public function setGroup( $name, $time = 0 ) {
        global $USER_RIGHT;
        
        if ( in_array($name, $this->user_groups) ) {
            return true;
        }
        
        if ( !isset( $USER_RIGHT[ $name ] ) ) {
            return false;
        }
        
        if ( $time > 0 ) {
            $time = (int)( time() + $time );
        } else {
            $time = 'NULL';
        }
        
        if ( DB::query("INSERT INTO `global_user_group`(`user_id`, `group_name`, `group_expires`) VALUES ({$this->user_id},'$name',$time)") ) {
            $this->user_groups[] = $name;
            $this->refreshPermissions();
            
            return true;
        } else {
            return false;
        }
    }
    
    public function deleteGroup( $name ) {
        if ( !in_array($name, $this->user_groups) ) {
            return true;
        }
        
        if ( !preg_match('/^[0-9a-z_-]{1,32}$/', $name) ) {
            return false;
        }
        
        if ( DB::query("DELETE FROM `global_user_group` WHERE `user_id` = {$this->user_id} AND `group_name` = '$name'") ) {
            
            if( ( $key = array_search($name, $this->user_groups) ) !== false ) {
                 unset( $this->user_groups[$key] );
            }
            
            $this->refreshPermissions();
            
            return true;
        } else {
            return false;
        }
    }
    
    private function refreshPermissions() {
        global $USER_RIGHT;
        
        $this->user_permissions = array();
        
        foreach ( $this->user_groups as $group ) {
            if ( !empty( $USER_RIGHT[ $group ] ) ) {
                foreach ( $USER_RIGHT[ $group ] as $key => $value ) {
                    if ( isset( $this->user_permissions[ $key ] ) ) {
                        $this->user_permissions[ $key ] = $this->user_permissions[ $key ] & $USER_RIGHT[ $group ][ $key ];
                    } else {
                        $this->user_permissions[ $key ] = $USER_RIGHT[ $group ][ $key ];
                    }
                }
            }
        }
    }
    
    private static function garbageCollector() {
        DB::query("DELETE FROM `global_user_group` WHERE `group_expires` != NULL AND `group_expires` < " . time());
    }
    
    private function __construct( $uid, $result ) {
        global $USER_RIGHT;
        
        $this->user_id = (int)$uid;
        
        while ( $row = $result->fetch_assoc() ) {
            $this->user_groups[] = $row['group_name'];
            
            $this->refreshPermissions();
        }
        
    }
    
}