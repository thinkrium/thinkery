<?php


class databaseConfig {

    /*
     * the db_name is $db_name
     * 
     * the db_owner is $db_owner
     * 
     * the db_pass is $db_pass;
     */

    
    
    /*
     * take in the database parameters 
     * 
     * needs keys databaseName, userName, password
     */
    public function __construct() {
        
        
    }

 
    public function connection() {
        
        $databaseName = $this->db_name;
        
        $connectionQuery = "mysql:dbname=" . $databaseName . ";host=127.0.0.1";

        
        try {
            $databaseConnection = new PDO($connectionQuery, $this->db_owner, $this->db_pass);
            return $databaseConnection;
        }
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

         
    }
}

