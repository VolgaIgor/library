<?php

class Session extends GlobalSession {
    
    private $local_sid = NULL;
    private $local_session_create = NULL;
    private $local_session_status = NULL;
    private $user_hash = NULL;
    private $user_csrf = NULL;

    private function __construct( GlobalSession $gbl, $data ) {
        $this->gbl_sid = $gbl->gbl_sid;
        $this->gbl_session_create = $gbl->gbl_session_create;
        $this->gbl_session_last_time = $gbl->gbl_session_last_time;
        $this->gbl_session_last_ip = $gbl->gbl_session_last_ip;
        $this->user_id = $gbl->user_id;
        
        $this->local_sid = $data['local_sid'];
        $this->local_session_create = (int)$data['local_session_create'];
        $this->local_session_status = (int)$data['local_session_status'];
        $this->user_hash = $data['user_hash'];
        $this->user_csrf = $data['user_csrf'];
    }
    
    public static function newFromSID( $global_sid, $local_sid = NULL ) {
        $global_session = parent::newFromSID( $global_sid );
        
        if ( $global_session === null ) {
            /* ---Подозрительное поведение--- */
            
            if ( preg_match( '/^[a-f0-9]{' . SESSION_KEY_LENGTH . '}$/', $local_sid ) ) {
                $result = DB::query("SELECT `gbl_sid` FROM `%PREFIX%_session` WHERE `sid` = '$local_sid'");
                
                if ( $result !== false && $result->num_rows === 1 ) {
                    $row = $result->fetch_assoc();
                    DB::query("DELETE FROM `global_session` WHERE `gbl_sid` = '{$row['gbl_sid']}'");
                    
                    DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$row['gbl_sid']}'");
                }
            }
            
            return null;
        }
        
        /* Проверка локальной сессии */
        if ( !preg_match( '/^[a-f0-9]{' . SESSION_KEY_LENGTH . '}$/', $local_sid ) ) {
            /* Подозрительно, возможно взлом */
            $global_session->destroy();
            
            return null;
        }
        
        $result = DB::query("SELECT * FROM `%PREFIX%_session` WHERE `sid` = '$local_sid'");
        
        /* Если локальной сессии в БД не найдено */
        if ( $result === false || $result->num_rows !== 1 ) {
            /* Подозрительно, возможно взлом */
            $global_session->destroy();
            
            return null;
        }
        
        $local_session = $result->fetch_assoc();
        
        if ( $local_session['gbl_sid'] != $global_session->getGlobalSID() ) {
            /* Ну точно какой-то взлом */
            
            DB::query("DELETE FROM `global_session` WHERE `gbl_sid` = '{$local_session['gbl_sid']}'");
            DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$local_session['gbl_sid']}'");

            $global_session->destroy();
            
