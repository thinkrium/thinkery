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
        
        $this->set_condensed_url_db();
    }

    /*
     * grabs the url array from the module management to apply the sql level 
     * modification including region position index
     */
    public function get_module_management_urls() {
        $this->region_urls = $this->module_management->getUrlArray();
    }    
    
    public function set_condensed_url_db() {
       $stmt = $this->connection->prepare('select * from regions');   

       $stmt->execute();
       
       $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
       $this->condensed_url_db = $results;
    }
  
    /*
     * returns the url array for processing in the router
     */
    public function getUrlArray() {
       return $this->condensed_url_db;  
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
        
        $region_parameters = array(); 

        $region_parameters['region_title'] = '';                        
       
        $region_parameters['region_object'] = '';                        
       
        $region_parameters['region_location'] = '';                        
        
        $region_parameters['region_function'] = '';                        
        
        $region_parameters['region_view'] = '';                        
        
        $region_parameters['region_validate'] = '';                        
        
        $region_parameters['region_submit'] = '';                        
                        
        
        $regions_size = count($this->condensed_url_code);
        
        $size_query = "select count(region_id) from regions";
        
        
        $insert_regions_query = 'insert into regions (url, region_title,'
                . ' region_location, region_object, region_view, region_function, '
                . ' region_validate, region_submit, position_index) '
                . ''
                . 'values '
                . ''
                . '(:url, :region_title, :region_location, :region_object, :region_view, '
                . ':region_function, :region_validate, :region_submit, :position_index)';
        
        $stmt = $this->connection->prepare($size_query);

        $stmt->execute();
    
        $region_in_db = (int)$stmt->fetch(PDO::FETCH_COLUMN);
       
//        if($region_in_db != $regions_size) {

            try {
               $this->connection->query("truncate regions");
               
                $this->connection->beginTransaction();
       
                foreach($this->condensed_url_code as $condensed_urls) {
                    $url = key($condensed_urls);
 
                    $stmt = $this->connection->prepare($insert_regions_query);
             
                    if(isset($condensed_urls[$url]['region'])) {
                        $region_parameters['region_title'] = $condensed_urls[$url]['region'];                        
                    }
                    else {
                        $region_parameters['region_title'] = null;                        
                        
                    }

                    if(isset($condensed_urls[$url]['location'])) {
                        $region_parameters['region_location'] = $condensed_urls[$url]['location'];                        
                        
                    }
                    else {
                        $region_parameters['region_location'] = null;                        
                        
                    }
             
                    if(isset($condensed_urls[$url]['object'])) {
                        $region_parameters['region_object'] = $condensed_urls[$url]['object'];                        
                        
                    }
                    else {
                        $region_parameters['region_object'] = null;                        
                        
                    }
             
                    if(isset($condensed_urls[$url]['view'])) {
                        $region_parameters['region_view'] = $condensed_urls[$url]['view'];                        
                        
                    }
                    else {
                        $region_parameters['region_view'] = null;                        
                        
                    }
             
             
                    if(isset($condensed_urls[$url]['function'])) {
                        $region_parameters['region_function'] = $condensed_urls[$url]['function'];                        
                        
                    }
                    else {
                        $region_parameters['region_function'] = null;                        
                        
                    }
             
                    if(isset($condensed_urls[$url]['validate'])) {
                        $region_parameters['region_validate'] = $condensed_urls[$url]['validate'];                        
                        
                    }
                    else {
                        $region_parameters['region_validate'] = null;                        
                        
                    }

                    if(isset($condensed_urls[$url]['submit'])) {
                        $region_parameters['region_submit'] = $condensed_urls[$url]['submit'];                        
                        
                    }
                    else {
                        $region_parameters['region_submit'] = null;                        
                        
                    }

                                 
                $stmt->bindValue(':url', $url);
                $stmt->bindValue(':region_title', $region_parameters['region_title']);
                $stmt->bindValue(':region_location', $region_parameters['region_location']);
                $stmt->bindValue(':region_object', $region_parameters['region_object']);
                $stmt->bindValue(':region_view', $region_parameters['region_view']);
                $stmt->bindValue(':region_function', $region_parameters['region_function']);
                $stmt->bindValue(':region_validate', $region_parameters['region_validate']);
                $stmt->bindValue(':region_submit', $region_parameters['region_submit']);
                $stmt->bindValue(':position_index', 0);


                $stmt->execute();
                }
                
            $this->connection->commit();
         
           }
           catch(PDOException $e) {
               exit(var_dump($e));
           }
        //}
            
    }
        
    public function dynamicRegionAdd($params) {
        
        $url = array();
        
        $url['*'] = $params;
        
    }
    
    
}
