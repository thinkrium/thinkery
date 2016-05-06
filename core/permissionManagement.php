<?php

/*
 * this class is only used to check the url against a permission and compare the
 * users existing permission
 * 
 */
class permissionManagement {
    
    private  $connection;
    
    private  $module_management;
    
    private  $permissions_array;
    
    private  $permited;
    
    private  $current_permissions;

    private  $flattened_perms;
    
    private  $role_id;

    private  $session;

    public function __construct($connection, $moduleManagement, $session_object) {


        $this->connection = $connection;
        
        $this->module_management = $moduleManagement;
        
        $this->permissions_array = array();
        
        $this->session = $session_object;
        
        $this->assemble_permissions();
        
        $this->set_current_users_permissions();
        
        $this->flatten_perms();

        $this->set_role_ids();
    }
    
    
    /*
     * assemble permissions goes through the modules and searches for the proper
     * permissions
     * 
     * and assembles them in an array
     * 
     */
    public function assemble_permissions() {
        
        foreach ($this->module_management->get_permissions_array() as $module)  {

            if(!is_array($module)) {

                $method = $module . "_get_permissions";
                
                if(method_exists($module, $method)) {
                    $this->permissions_array[] = call_user_func_array(array($module, $method), array());
                }
            }
        } 
    }
    
    public function find_url($perms) {
        
        foreach ($this->permissions_array as $permissions) {

            if(isset($permissions[$perms])) {
                return $permissions[$perms]['page'];
            }
        }
   
    }

    public function flatten_perms() {
        
        /*
         * iterate through the top layer of the perms from code array
         * leaving just the functions in a local variable.
         * 
         */

        
        $this->flattened_perms['code'] = array();
        $this->flattened_perms['database'] = array();
        
        $perms_from_code = $this->get_permissions_array();
        
        $perms_from_db = $this->get_current_users_permissions();
        
        foreach($perms_from_code as $p_n_c) {

            $this->flattened_perms['code'][] = array_column($p_n_c, "function");            
        }
        
        if(count($perms_from_db) > 0) {
            foreach($perms_in_code as $p_n_db) {

                $this->flattened_perms['database'][] = array_column($p_n_db, "function");            
            }
        }

        
    }
    
    
    /*
     * flattens out the coded perms and sets it to a flat array 
     * it checks if the proper permissions exist and then checks and
     * returns its valuse a boolean
     * 
     */
    public function permission_granted($function, $url) {

        $return = false;
        
        foreach ($this->flattened_perms['code'] as $perm_array_cont) {
            foreach ($perm_array_cont as $perm_array) {
               
                $perms[] = $perm_array;       
            }    
        }    
        
        if(!in_array($function, $perms)) {
            $return = TRUE;
        }
        else if(in_array($function, $perms)
                && in_array($function, $this->flattened_perms['database'])
                && $this->session->get_uid() > 1
                ) {
            $return = TRUE;
        }
        else if($this->session->get_uid() == 1) {
            $return = TRUE;
        }
        else if( 
                $this->session->get_uid() == 0
                && ($url == 'user/register' || $url == 'user/login')
                ) {
            $return = TRUE;            
        }
        else if( 
                $this->session->get_uid() > 0
                && $url == 'user/logout'
                ) {
            $return = TRUE;            
        }
        else {
            $return = FALSE;
        }
        
        
        return $return;

    }
    
    public function set_role_ids() {
        
        $uid = $this->session->get_uid();
        
        $stmt = $this->connection->prepare('select uid, role_id from user_roles where uid = :uid');
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        
        $this->role_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function get_role_ids() {
        return $this->role_id;
        
    }
    
    public function get_permissions_array() {
        return $this->permissions_array;
    }
    
    public function get_current_users_permissions() {
        return $this->current_permissions;
    }
    
    public function set_current_users_permissions() {

        /*
         * current perms grabs the current users uid and sqls the perms for each user and iterates the
         * perm array from the db to see if it is okay in the page or functionality
         */
        $stmt = $this->connection->prepare('select role_permissions.permission '
                                         . 'from user_roles join role_permissions on '
                                         . 'user_roles.role_id = role_permissions.role_id where user_roles.uid = :uid');
        $stmt->bindValue(":uid", $this->session->get_uid());
        
        $stmt->execute();        

        $this->current_permissions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'permission'); 
        
    }
}

