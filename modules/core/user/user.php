  <?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author thomedy
 */
class user {
    //put your code here
    private $databaseConnection;
    
    private $session;
    
    private $errors;
    
    public function __construct($connection, $session, $errors) {
        
        /*
         * carry the database connection over to the object
         * 
         */
        $this->databaseConnection = $connection;
        
        /*
         * carry the session over to the current object
         */
        $this->session = $session;
        
        /*
         * send in the errors and assign it to a private var
         * 
         */
        $this->errors = $errors;
    }
    
    public function userAddUrl() {
        
        $url = array();
        
        $url[] = array( 'user' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'user',
            'function' => 'user_show',
            'view' => 'user.view',
            )
        );

                
        $url[] = array( 'user/add' => array(
            'object' => __CLASS__,
            'region' => 'user_add',
            'location' => __DIR__,
            'function' => 'user_add',
            'view' => 'user_add.view',
            'validate' => 'user_add_validate',
            'submit'=> "user_add_submit",
            )
        );
  
                
        $url[] = array( 'user/login' => array(
            'object' => __CLASS__,
            'region' => 'loginForm',
            'location' => __DIR__,
            'function' => 'login_form',
            'view' => 'user_login.view',
            'validate' => 'user_login_validate',
            'submit'=> "user_login_submit",
            )
        );
        
        $url[] = array('user/logout' => array(
            'object' => __CLASS__,
            'region' => 'logout',
            'location' => __DIR__,
            'function' => 'user_logout',
            'view' => 'user_logout.view',
            )
        );
        
        
        
        $url[] = array( 'user/register' => array(
            'object' => __CLASS__,
            'region' => 'userRegisterForm',
            'location' => __DIR__,
            'function' => 'user_register',
            'view' => 'user_register.view',
            'submit'=> "user_register_submit",
            'validate' => 'user_register_validate',
            )
        );

        


        $url[] = array( 'user/!id' => array(
            'region' => 'user_id',
            'location' => __DIR__,
            'object' => __CLASS__,
            'function' => 'user_show',
            'view' => 'user.view',
            )    
        );

        $url[] = array( 'user/!id/edit' => array(
            'region' => 'user_id_edit',
            'location' => __DIR__,
            'object' => __CLASS__,
            'function' => 'user_id_edit',
            'validate' => 'user_id_edit_validate',
            'submit' => 'user_id_edit_submit',
            'view' => 'user_id_edit.view',
            )
        
        );

