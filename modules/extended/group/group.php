<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of group
 *
 * @author thomedy
 */
class group {
    //put your code here
    
    private $databaseConnection;
    
    private $session;
    
    private $errors;


    public function __construct($connection, $session, $errors) {

        $this->databaseConnection = $connection;

        $this->session = $session;
        
        $this->errors = $errors;
        
    }
    
    public function groupAddUrl() {
        $url = array();
        
        $url[] = array('groups' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => '',
            'function' => 'groups_show',
            'view' => 'groups_show.view',
            )
        );

        
        $url[] = array('group/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'groupsAdd',
            'function' => 'group_add',
            'validate' => 'group_add_validate',
            'submit' => 'group_add_submit',
            'view' => 'group_add.view',
            )
        );

        $url[] = array('group/!id' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'group_id_show',
            'function' => 'group_id_show',
            'view' => 'group_id_show.view',
            )
        );
        
        $url[] = array( 'group/!id/communications' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'group_id_communications',
            'function' => 'group_id_communications',
            'validate' => 'group_id_communications_validate',
            'submit' => 'group_id_communications_submit',
            'view' => 'group_id_communications.view',
            )
        );
        
        $url[] = array('group/!id/members' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'group_id_members',
            'function' => 'group_id_members',
            'view' => 'group_id_communications.view',
            )
        );
        
        $url[] = array('group/!id/members/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'group_id_members_add',
            'function' => 'group_id_members_add',
            'validate' => 'group_id_members_add_validate',
            'submit' => 'group_id_members_add_submit',
            'view' => 'group_id_members_add.view',
            )
        );
        
       
        return $url;
        
    }
    
    public function group_id_members($params) {
        
        $group_id = $params['!id'];
        
        $stmt = $this->databaseConnection->prepare('select * from groups where group_id = :group_id');
        $stmt->bindParam(':group_id', $group_id );
        $stmt->execute();
        
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return "fix the query to be  a join";
        
    }
            
    public function group_get_permissions() {
            
        $perm = array();
            
        $perm['group_add'] = array(
            'label' => "Add a group",
            'function' => "group_add"
        );
            
        $perm['groups_show'] = array(
            'label' => "See all the groups in a view",
            'function' => "groups_show"
        );
            
        return $perm;
    }
    
    public function groups_show() {

        $stmt = $this->databaseConnection->prepare('select * from groups');
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
    
    public function group_id_show($params) {

        $group_id = $params['!id'];
        
        $stmt = $this->databaseConnection->prepare('select * from groups where group_id = :group_id');
        $stmt->bindParam(':group_id', $group_id );
        $stmt->execute();
        
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $results;
        
    }

}

