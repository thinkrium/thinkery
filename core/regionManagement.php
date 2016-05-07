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
    
    private $region_urls;
    
    public function __construct($connection, $moduleManagement, $session_object, $error_obect) {
        
        $this->connection = $connection;
        
        $this->module_management = $moduleManagement;
        
        $this->session = $session_object;
        
        $this->error = $error_obect;
        
        $this->get_module_management_urls();
        
        
    }

    public function get_module_management_urls() {
        $this->region_urls = $this->module_management->getUrlArray();
    }    
  
    public function getUrlArray() {
       return $this->region_urls;  
    }
  
    public function dynamicRegionAdd($params) {
        
        $url = array();
        
        $url['*'] = $params;
        
    }
    
    
}
