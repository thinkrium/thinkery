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
    
    private $condensed_url_code;
    
    private $condensed_url_db;

    public function __construct($connection, $moduleManagement, $session_object, $error_obect) {
        
        $this->connection = $connection;
        
        $this->module_management = $moduleManagement;
        
        $this->session = $session_object;
        
        $this->error = $error_obect;
        
        $this->get_module_management_urls();
        
        $this->condense_url_array();
    }

    public function get_module_management_urls() {
        $this->region_urls = $this->module_management->getUrlArray();
    }    
    
    public function set_condensed_url_db() {
        
    }
  
    public function getUrlArray() {
       return $this->condensed_url_code;  
    }
  
    public function condense_url_array() {
        foreach ($this->region_urls as $urls) {
            if(is_array($urls)) {
                foreach ($urls as $url) {
                    $this->condensed_url_code[] = $url;
                }
            }   
        }
    }
    
    public function set_db_regions() {
        foreach($this->condensed_url_code as $c) {
            
            print_r($c, true);
            print "</br>";
        }
        
    }
    
    public function dynamicRegionAdd($params) {
        
        $url = array();
        
        $url['*'] = $params;
        
    }
    
    
}
