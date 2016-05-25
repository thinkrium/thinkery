<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of regions
 *
 * @author thomedy
 */
class regions {
    //put your code here
    
    private $db_connection;
    
    public function __construct($connection, $session) {
        $this->db_connection = $connection;
    }
    
    /*
     * adds urls for pages to exist
     */
    public function regionsAddUrl($params = null) {

        $regions = array();
        
        $regions[] = array( 'regions/manage' => array(
            'location' => __DIR__,
            'object' => __CLASS__,
            'view' => 'regions_manage.view',
            'region' => 'regionManage',
            'function' => 'regions_manage',
            'validate' => 'regions_manage_validate',
            'submit'  => 'regions_manage_submit'
            )
        );

        $regions[] = array( 'region/add' => array(
            'location' => __DIR__,
            'object' => __CLASS__,
            'view' => 'regions_add.view',
            'region' => 'regionsAdd',
            'function' => 'add_region',
            'validate' => 'add_region_validate',
            'function' => 'add_region_submit',
            )
        );
        
        return $regions;
        
    }
    
    public function regions_get_permissions() {
       $perms = array();
       
       $perms['region_add'] = array(
           'label' => 'Add Regions',
           'function' => 'add_regions'
       );        

        $perms['regions_manage'] = array(
           'label' => 'Manage Regions',
           'function' => 'manage_regions'
         );        
        
         return $perms;

    }
    
    public function add_region() {
        
    }
    
    public function add_region_validate() {
        
        
    }
    
    public function add_region_submit() {
        
        
    }
    
    public function regions_test() {
        
    }

    /*
     * this functin is to set up the regions for a gui manipulation
     */    
    public function regions_manage() {
        
        
        $stmt = $this->db_connection->prepare('select * from regions order by position_index ASC');
        
        $stmt->execute();
        
        $results['regions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $this->db_connection->prepare('select * from region_containers');
        
        $stmt->execute();
        
        $results['region_containers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
    
    public function regions_manage_validate($params) {
        return true;
    }
    
    public function regions_manage_submit($params) {

        $connection = $params['connection'];
        
        $container_name = '';
                 
        foreach($params as $key => $parameters) {

            $found_containers = preg_match('/container_\d+/', $key, $container_matches); 

            $found_position = preg_match('/position_index_\d+/', $key, $position_index_matches); 

            if($found_containers) {
                
                $container_name[] = $parameters;
                
            }

            if($found_position) {
                
                $position_index[] = $parameters;
                
            }

        }

        $session_object = $params['session_object'];
        
        $connection = $params['connection'];
        

        $region_object =  new $params['object_name']($connection, $session_object);
        
        $region_object->sort_position_index($position_index, $region_object, null);
        
        $query_update = 'update regions set reg_cont_id=:reg_cont_id where region_id = :region_id';
        
        $connection->beginTransaction();

        foreach($container_name as $container) {
 
            $stmt = $connection->prepare($query_update);
            if(!is_array($container)) {
                $sql_parameters = explode('|', $container);
            }
                
            $cont_id = $sql_parameters[1];
            $reg_id = $sql_parameters[3];
                
            $stmt->bindValue(":reg_cont_id", $cont_id);
            $stmt->bindValue(':region_id', $reg_id);

            $stmt->execute();            
        }
        
        $query_update = 'update regions set position_index=:position_index where region_id = :region_id';

        for($i = 0; $i < count($position_index); $i++) {
            $current_region_id = key($position_index[$i]);
            
            $modified_position = $position_index[$i][$current_region_id];
            
            $stmt = $connection->prepare($query_update);
            $stmt->bindValue(":position_index", $modified_position);
            $stmt->bindValue(':region_id', $current_region_id);
            
            
            $stmt->execute();
            
        }
        
    $connection->commit();
    }
    
    /*
     * calls recursively the sort algorithm which compresses and orders the position index
     * and then returns the results;
     * 
     * maintains state with current object
     * 
     * eventually hope to remove that for future api
     * 
     * $region is the passed in array to sort through
     * 
     * $smallest_number is the array to populate
     * 
     * $flags is future api stuff
     */
    public function sort_position_index($region, $current_object, $smallest_number = null, $stop = null) {
        
        if($smallest_number == null) {
            $stop = count($region);
        }
        /*
         * if its the first call to the sort position index  then the smallest_number
         * array will be  null
         * 
         * if that is the case then you create the array 
         * 
         */
        if($smallest_number == null) {
            
            $smallest_number = array();
            
        }

        /*
         * the top level of the smallest number array will be initiated to
         * null every time 
         */
        $smallest_number[] = null;
 
        /*
         * top level will be the size of the smallest number array - 1
         */
        $top_level = count($smallest_number) - 1;
        
        /*
         * region_key will be initiated to null for testing
         */
        $region_number_key = null;

        foreach($region as $ind => $reg) {

             
            if($region_number_key == null) {

                $region_number_key = key($reg);
                          
                $region_number_value = $reg[$region_number_key];
                $top_level_region = $ind;

            }
            else if(
                    $region[$previous_number_ind][$previous_number_key] >= $reg[key($reg)]
                ){
                $region_number_key = key($reg);
                 $region_number_value = $reg[key($reg)];
                $top_level_region = $ind;
            }
            
            $previous_number_key = key($reg);
            $previous_number_ind = $ind;
        }

        $smallest_number[$top_level][$region_number_key] = $region_number_value;

        if(isset($region[$top_level_region])) {
            unset($region[$top_level_region]);
        } 
        
        $str = count($region);        
        if(count($smallest_number) ==  $stop) {
            return $smallest_number;
        }
        else {
            $current_object->sort_position_index($region, $current_object, $smallest_number, $stop);
        }
        
    }
}