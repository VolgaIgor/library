<?php

function numberDeclension( $number, $root, $prefixArr ) {
    $number = abs( (int)$number );
    if ( !is_array($prefixArr) )
        return '';
    if ( $number % 10 == 1 && $number % 100 != 11 ) {
        return $root . $prefixArr[1];
    } else if ( $number % 10 >= 2 && $number % 10 <= 4 && (int)( ( $number % 100 ) / 10 ) != 1 ) {
        return $root . $prefixArr[2];
    } else {
        return $root . $prefixArr[0];
    }
}

function dateDiff( $startDate, $endDate ) {
    $startDate = DateTime::createFromFormat('U', (int)$startDate);
    $endDate = DateTime::createFromFormat('U', (int)$endDate);
    $diff = $startDate->diff($endDate);
    
    $prefix = [
        'year' => [ '', [ 'лет', 'год', 'года' ] ],
        'month' => [ 'месяц', [ 'ев', '', 'а' ] ],
        'day' => [ 'д', [ 'ней', 'ень', 'ня' ] ],
        'hours' => [ 'час', [ 'ов', '', 'а' ] ],
        'minute' => [ 'минут', [ '', 'а', 'ы' ] ],
        'second' => [ 'секунд', [ '', 'а', 'ы' ] ]
    ];
    
    if ( $diff->format('%y') != '0' ) {
        $date = $diff->format('%y ') . numberDeclension( $diff->format('%y'), $prefix['year'][0], $prefix['year'][1] );
        if  ( $diff->format('%m') != '0' ) {
            $date .= $diff->format(' %m ') . numberDeclension( $diff->format('%m'), $prefix['month'][0], $prefix['month'][1] );
        }
    } else if ( $diff->format('%m') != '0' ) {
        $date = $diff->format('%m ') . numberDeclension( $diff->format('%m'), $prefix['month'][0], $prefix['month'][1] );
        if  ( $diff->format('%d') != '0' && $diff->format('%m') == '1' ) {
            $date .= $diff->format(' %d ') . numberDeclension( $diff->format('%d'), $prefix['day'][0], $prefix['day'][1] );
        }
    } else if ( $diff->format('%d') != '0' ) {
        $date = $diff->format('%d ') . numberDeclension( $diff->format('%d'), $prefix['day'][0], $prefix['day'][1] );
        if  ( $diff->format('%h') != '0' && $diff->format('%d') == '1' ) {
            $date .= $diff->format(' %h ') . numberDeclension( $diff->format('%h'), $prefix['hours'][0], $prefix['hours'][1] );
        }
    } else if ( $diff->format('%h') != '0' ) {
        $date = $diff->format('%h ') . numberDeclension( $diff->format('%h'), $prefix['hours'][0], $prefix['hours'][1] );
        if  ( $diff->format('%i') != '0' && (int)$diff->format('%h') < 3 ) {
            $date .= $diff->format(' %i ') . numberDeclension( $diff->format('%i'), $prefix['minute'][0], $prefix['minute'][1] );
        }
    } else if ( $diff->format('%i') != '0' ) {
        $date = $diff->format('%i ') . numberDeclension( $diff->format('%i'), $prefix['minute'][0], $prefix['minute'][1] );
        if  ( $diff->format('%s') != '0' && (int)$diff->format('%i') < 10 ) {
            $date .= $diff->format(' %s ') . numberDeclension( $diff->format('%s'), $prefix['second'][0], $prefix['second'][1] );
        }
    } else {
        $date = $diff->format('%s ') . numberDeclension( $diff->format('%s'), $prefix['second'][0], $prefix['second'][1] );
    }
    return $date;
}

function getURL() {
    return ( (USE_SSL) ? 'https://' : 'http://' ) . MAIN_DOMAIN;
}