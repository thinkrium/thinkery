<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of errors
 *
 * @author thomedy
 */
class errors extends Exception{
    //put your code here
    
    /*
     * not sure what i am going to do here.
     * at all!
     */
    
    private $errors = [];
    
    /*
     * db holds the injected database connection
     * for quering
     */
    private $db;
    
    /*
     * the constructor is called and injected with a database connection
     *
     */
    public function __construct($connection ){
        $this->db = $connection;
        
    }
    
    /*
     * errorAddUrl is the url api add so that you can grab the url array and 
     * merge the array together for use in the controller.php
     *
     */
    public function errorAddUrl($params = null) {
        
        $url = array();

        $url[] = array('error/log' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'error_log',
            'function' => 'display_current_errors',
            'view' => 'error_log.view',
            )
        );
        
        return $url;
        
                
    }
    
    /*
     * auth_id is > 0 if user, = 0 if anonymous < 0 if system
     */
    
    public function set_error($auth_id, $message) {
        
        if($auth_id > 0) {
            
            $stmt = $this->db->prepare("insert into error_log (aid, message) values (:aid, :message)");
        
            $stmt->bindParam(':aid', $auth_id);
        
            $stmt->bindParam(':message', $message);
        
            $stmt->execute();
        }
        else if($auth_id == 0) {
            
            $_SESSION['anonymous_user_errors'][] = $message; 
        }            
    }
    
    /*
     * display_current_errors sets teh error array for conditional use in build page
     *
     */
    public function display_current_errors() {
        
       $error = array();
        
       if(false) {
           
           $error['info'] = "errors exist";
       }
       else {

           $error['info'] = "The error log is currently empty!";
       }

       return $error;
    }
    
    /*
     *  still not sure how i want to do this
     *
     */
    public function show_error() {
        
    }
    
    /*
     * add_error does what it says it adds an error to the session variable error_count for
     * checking in the initialization to determine if an error message should be displayed
     *
     */
     
    public function add_error() {
        $_SESSION['error_count']++;
    }

    /*
     * errors_exist checks if errors exist and return true
     *
     */
    public function errors_exist() {
        if($_SESSION['error_count'] > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * create_errors is wher eit checks for the error count to exist and if it doesn't 
     * exist then it creates the error system
     *
     * clear errors does the same as create errors but it doest it at different times
     *
     */
    public function create_errors() {
        if(!isset($_SESSION['error_count'])) {
             $_SESSION['error_count'] = 0;
             $_SESSION['anonymous_user_errors'] = NULL;
             $_SESSION['anonymous_user_errors'] = array();

        }
    }

    /*
     * create_errors is wher eit checks for the error count to exist and if it doesn't 
     * exist then it creates the error system
     *
     * create errors does the same as clear errors but it doest it at different times
     *
     */
    
    public function clear_errors() {
             $_SESSION['error_count'] = 0;
             $_SESSION['anonymous_user_errors'] = NULL;
             $_SESSION['anonymous_user_errors'] = array();
    }
    

    /*
     *
     * get_error_count returns the count on errors
     *
     */
    public function get_error_count() {
        
        return $_SESSION['error_count'];
    }
    
    
    /*
     * the error_get_permissions allows anyone who has perms to see the errors page
     * 
     */
    public function error_get_permissions() {
        
        $permissions = array();
        
        $permissions['error_log'] = array(
            'label' => "See the error log",
            'function' => 'error_log'            
        );
        
        return $permissions;
    }
        
}
