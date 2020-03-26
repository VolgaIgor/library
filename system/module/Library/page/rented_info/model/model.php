<?php

class ModelRentedInfoModel extends Model {
    
    public function getRentInfo( int $id ) {
        $sql = "SELECT * FROM `log_lease` WHERE `id` = {$id}";
        
        $result = DB::query( $sql );
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            
            return $row;
        }
        
        return null;
    }
    
    public function getOverdueDay( int $id ) {
        $sql = "SELECT `GET_DAY_RENT_OVERDUE`({$id}) AS `count`;";
        
        $result = DB::query( $sql );
        if ( $result !== null && $result->num_rows === 1 ) {
            $row = $result->fetch_assoc();
            
            return $row['count'];
        }
        
        return 0;
    }
    
    public function returnRent( int $id ) {
        $sql = "CALL RETURN_RENT_BOOK({$id});";
        
        $result = DB::query( $sql );

        if ( !empty( $result ) ) {
            return true;
        }
        
        return false;
    }
    
}