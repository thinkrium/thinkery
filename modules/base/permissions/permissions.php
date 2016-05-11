<?php

/*
 *
 *  The permissions object is used to build the page available to the system
 *
 *  $connection - holds the database connection
 *  
 *  $errors - holds the error system
 *
 *  $session - holds the session object for checking uid, user is anon and what not
 * 
 *
 */
 

class permissions {
    //put your code here
    
    /*
     * holds the db connection
     */
    private $connection;
    
    /*
     * holds the error object for necessary logs
     */
    private $errors;
    
    /*
     * injects the session object to make functionality like getting logged in user
     * easier
     * 
     */
    private $session;
    
    /*
     * this is the currently logged in user
     * 
     */
    private $logged_in_user;
    
    /*
     * send in the error system
     */
     
    /*
     *
     * sets up the connection for the database
     * sets up the session
     * sets up the error system 
     * 
     * all this is done with injection of the functionality
     *
     */
    
    public function __construct($connection, $session, $errors) {
        
        $this->connection = $connection;
        
        $this->session = $session;
        
   }
   
    /*
     * moduleAddUrl is the api for grabing the url array 
     * so the url array comes from permissionsUrlArray
     *
     * probably change the api name to '_' from camel casing
     * 
     */
    
    public function permissionsAddUrl($params = null) {
        
        $url = array();
        
        $url[] = array( 'permissions/manage' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'permissions_manage',
            'function' => 'permissions_manage',
            'validate' => 'permissions_manage_validate',
            'submit' => 'permissions_manage_submit',
            'view' => 'permissions_manage.view',
            )
        );

