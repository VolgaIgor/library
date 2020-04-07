<?php
    ini_set( 'display_errors', true );
    
    /* DB SETTING */
    define( 'DB_HOST', 'localhost' );
    define( 'DB_USERNAME', 'bd_user' );
    define( 'DB_PASSWORD', 'bd_pass' );
    define( 'DB_NAME', 'bd_db' );
    
    define( 'PROJECT_NAME', 'Либрару' );
    define( 'PROJECT_PREFIX', 'lb' );
    define( 'MAIN_DOMAIN', 'library.xn--90aihhxfgb.xn--p1ai' );
    define( 'USE_SSL', false );
    
    define( 'MAIN_THEME', 'main' );
    
    /* SESSION SETTING */
    define( 'SESSION_LOCAL_NAME', 'SIDLBLK' );

    define( 'SESSION_GLOBAL_MAX_LIVE', 31536000 ); // One year
    define( 'SESSION_GLOBAL_MAX_INACTIVE', 5184000 ); // 60 days
    define( 'SESSION_GLOBAL_LAST_TIME_RANGE', 60 );
    define( 'SESSION_GLOBAL_NAME', 'SIDLBGBL' );
    define( 'SESSION_GLOBAL_AUTH_LIVE', 30 );
    
    define( 'SESSION_OLD_LOCAL_MAX_LIVE', 60 );
    define( 'SESSION_LOCAL_MAX_LIVE', 604800 ); // One weekend
    
    define( 'SESSION_KEY_LENGTH', 32 );
    
    $USER_RIGHT = array();
    
    $USER_RIGHT['banned']['login'] = false;
    $USER_RIGHT['banned']['can_lease'] = true;
    
    $USER_RIGHT['user']['login'] = true;
    $USER_RIGHT['user']['can_lease'] = true;
    
    $USER_RIGHT['root']['login'] = true;
    $USER_RIGHT['root']['admin_login'] = true;
    
    date_default_timezone_set( 'Europe/Moscow' );
    
    URL::addPage('/404', '/page/error', 404);
    URL::addPage('/500', '/page/error', 500);
    URL::addPage('/', '/page/main');
    URL::addAdmin('', '/admin/main');
    URL::addAdmin('/sql', '/admin/sql');
    
    Loader::loadModule( 'SSO' );
    const SSO_TRUST_URL = array(
        'library.xn--90aihhxfgb.xn--p1ai' => [
            'prefix' => 'lb',
            'name' => 'Библиотека Либрару',
            'url' => 'library.киберпро.рф'
        ]
    );
    
    Loader::loadModule( 'Library' );