            return null;
        }
        
        $data = array();
        $data['local_sid'] = $local_session[ 'sid' ];
        $data['local_session_create'] = (int)$local_session[ 'session_create' ];
        $data['local_session_status'] = (int)$local_session[ 'session_status' ];
        $data['user_hash'] = $local_session['user_hash'];
        $data['user_csrf'] = $local_session['user_csrf'];
        
        if ( $data['local_session_status'] === 0 ) {
            /* Если локальная сессия помечена устаревшей */
            if ( $data['local_session_create'] + SESSION_OLD_LOCAL_MAX_LIVE < time() ) {
                /* Похоже на взлом */
                DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$global_session->getGlobalSID()}'");
                
                $global_session->destroy();
                
                return null;
            }
        }
        
        return new Session( $global_session, $data );
    }
    
    public static function create( $uid, GlobalSession $global_session = null ) {
        $uid = (int)$uid;
        
        if ( $global_session != null ) {
            if ( $global_session->getUserId() !== $uid ) {
                return null;
            }
        }
        
        if ( $global_session == null ) {
            $global_session = parent::create( $uid );
            if ( $global_session === null ) {
                return null;
            }
        }
        
        $data = array();
        
        $data['local_sid'] = static::genLocalSID();
        $data['local_session_create'] = time();
        $data['local_session_status'] = 1;
        $data['user_hash'] = static::getCurUserHash();
        $data['user_csrf'] = static::genCSRF();
        
        /* Проверка на занесение в базу */                     
        if ( !DB::query("INSERT INTO `%PREFIX%_session`(`sid`, `gbl_sid`, `session_create`, `session_status`, `user_hash`, `user_csrf`) VALUES " .
                               "('{$data['local_sid']}','{$global_session->getGlobalSID()}',{$data['local_session_create']},1,'{$data['user_hash']}','{$data['user_csrf']}')")
        ) {
            $global_session->destroy();
            
            return null;
        }
        
        return new Session( $global_session, $data );
    }
    
    public function continue() {
        
        if ( !parent::continue() ) {
            return false;
        }
        
        if ( $this->local_session_status === 0 ) {
            /* Если локаьная сессия помечена устаревшей */
            $result = DB::query("SELECT `sid`, `session_create`, `user_hash` FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$this->gbl_sid}' AND `session_status` = 1");
        
            if ( $result === false || $result->num_rows !== 1 ) {
                /* Чёт странное */
                DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$this->gbl_sid}'");
                
                parent::destroy();
                
                return false;
            }
            
            $session = $result->fetch_assoc();
            
            if ( $session[ 'user_hash' ] != static::getCurUserHash() ) {
                /* Подозрительно, новый новый пользователь откуда то */
                DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$this->gbl_sid}'");
                
                parent::destroy();
                
                return false;
            }
            
            $actualLocalSID = $session[ 'sid' ];
            
            setcookie( SESSION_LOCAL_NAME, $actualLocalSID, $this->gbl_session_create + SESSION_GLOBAL_MAX_LIVE, '/', MAIN_DOMAIN, USE_SSL, true);
        } else {
            if ( $this->user_hash !== static::getCurUserHash() ) {
                static::regenLocalSID();
            } else if ( $this->local_session_create + SESSION_LOCAL_MAX_LIVE < time() ) {
                static::regenLocalSID();
            }
        }
        
        return true;
        
    }
    
    /* Удаление локальной сессии */
    public function destroy() {
        DB::query("DELETE FROM `%PREFIX%_session` WHERE `gbl_sid` = '{$this->gbl_sid}'");
        
        parent::destroy();
        
        $this->local_sid = NULL;
        $this->local_session_create = NULL;
        $this->local_session_status = NULL;
        $this->user_hash = NULL;
        $this->user_csrf = NULL;
    }
    
    /* Генерирование нового локального SID */
    private function regenLocalSID() {
        $newSID = static::genLocalSID();
        if ( $newSID === false ) {
            $this->destroy();
            
            return false;
        }
        
        DB::query("UPDATE `%PREFIX%_session` SET " . 
                         "`session_status`=0, `session_create`=" . time() . 
                         " WHERE `sid` = '" . $this->local_sid . "'");
        
        DB::query("INSERT INTO `%PREFIX%_session`(`sid`, `gbl_sid`, `session_create`, `session_status`, `user_hash`, `user_csrf` ) " .
                         "VALUES ('" . $newSID . "','" . $this->gbl_sid . "'," . time() . ",1,'" . static::getCurUserHash() . "','" . static::genCSRF() . "')");
        
        setcookie( SESSION_LOCAL_NAME, $this->local_sid, $this->gbl_session_create + SESSION_GLOBAL_MAX_LIVE, '/', MAIN_DOMAIN, USE_SSL, true);
        
        return true;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getLastTime() {
        return $this->gbl_session_last_time;
    }
    
    public function getLastIP() {
        return static::decodeIP($this->gbl_session_last_ip);
    }
    
    public function getSID() {
        return $this->local_sid;
    }
    
    public function getCSRF() {
        return $this->user_csrf;
    }
    
    private static function genCSRF() {

        return md5( bin2hex( random_bytes( 30 ) ) );

    }
    
    private static function genLocalSID() {
        
        while ( true ) {
            $session_id = substr( bin2hex( random_bytes( SESSION_KEY_LENGTH ) ), 0, SESSION_KEY_LENGTH );
            
            $result = DB::query("SELECT `sid` FROM `%PREFIX%_session` WHERE `sid` = '$session_id'");
            
            if ( $result === false ) {
                return false;
            }
            
            if ( $result->num_rows !== 0 ) {
                continue;
            }
            
            return $session_id;
        }
        
    }
    
    private static function getCurUserHash() {
        
        return md5( $_SERVER['REMOTE_ADDR'] );
        
    }
}