        $url[] = array( 'roles/delete' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'rolesDelete',
            'function' => 'roles_delete',
            'validate' => 'roles_delete_validate',
            'submit' => 'roles_delete_submit',
            'view' => 'roles_delete.view',
            )
        );

        $url[] = array( 'roles/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'rolesAdd',
            'function' => 'roles_add',
            'validate' => 'roles_add_validate',
            'submit' => 'roles_add_submit',
            'view' => 'roles_add.view',
            )
        );

        return $url;
    }
    
    /*
     *
     *  get_user gets the user
     */
    public function get_user($uid) {
        $this->logged_in_user = $uid;
    }
    
    /*
     * grabs the role types out of the database
     * 
     * and grabs the permissions from the function gather permissions
     * 
     * then combines them into a single associative array to send to the view
     */
    public function permissions_manage() {

        $stmt = $this->connection->prepare('select count(role_id) from role_permissions');
        
        $stmt->execute();
        
        $role_based_permissions_test = $stmt->fetch(PDO::FETCH_COLUMN);

        $existing_role_based_permissions = '';
        
        if(!(int)$role_based_permissions_test == 0) {
            $stmt = $this->connection->prepare('select * from role_permissions');
        
            $stmt->execute();
        
            $existing_role_based_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
         
        }
        
        $stmt = $this->connection->prepare('select * from role_types');

        $stmt->execute();
        
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
        $permissions = $this->gather_permissions();
        
        
        
        $return = array('roles' => $roles,
                        'permissions' => $permissions,
                        'existing_permissions' => $existing_role_based_permissions
                    );
        
        return $return;
    }
    
    /*
     * this calls the module management object and  grabs  all the permissions
     * into one veriable
     */
    public function gather_permissions(){
        

        /*
         * require module management object to collect the permissions
         */
        $module_management =  _ABSOLUTE_ROOT . "/moduleManagement.php";

        /*
         * if the file exists include it
         */
        if(file_exists($module_management)) {
            
            require_once $module_management;
        }
        
        /*
         * create the array to be populated later
         */
        $all_permissions = array();
        
        $baseModulePath = 'modules/base';

        $coreModulePath = 'modules/core';

        $extendedModulePath = 'modules/extended';



        $mod_managed = new moduleManagement($baseModulePath, $coreModulePath, $extendedModulePath, $this->connection );

        foreach ($mod_managed->get_permissions_array() as $module)  {

            if(!is_array($module)) {
                $method = $module . "_get_permissions";
                if(method_exists($module, $method)) {
                    $all_permissions[] = call_user_func_array(array($module, $method), array());
                }
            }
        } 

        return $all_permissions;
    }
    
    public function permissions_manage_validate($form_args) {
        
        /*
         * the validation variable defaults to false
         * 
         */
        
        $form_validates = false;

        /*
         * create the root path
         */
        $ABSOLUTE_ROOT = explode("/modules/base/permissions", __DIR__)[0];
        
        $path = array();
        
        $available_permissions = array();
        
        /*
         * the array key holds the path of each root
         */
        $path['base_modules_path']     = "$ABSOLUTE_ROOT/modules/base";
        $path['core_modules_path']     = "$ABSOLUTE_ROOT/modules/core";
        $path['extended_modules_path'] = "$ABSOLUTE_ROOT/modules/extended";
        
        /*
         * iterate throught the $path array to collect module names
         */
        foreach ($path as $p) {
            $modules[ ] = array_slice(scandir($p),2);
        }

        foreach ($modules as $key => $location) {
            foreach ($location as $mod) {
                if(file_exists($key . "/" . $mod . '/' . $mod .'.php')) {
                    
                    $filename = $key . "/" . $mod . '/' . $mod .'.php';
                    require_once $filename;
                }    
            }
        }
        
        
        
        /*
         * collect the modules permissions included
         */
        foreach ($modules as $array) {
            if(is_array($array)) {
                foreach ($array as $mod) {
                    if(method_exists($mod, $mod . "_get_permissions")) {
                        $available_permissions[] = call_user_func_array(array($mod, $mod . "_get_permissions"), array());
                    }    
                }    
            }
        }
        
        /*
         * check if it exists
         * 
         * it iterates through the list of available perms and makes sure it exists
         * if it never sees one then it stays false
         * 
         * that will only happen if someone manipulates the form
         * maliciously
         */

        $perm_count = 0;
        
        foreach ($form_args['permissions'] as $permissions => $v) {
            $current_permissions = key($v);
            foreach($available_permissions as $key => $comparable_perms) {
                if($current_permissions == $key) {
                  $perm_count++;   
                }
            }
        }    

        if(isset($form_args['permissions'])) {
            if($perm_count == sizeof($form_args['permissions'])) {
                $form_validates = true;
            }
            else {
                $form_validates = false;
            }
        }
        
        return $form_validates;
    }
    
    
    public function permissions_manage_submit($form_args) {
        
        $connection = $form_args['connection'];
        $session = $form_args['session_object'];

        $str = '';
        
        $query = "insert into role_permissions (role_id, permission) values (:role_id, :permission)";
 
        $connection->beginTransaction();

        $stmt = $connection->prepare('truncate table role_permissions');
        $stmt->execute();
        
       
        
        $stmt = $connection->prepare($query);
        
        foreach ($form_args['permissions'] as $role_id => $permissions_data) {
            
            if(is_array($permissions_data)) {
                foreach($permissions_data as $sub_permissions_data) {
                    if(is_array($sub_permissions_data)) {
                         foreach($sub_permissions_data as $permission) {
                             $stmt->bindParam(":role_id", $role_id);
                             $stmt->bindParam(':permission', $permission);
                             
                             $stmt->execute();
                         }     
                    }         
                }
            }    
        }
        $connection->commit();
        
    }
    

    /*
     * roles_manage is the page to adjust database roles 
     * you can edit or delete roles
     */
    public function roles_delete() {

        $stmt = $this->connection->prepare('select role_name from role_types');
        
        $stmt->execute();
        
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dynamic_roles = [];
        
        foreach($roles as $role) {
           $dynamic_roles[$role['role_name']] = ucfirst($role['role_name']);    
            
        }
        
        
        return $dynamic_roles;
    }
    
    /*
     * 
     * roles_manage_validate is the function to validate submit before
     * submission and querypage to adjust database roles 
     * to the database for adjustment in the future
     */
    public function roles_delete_validate($form_args) {
        return true;
    }
    
    /*
     * 
     *  roles_delete_submit is the page to adjust database roles 
     *  submits the query
     *  to the database for adjustment in the future
     */
    public function roles_delete_submit($form_args) {
       
        $connection = $form_args['connection'];

        $error = $form_args['error'];
        
        $session = $form_args['session_object'];
        try {
            foreach($form_args['role'] as $role) {
                $stmt = $connection->prepare('delete from role_types where role_name = :role_name ');
        
                $stmt->bindParam(":role_name", $role);
                $stmt->execute();
            }
        }
        catch(PDOException $e) {
            $error->add_error();
            
            $error->set_error($session_get_uid(), "The delete didn't work please check your request and try again");
            
        }
    }

    /*
     * roles_manage is the page to adjust database roles 
     * you can edit or delete roles
     */
    public function roles_add() {

        $stmt = $this->connection->prepare('select role_name from role_types');
        
        $stmt->execute();
        
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dynamic_roles = [];
        
        foreach($roles as $role) {
           $dynamic_roles[$role['role_name']] = ucfirst($role['role_name']);    
            
        }
        
        
        return $dynamic_roles;
    }
    
    /*
     * 
     * roles_manage_validate is the function to validate submit before
     * submission and querypage to adjust database roles 
     * to the database for adjustment in the future
     */
    public function roles_add_validate($form_args) {
        
        $form_validates = false;
        if(preg_match('/[^a-z0-9\s-]/i',$form_args['new_role'])) {
           $form_validates = false;
        }
        else {
           $form_validates = true;
        }
        
        return $form_validates;
    }
    
    /*
     * 
     * roles_manage_submit is the page to adjust database roles 
     * submits the query
     * to the database for adjustment in the future
     */
    public function roles_add_submit($form_args) {
       $connection = $form_args['connection']; 
        
       $stmt = $connection->prepare("insert into role_types (role_name) values (:role_name)");
       $stmt->bindParam(":role_name", $form_args['new_role']);
       

       $stmt->execute();
        
    }
    
    
    public function permissions_get_permissions() {
        $perms = array();
        
        $perms['manage_permissions'] = array(
            'label' => 'Manage permissions',
            'function' => 'permissions_manage'
        );
        
        return $perms;
        
    }
    
}
