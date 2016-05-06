<?php

/*
 *
 * potential module for use in the future. Honestly im not sure where im going with this quite yet
 * 
 */
 
class module {
    
    protected $dbConnection;
    //put your code here
    public function __construct() {
        
    }
    
    public function setDatabaseConnection( $connection, $session ) {
        $this->dbConnection = $connection;
        
    }
}
