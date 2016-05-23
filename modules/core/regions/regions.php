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
    
    public function sort_position_index(&$region, $current_object, $smallest_number = null, $flags = null) {
        
        if($smallest_number == null) {
            $smallest_number = array();
            $smallest_number[] = '';
            
        }
        else {
            $smallest_number[] = '';
            $last_built_key = key($smallest_number[count($smallest_number) - 2]);
        }
        
        $smallest_number_key = '';
        
        $last_small_number_key = '';
        
        $smallest_number_top_level = count($smallest_number) - 1;
        
        for($i = 0; $i < count($region); $i++) {
            
            if($smallest_number_key == '') {            

                $smallest_number_key = key($region[$i]);
                $last_small_number_key = $smallest_number_key;
//                exit(var_dump($smallest_number_key, $last_small_number_key));

            }
            else {
                $smallest_number_key = key($region[$i]);
            }
                  
            $region_number_key = key($region[$i]);
           
            /*
             * this is the first iteratoin of the recursive function and the smallest number array is only the first element
             */
            if($smallest_number[$smallest_number_top_level] == '') {

                $smallest_number[$smallest_number_top_level][$smallest_number_key] = 
                       $region[$i][$region_number_key];

            }
            
            /*
             * this is not the first iteration of the recursive function and the key of the input from the form
             * is not set yet
             */
//           else if(!isset($smallest_number[$smallest_number_top_level][$last_small_number_key]) ///&& 
       //          ($smallest_number[$smallest_number_top_level][$last_small_number_key] > (int)$region[$i][$region_number_key])  
//                   ) {
//               unset($smallest_number[$smallest_number_top_level]);
//               $smallest_number[$smallest_number_top_level][$region_number_key] = 
//                       $region[$i][$region_number_key];
//                                   if($smallest_number_top_level == 0) {
//                exit(var_dump($smallest_number, $region,  $smallest_number_key, $smallest_number_top_level));

//           }
               
//          }
           /*
            * this is not the first iteration of the recursive function and the key of the input from the form element is 
            * set and now your just comparing it to the array through the calls to the recursive function
            */
           else if(
                   (isset($smallest_number[$smallest_number_top_level][$last_small_number_key]))
                    &&
                   ($smallest_number[$smallest_number_top_level][$last_small_number_key] > (int)$region[$i][$region_number_key])

                     &&
                   ($region_number_key != $last_built_key)
                            
                   )
                  {

               unset($smallest_number[$smallest_number_top_level][$last_small_number_key]);

               $smallest_number[$smallest_number_top_level][$region_number_key] = 
                       $region[$i][$region_number_key];
           }

        }
             if($smallest_number_top_level == 2) {
                exit(var_dump("check", $smallest_number, $region,  $smallest_number_key, $smallest_number_top_level));

           }

        if(count($region) != count($smallest_number)) {        
            
            $current_object->sort_position_index($region, $current_object, $smallest_number, 0);
        }
        else {
//                exit(var_dump($smallest_number, $region,  $smallest_number_key, $smallest_number_top_level));
            
        }
        
    }
}
