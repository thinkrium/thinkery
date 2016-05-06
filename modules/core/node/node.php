<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of node
 *
 * @author thomedy
 */
class node {
    
    private $connection;

    private $session;
    
//    private $errors;

    
    public function __construct($db_connection, $session, $errors) {
        
        $this->connection = $db_connection;
        
        $this->session = $session;
        
//        $this->errors = $errors;
    }
    
    public function nodeAddUrl($params = null) {
        
        $url = array();
        
        $url[] = array('welcome' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'welcome',
            'view' => 'home.view',
            'function' => 'welcome',
            )
        );
        
        
        $url[] = array('node' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => '',
            'view' => 'node.view',
            'function' => 'node_view',
            )
        );
        
        $url[] = array('node/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => '',
            'view' => 'node_add.view',
            'function' => 'node_add',
            'validate' => 'node_add_validate',
            'submit' => 'node_add_submit',
            )
        );


        $url[] = array('node/types' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => '',
            'view' => 'node_types.view',
            'function' => 'node_types',
            )
        );

        $url[] = array('node/type/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => '',
            'view' => 'node_type_add.view',
            'function' => 'node_type_add',
            'validate' => 'node_type_add_validate',
            'submit' => 'node_type_add_submit',
            )
        );
        return $url;
    }
    
    public function node_get_permissions() {
    
        $permissions = array();
        
        $permissions['add_node'] = array(
            'label' => 'add a node',
            'function' => 'node_add',
        );

        $permissions['node_type_add'] = array(
            'label' => 'Create a new content type',
            'function' => 'node_type_add',
        );
        

        return $permissions;
    }
    
    public function welcome() {
        
        
    }
    
    public function node_types() {
        
    } 
    
    public function node_type_add() {
        
    } 
    
    public function node_type_add_validate() {
        
    } 
    
    public function node_type_add_submit() {
        
    } 
    
}
