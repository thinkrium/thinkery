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
        
        
        $stmt = $this->db_connection->prepare('select * from regions');
        
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

            $found_containers = preg_match('/[container_]\d+/', $key, $container_matches); 

            $found_position = preg_match('/[position_index_]\d+/', $key, $position_index_matches); 

            if($found_containers) {
  //              $container_index = "container_" . $container_matches[0];
                
                $container_name[] = $parameters;
                
            }

            if($found_position) {
//                $position_index = "position_index_" . $position_index_matches[0];
                
                $position_index[] = $parameters;
                
            }

        }
        
//         exit(var_dump($position_index));
        $query_update = 'update regions set reg_cont_id=:reg_cont_id where region_id = :region_id';
        
     $connection->beginTransaction();
///        exit(var_dump($container_name));
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
        
      //  foreach($position_index)
        $connection->commit();

         
    }
    
}
