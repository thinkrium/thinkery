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

    /*
     * sets the region management and calls the methods that need less dynamic 
     * modification
     */
    public function __construct($connection, $moduleManagement, $session_object, $error_obect) {
        
        $this->connection = $connection;
        
        $this->module_management = $moduleManagement;
        
        $this->session = $session_object;
        
        $this->error = $error_obect;
        
        $this->get_module_management_urls();
        
        $this->condense_url_array();
        
        $this->instantiate_db_regions();
    }

    /*
     * grabs the url array from the module management to apply the sql level 
     * modification including region position index
     */
    public function get_module_management_urls() {
        $this->region_urls = $this->module_management->getUrlArray();
    }    
    
    public function set_condensed_url_db() {
        
    }
  
    /*
     * returns the url array for processing in the router
     */
    public function getUrlArray() {
       return $this->condensed_url_code;  
    }
  
    /*
     * condense the multi dimensional url array into one single tiered array where 
     * its easier to iterate with less loops
     */
    public function condense_url_array() {
        foreach ($this->region_urls as $urls) {
            if(is_array($urls)) {
                foreach ($urls as $url) {
                    $this->condensed_url_code[] = $url;
                }
            }   
        }
    }
    
    /*
     * instantiate db regions sets the initial db regions for extraction in the 
     * it checks if there are the exact amount of regions in the db as in the code
     * and if so do nothing
     * 
     * if there is a different number then reset it
     * 
     */
    public function instantiate_db_regions() {
        
        
       $regions_size = count($this->condensed_url_code);
       
       $size_query = "select count(region_id) from regions";
       
       $insert_regions_query = 'insert into regions (region_title, region_location, region_function, "
               . " region_validate, region_submit, region_object, position_index
               ") values (:region_title, :region_location, :region_function, "
               . " :region_validate, :region_submit, :region_object, :position_index)';
       
       $stmt = $this->connection->prepare($size_query);

       $stmt->execute();
       
       $region_in_db = (int)$stmt->fetch(PDO::FETCH_COLUMN);
       
       if($region_in_db != $regions_size) {
           
           try {
               $this->connection->query("delete * from regions");
               
               $this->connection->beginTransaction();
       
                foreach($this->condensed_url_code as $condensed_urls) {
 
                    $stmt = $this->connection->prepare($insert_regions_query);
             
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_title", 'test');
                        
                    }
                    else {
                        
                    }
             
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_location", 'test');
                        
                    }
                    else {
                        
                    }
             
             
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_function", 'test');
                        
                    }
                    else {
                        
                    }
             
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_validate", 'test');
                        
                    }
                    else {
                        
                    }

                                 
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_title", 'test');
                        
                    }
                    else {
                        
                    }
             
                    if(isset($condensed_urls['regions'])) {
                        $stmt->bindValue(":regions_title", 'test');
                        
                    }
                    else {
                        
                    }
             

                    $stmt->execute();
                }
                
            $this->connection->commit();

           }
           catch(PDOException $e) {
               exit(var_dump($e));
           }
        }
            
    }
        
    public function dynamicRegionAdd($params) {
        
        $url = array();
        
        $url['*'] = $params;
        
    }
    
    
}
