<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of session
 *
 * @author thomedy
 */
class session {
    //put your code here
    
    public function _construct() {
        
        
    }
    
    /*
     * create the user profile done in init
     * 
     */
    public function create_user_profile() {
/*        if(isset($_SESSION['user'])) {
            $_SESSION['user'] = array();
        }    
*/    }
    
    /*
     * set the user id in session to uid
     */
    public function set_uid($uid) {
        
        $_SESSION['user']['id'] = (int)$uid;
    }
    
    /*
     * RETURNs the session uid for use
     */
    public function get_uid() {
        
        return $_SESSION['user']['id'];
    }
    
    /*
     * display the session variable contents
     */
    public function display_session() {
        var_dump($_SESSION);
    }
    
    /*
     * does the user id exist
     */
    public function user_id_exists() {
        if(isset($_SESSION['user']['id'])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function user_is_anonymous() {
        
        if($_SESSION['user']['id'] < 1) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function end_session() {
        session_destroy();
    }
}
