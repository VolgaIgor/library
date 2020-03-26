<?php

class GlobalSession {
    
    protected $gbl_sid = NULL;
    protected $gbl_session_create = NULL;
    protected $gbl_session_last_time = NULL;
    protected $gbl_session_last_ip = NULL;
    protected $user_id = NULL;

    private function __construct( $data ) {
        $this->gbl_sid = $data['gbl_sid'];
        $this->gbl_session_create = (int)$data['gbl_session_create'];
        $this->gbl_session_last_time = (int)$data['gbl_session_last_time'];
        $this->gbl_session_last_ip = $data['gbl_session_last_ip'];
        $this->user_id = (int)$data['user_id'];
    }
    
    public static function newFromSID( $sid ) {
        if ( empty( $sid ) || !preg_match( '/^[a-f0-9]{' . SESSION_KEY_LENGTH . '}$/', $sid ) ) {
            return null;
        }
        
        $result = DB::query("SELECT * FROM `global_session` WHERE `gbl_sid` = '$sid'");
        
        /* Если глобальной сессии в БД не найдено */
        if ( $result === false || $result->num_rows !== 1 ) {
            return null;
        }
        
        $session = $result->fetch_assoc();
        
        $data = array();
        $data['gbl_sid'] = $session['gbl_sid'];
        $data['gbl_session_create'] = (int)$session['gbl_session_create'];
        $data['user_id'] = (int)$session['user_id'];
        $data['gbl_session_last_time'] = (int)$session['gbl_session_last_time'];
        $data['gbl_session_last_ip'] = $session['gbl_session_last_ip'];
        
        /* Если время жизни глобальной сессии закончилось 
           ИЛИ 
           Если пользователь был неактивен больше SESSION_GLOBAL_MAX_INACTIVE 
        */
        if ( 
            $data['gbl_session_create'] + SESSION_GLOBAL_MAX_LIVE < time() ||
            $data['gbl_session_last_time'] + SESSION_GLOBAL_MAX_INACTIVE < time()
        ) {
            DB::query("DELETE FROM `global_session` WHERE `gbl_sid` = '{$data['gbl_sid']}'");
            
            return null;
        }
        
        return new GlobalSession( $data );
    }
    
    public static function create( $uid ) {
        $uid = (int)$uid;
        
        if ( $uid <= 0 ) {
            return null;
        }
        
        $data = array();
        
        $curIP = static::encodeIP( $_SERVER['REMOTE_ADDR'] );
        if ( $curIP === false ) {
            return null;
        }
        
        $data['gbl_session_last_ip'] = $curIP;
        
        $data['gbl_sid'] = static::genGlobalSID();
        $data['gbl_session_create'] = time();
        $data['gbl_session_last_time'] = time();
        $data['user_id'] = $uid;
        
        /* Проверка на занесение в базу */
        if ( 
            !DB::query("INSERT INTO `global_session`(`gbl_sid`, `user_id`, `gbl_session_create`, `gbl_session_last_time`, `gbl_session_last_ip`) " .
                              "VALUES ('{$data['gbl_sid']}',{$data['user_id']},{$data['gbl_session_create']},{$data['gbl_session_last_time']},'{$data['gbl_session_last_ip']}')")
        ) {
            return null;
        }
        
        return new GlobalSession( $data );
    }
    
    public function continue() {
        $curIP = static::encodeIP( $_SERVER['REMOTE_ADDR'] );
        if ( $curIP === false ) {
            return false;
        }
        
        if ( $this->gbl_session_last_ip !== $curIP ) {
            $this->gbl_session_last_time = time();
            $this->gbl_session_last_ip = $curIP;
            
            DB::query("UPDATE `global_session` SET `gbl_session_last_time`={$this->gbl_session_last_time},`gbl_session_last_ip`='{$this->gbl_session_last_ip}' WHERE `gbl_sid` = '{$this->gbl_sid}'");
        } else if ( $this->gbl_session_last_time + SESSION_GLOBAL_LAST_TIME_RANGE < time() ) {
            $this->gbl_session_last_time = time();

            DB::query("UPDATE `global_session` SET `gbl_session_last_time`={$this->gbl_session_last_time} WHERE `gbl_sid` = '{$this->gbl_sid}'");
        }
        
        return true;
        
    }
    
    /* Удаление глобальной сессии */
    public function destroy() {
        DB::query("DELETE FROM `global_session` WHERE `gbl_sid` = '" . $this->gbl_sid . "'");
        
        $this->gbl_sid = NULL;
        $this->gbl_session_create = NULL;
        $this->gbl_session_last_time = NULL;
        $this->gbl_session_last_ip = NULL;
        $this->user_id = NULL;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getGlobalSID() {
        return $this->gbl_sid;
    }
    
    public function getLastTime() {
        return $this->gbl_session_last_time;
    }
    
    public function getLastIP() {
        return static::decodeIP($this->gbl_session_last_ip);
    }
    
    private static function genGlobalSID() {
        
        while ( true ) {
            $session_id = substr( bin2hex( random_bytes( SESSION_KEY_LENGTH ) ), 0, SESSION_KEY_LENGTH );
            
            $result = DB::query("SELECT `gbl_sid` FROM `global_session` WHERE `gbl_sid` = '$session_id'");
            
            if ( $result === false ) {
                return false;
            }
            
            if ( $result->num_rows !== 0 ) {
                continue;
            }
            
            return $session_id;
        }
        
    }
    
    public static function encodeIP( $ip ) {
        
        $ip = inet_pton($ip);
        
        if ( $ip === false )
            return null;
        
        return base64_encode($ip);
        
    }
    
    public static function decodeIP( $ip ) {
        
        $ip = base64_decode($ip);
        
        if ( $ip === false )
            return null;
        
        $ip = inet_ntop($ip);
        
        if ( $ip === false )
            return null;
        
        return $ip;
        
    }
}