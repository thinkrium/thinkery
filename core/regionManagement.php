<?php


/*
 * region management is part of the ui. This is the management class
 * manipulating the database instruction of the region location and 
 * even creation. But this is region homebase
 * 
 */ 

  class regionManagement {
 
    private $connection;
    
    private $module_management; 
    
    private $session;
    
    private $error;
    
    public function __construct($connection, $moduleManagement, $session_object, $error_obect) {
        
        $this->connection = $connection;
        
        $this->module_management = $moduleManagement;
        
        $this->session = $session_object;
        
        $this->error = $error_obect;
        
    }
    
    public function dynamicRegionAdd($params) {
        
        $url = array();
        
        $url['*'] = $params;
        
    }
    
    
}
