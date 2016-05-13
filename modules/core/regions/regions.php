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
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
}