        $url[] = array( 'user/!id/role/edit' => array(
            'region' => 'user_id_edit_role',
            'location' => __DIR__,
            'object' => __CLASS__,
            'function' => 'user_id_role_edit',
            'validate' => 'user_id_role_edit_validate',
            'submit' => 'user_id_role_edit_submit',
            'view' => 'user_id_role_edit.view',
            )
        );

         
        return $url;
        
    }

    /*
     * experiment with nomenclature and convention
     * 
     */
    
    public function login_form() {
        
        $login = array();
        
        $login['form_name'] = array(
            'title' => "User Name",
            'type' => 'textfield',
        );
        
        return $login;
    }
    
    /*
     * this function is to log out of the session and also of the db
     * 
     */
    public function user_logout() {

        $this->session->set_uid(0);
        header("Location:" . _HTTPS . _SITE_URL . _ROOT_DIRECTORY);
    }
    
    
    public function test_function_4(){
        return array(
            'name' => 'someone else',
            'addy' => 'now'
            
        );
        
    }
    
    /*
     * The appropriate function named in the url array is sent an argument 
     * by necessity in the formManagement.php
     */
    
    public function login_form_submit($array) {

        /*
         * pass in the connection from the form to mke the data
         * into the db;
         * 
         */     
        $conn = $array['connection'];

    
       
        // prepare sql and bind parameters
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email) 
        VALUES (:firstname, :lastname, :email)");
    
        /*
         * hash the password from the login
         */
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);

    
    
        $firstname = $array['name'];
        $lastname = $array['email'];
        $email = $array['pass'];
        

        $stmt->execute();
        
        
        
    }
    
    public function testID() {}
    
    public function user_register_validate($user) {

        /*
         * set the variables for redirect just incase
         * TODO: make this work so that called functions work
         * without havin to set this every time
         * 
         * this will work for now as a minimal viable product
         * 
         */
        
        $error = $user['error'];
        
        $_ROOT_DIRECTORY = explode('/modules/base/forms/formManagement.php', $_SERVER['PHP_SELF'])[0];
        $_ABSOLUTE_ROOT = explode('/core', __DIR__)[0];
        $_SITE_URL = $_SERVER['SERVER_NAME'];

        /* SET THe https global to true if https and false if http */

        if(isset($_SERVER['HTTPS'])) {
            $_HTTPS ='https://';    
        }
        else {
            $_HTTPS ='http://';    
        }
        
        /*
         * 
         * set the variables to an easier to understand name
         * then hash the password
         * 
         * if the passwords are the same then hash it and send it
         * 
         * else redirect to user/register
         * 
         */

        $user_name = $user['name'];
        $user_email = $user['email'];
        $pass = $user['pass'];
        $pass_confirmed = $user['pass_confirmed'];

        $session_id = $user['session_id'];
        
        $connection = $user['connection'];

        $session = $user['session_object'];

        $author = $session->get_uid(); //temporary set to session user
        

        if($pass == $pass_confirmed) {
            
            /*
             * 
             * name_exists is a variable that will be null if it is empty and if
             * empty then you can call the 
             * 
             */
            $name_exists = call_user_func_array(array('user', 'is_name_available'), array($connection, $user_name));

            /*
             * email_exists holds the email test. if the email fails a seperate message will run and it will also res
             * reset the form
             */
            
            $email_exists =  call_user_func_array(array('user', 'is_email_available'), array($connection, $user_email));

            /*
             * email valid checks if the email put in is a valid email and will not call the 
             * submit function unless it is 
             */

            /*
             * if any of the validation steps does not pass then it sets an error and 
             * increases the error count and sets teh error by logging the message in the database
             */
            $email_is_valid = call_user_func_array(array('user', 'is_email_valid'), array($user_email));

        
            /*
             * is password valid checks for proper strength having one capital letter, one lowercase
             * letter and one number
             * 
             */
        
            $password_is_valid = call_user_func_array(array('user', 'is_password_valid'), array($pass));

 
            
            if($name_exists != NULL) {
                $error->add_error();
                $error->set_error($author, "still need an error system but the name is not available");
            }
            else if($email_exists != NULL) {
                $error->add_error();
                $error->set_error($author, "still need an error system but the email is not available");
            }
            else if(!$email_is_valid) {
                $error->add_error();
                $error->set_error($author, 'still need an error system but that is not a valid email');
            }

            else {
                
                /* 
                 * does nothing it is sent to the next phase of the conditionals
                 * and then it errors and returns false
                 * 
                 * 
                 */
            }    
        }
        else {
            $error->add_error();
            $error->set_error($author, "still need an error system but the passwords dont match");
        }
        
        if($error->errors_exist()) {
            
            return false;
        }
        else {
            return true;
        }
    }
    
    /*
     * looks for the name of the user in the database 
     * if the database if the name exists the ui will issue a warning and reset
     */
    public function is_name_available($connection, $name) {
       $stmt = $connection->prepare("select user_name from users where user_name = :name");
       
       $stmt->bindParam(":name",  $name);
       
       $stmt->execute();
 
       $db_user_name =   $stmt->fetch();
       
       return $db_user_name;
        
    }
    
    /*
     * looks for the name of the user in the database 
     * if the database if the name exists the ui will issue a warning and reset
     */
    public function is_email_available($connection, $user_email) {
       $stmt = $connection->prepare("select email from users where email = :email");
       
       $stmt->bindParam(":email",  $user_email);
       
       $stmt->execute();
 
       $db_user_email =   $stmt->fetch();
       
       return $db_user_email;
        
    }
    
    /*
     * checks email for valid email if it is true it is email
     * if it is false it returns false
     * 
     * the validate function checks false
     * 
     */
    
    public function is_email_valid($email) {

        return filter_var($email, FILTER_VALIDATE_EMAIL);
     
    }
    
    /*
     * checks to make sure that the password has a capital and lowercase and a number
     */
    
    public function is_password_valid($password) {
        
        $password_length_min = 7;
             
        $password_length_max = 256;

        if  (
                preg_match('/[A-Z][a-z][0-9]/', $password) &&
                strlen($password) > $password_length_min  &&
                strlen($password) < $password_length_max
            ) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /*
     * form management calls
     */
    public function user_register_submit($user) {
        
        /*
         * sets the user name to name
         * useremail to email
         * user pass to pass
         * 
         */
        
        /*
         * connection to user connection
         * 
         */
        
        $name = $user['name'];
        $email = $user['email'];
        $pass = $user['pass'];
        $pass_confirmed = $user['pass_confirmed'];
        
        $session = $user['session_object'];


        $session_id = $user['session_id'];
        
        $connection = $user['connection'];
        
        $author = $session->get_uid(); //temporary set to session user
        

        
        $pass_hashed = password_hash($pass, PASSWORD_BCRYPT);
           
        $stmt = $connection->prepare("insert into users (user_name, password, session_id, email)"
                                           . " values (:user_name, :password, :session_id, :email)");
            
        $stmt->bindParam(":user_name", $name);
        $stmt->bindParam(":password", $pass_hashed);
        $stmt->bindParam(":session_id", $session_id);
        $stmt->bindParam(":email", $email);
           
        $stmt->execute();
 
        /*
         * do not set the uid in user register create a double validation to protect users
         */
    }

    /*
     * test to see if the login form validates and if it does return true
     * which will mean that the submit function is called
     * 
     */
    public function user_login_validate($user) {

        $name = $user['name'];
        
        $session = $user['session_object'];
        
        $password = $user['pass'];
        $error = $user['error'];
        $connection = $user['connection'];

        $uid = $session->get_uid();
        
        $form_valid = true;
        
        
            /*
             * 
             * name_exists is a variable that will be null if it is empty and if
             * empty then you can call the 
             * 
             */
            $name_exists = call_user_func_array(array('user', 'is_name_available'), array($connection, $name));

       
            
            if($name_exists == NULL ) {
                $form_valid = FALSE;
                $error->add_error();
                $error->set_error($uid, "Im sorry either the user name or the password doesn't match.");
            }
            else if($name_exists != NULL) {
                
                $stmt = $connection->prepare('select * from users where user_name=:name');
                $stmt->bindParam(":name", $name);
                $stmt->execute();
                $db_user = $stmt->fetch();

                if(password_verify($password, $db_user['password'])) {
                    $form_valid = TRUE;     
                }else {
                    $form_valid = FALSE;
                
                    $error->add_error();                
                    $error->set_error($uid, "Im sorry either the user name or the password doesn't match.");
                }    
 
     
            }        
        
        
        return $form_valid;
        
    }
     
    /*
     * user_login_submit
     * 
     * info is the information sent to the function by
     * the formManagement page
     */
    public function user_login_submit($info) {
        
                
        $_ROOT_DIRECTORY = explode('/modules/base/forms/formManagement.php', $_SERVER['PHP_SELF'])[0];
        $_ABSOLUTE_ROOT = explode('/core', __DIR__)[0];
        $_SITE_URL = $_SERVER['SERVER_NAME'];

        /* SET THe https global to true if https and false if http */

        if(isset($_SERVER['HTTPS'])) {
            $_HTTPS ='https://';    
        }
        else {
            $_HTTPS ='http://';    
        }
        

        
        
        /*
         * name is passed for query's sake
         * 
         */
        $name = $info['name'];

        /*
         * the session object is passed in for use in creating uid
         */
        $session = $info['session_object'];

        /*
         * the connectoin to the db is passed to set the uid
         * 
         */
        $connection = $info['connection'];

        
        $stmt = $connection->prepare('select * from users where user_name=:name');
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        $db_user = $stmt->fetch();

        $uid = $db_user['uid'];
        
        $session->set_uid($uid);
    }
    

    /*
     * show the user that is either logged in or is in the arguments
     * display all the information about the user 
     * 
     */
    public function user_show($id = null) {

        

        $uid = '';
        
        if($id == NULL) {

            $uid = $this->session->get_uid();
        }
        else {
            $uid = (int)$id['!id'];
        }

        $query = 'select users.uid, users.user_name, users.email, users_profile.about from users '
               . 'left join users_profile on users.uid = users_profile.uid'
               . ' where users.uid = :uid';

        $stmt = $this->databaseConnection->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results;
    }

    public function test() {
    
    }

    public function user_register() {
        
    }
    
    /*
     * maybe  not necessary have to check
     */
    public function display_user() {
        
    }

    

    public function user_id_edit($params) {
 

        
        $query = "select users.uid as uid, users.email as email, users.user_name as user_name, "
                . "users_profile.first_name as first_name, "
                . "users_profile.last_name as last_name, users_profile.about as about "
                . "from users, users_profile where users.uid = :uid or users_profile.uid = :uid";
        
        $uid = $params['!id'];
        $stmt = $this->databaseConnection->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();

        $user = $stmt->fetch();

        if($user) {
            return $user;
        }
        else {
            return null;
        }
    }
    

    
    /*
     * this function is called on the user profile edit form before it 
     * goes to the submit function
     */
    public function user_id_edit_validate($user) {
        
        $form_content = array();

        
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $email = $user['email'];
        $about_me = $user['about'];
        
        $session = $user['session_object'];
        
        $error = $user['error'];
        $connection = $user['connection'];

        $uid = $user['!id'];
        
        $form_validates  = true;


        /*
         * email valid checks if the email put in is a valid email and will not call the 
         * submit function unless it is 
         */

        /*
         * if any of the validation steps does not pass then it sets an error and 
         * increases the error count and sets teh error by logging the message in the database
         */
       
        $email_is_valid = call_user_func_array(array('user', 'is_email_valid'), array($email));

        
        if(!$email_is_valid) {
            
            $error->add_error();
            $error->set_error($uid, "Sorry the email is not formatted correctly!");

            $form_validates =  false;
            
        }        
     
        return $form_validates;
    }

    /*
     * this the functionality of the user profile edit page
     * 
     * it uses transaction pdo to make it more efficient by not
     * commiting the sql query until both statements are recorded
     * 
     * it then only works if it all plays out then if not it goes to the 
     * catch block where it commits an error message under uid -1 which is system
     * 
     */
    public function user_id_edit_submit($user) {
        
        
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $email = $user['email'];
        $about_me = $user['about'];
        
        $session = $user['session_object'];
        
        $error = $user['error'];
        $connection = $user['connection'];

        $uid = $user['!id'];
        

        try {
            
            $query = 'select uid from users_profile where uid=:uid';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            
            $results = $stmt->fetch();
            
            if(!$results) {   
                $connection->beginTransaction();
                $stmt = $connection->prepare(                
                    'update users set email = :email where uid=:uid');

                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':email', $email);
          
                $stmt->execute();
        
                $stmt = $connection->prepare(                
                 ' insert into users_profile '
                . '(last_name, first_name, about, uid) values(:last_name, :first_name, '
                . ':about, :uid);'
                );
                
                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':about', $about_me);
    
                $stmt->execute();        
            
    
                $connection->commit();
                
            }
            else {
                $connection->beginTransaction();
                $stmt = $connection->prepare(                
                    'update users set email = :email where uid=:uid');

                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':email', $email);
          
                $stmt->execute();
        
                $stmt = $connection->prepare(                
                 'update users_profile '
                . 'set last_name=:last_name, first_name=:first_name, '
                . 'about=:about where uid=:uid;'
                );
                
                $stmt->bindParam(':uid', $uid);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':about', $about_me);
    
                $stmt->execute();        
            
    
                $connection->commit();
            }    
        }
        catch (PDOException $e ) {
            
            $connection->rollBack();
            $error->add_error();
            
            $error->set_error(-1, "the database transaction errored out some how");
        }    
    }
    
    /*
     *   the user_id_role_edit takes in the user id and passes it as an argument
     *   it checks if it matches the logged in user or if ithe user is 1
     * 
     *   then it creates the page that is used to assign a role to a user 
     * 
     */
    public function user_id_role_edit($params) {

        
        $uid = $params["!id"];

        if($uid == $this->session->get_uid() ||
           $this->session->get_uid() == 1) {     

        
            $stmt = $this->databaseConnection->prepare('select * from role_types');
            $stmt->execute();
         
            
            $existing_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            

            $stmt = $this->databaseConnection->prepare('select role_name from user_roles where uid = :uid');
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
         
            $dynamic_user_roles = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            
            $return = array(
                'existing_roles' => $existing_roles,
                'uid' => $params['!id'],
                'dynamic_roles' => $dynamic_user_roles
            );
            
            return $return;
            
        }
        else {
            $this->errors->add_error();
          
            $this->errors->set_error($session->get_uid(), "Im sorry you cannot make this edit");
        }    
    }
    
    /*
     * checks to see if the roles that are being set for the user exist in the list
     * if they exist then you can validate 
     * 
     */
    public function user_id_role_edit_validate($form_args) {
        
        /*
         * check to see that the code hasn't been tampered with by making sure the selection exists inside the 
         * current role types
         * 
         */
        
        $form_validates = true;
        
        $connection = $form_args['connection'];
        
        $session = $form_args['session_object'];
        
        $error = $form_args['error'];
         
        $stmt = $connection->prepare('select role_name from role_types');
        
        $stmt->execute();
        
        $existing_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existing_role_names = array_column($existing_roles, 'role_name');
        
        if(isset($form_args['role'])) {
      
            foreach ($form_args['role'] as $key => $new_role_data) {

                $new_role = key($new_role_data);
                
                if(!in_array($new_role, $existing_role_names)) {
                   $form_validates = FALSE;  
                   break;
                }                
            }    
        }
        else {
            $form_validates = true;
        }

        return $form_validates; 
    }

    
    /*
     * user_id_role_edit_submit  is the function that submits the roles to the database
     * if there are pre existing roles then delete them and start over if there are 
     * none then just a
     * 
     */
    public function user_id_role_edit_submit($form_args) {

        $uid = $form_args['!id'];

        $connection = $form_args['connection'];
                
        $session = $form_args['session_object'];
        
        $error = $form_args['error'];
        
        
        $roles = '';

        $stmt = $connection->prepare("select count(*) from user_roles where uid = :uid");
        
        
        $stmt->bindParam(":uid", $uid);
        
        $stmt->execute();

        $role_test = $stmt->fetch(PDO::FETCH_COLUMN);

        if((int)$role_test > 0) {
            
            $stmt = $connection->prepare('delete from user_roles where uid = :uid');
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
           
        }

        if(isset($form_args['role'])) {

            $roles = $form_args['role'];

            $prepared_roles = array();
            
            try {
                
                $connection->beginTransaction();

                $query = "insert into user_roles (uid, role_id, role_name) values (:uid, :role_id, :role_name)";

                $stmt = $connection->prepare($query);

                foreach ($roles as $role_id => $role_data) {
                    
                    $role = key($role_data);
                    

                    $stmt->bindParam(':role_id', $role_id);
                    $stmt->bindParam(':uid', $uid);
                    $stmt->bindParam(':role_name', $role);
                    
                    $stmt->execute();

                }

                $connection->commit();
            }
            catch(PDOException $e) {

                $connection->rollBack();
                $error->add_error();
            
                $error->set_error(-1, "the database transaction errored out some how");
            } 
        }
    }
    
    public function user_add() {
        
    }
    
    public function user_add_validate() {
        
    }
    
    public function user_add_submit() {
        
    }
    
    public function user_get_permissions() {
        $permissions = array();
        
        $permissions['show_user'] = array(
            'label' => 'view your own page',
            'function' => 'show_user'
        );
        
        $permissions['user_id_edit_key'] = array(
            'label' => 'edit the users account',
            'function' => 'user_id_edit'
            
        );
        
        
        return $permissions;
        
    }
}

