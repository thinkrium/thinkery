<?php 

session_start();
    
/*
 * create the root dir so that you can send the database connection to the 
 * proper submit functions
 * 
 */

 /*
  * root dir that holds the root directory
  */
$root_dir = explode('/core', __DIR__)[0];

 /*
  * include the databaseconfig
  */

require_once "$root_dir/core/databaseConfig.php";

$db = new databaseConfig;

$connection = $db->connection();

 /*
  * include the filemanagement object for manipulation in file uploads
  */


require_once "$root_dir/core/fileManagement.php";

$fileManagement = new fileManagement();


/*
 *
 * holds the white list for all file extensions
 */

require_once "$root_dir/core/mediaRequirements.php";

$mediaRequirements = new mediaRequirements();

                                
/*
 * INCLUDE THE error class so that you can log errors on form management
 */
require_once "$root_dir/modules/base/error/errors.php";

$error = new errors($connection);

/*
 * include the mail object so that you can send an error if necessarry
 */
require_once "$root_dir/modules/base/email/email.php";
$email_object = new email();



/*
 * include the session object so that you can send an error if necessarry
 */
require_once "$root_dir/core/session.php";
$session_object = new session();

    /*
     * session holds the uid and sets the uid to the value if it exists
     */
    /*
     * 0 as a uid = no logged in user;
     */
    


    $uid = $session_object->get_uid();




/*
 * the formManagement page checks for the values coming out of the form
 * and then determines where the form came from and calls its submit function
 * by searching the url array for the proper submit function
 * 
 * it then calls the submit function and sends in the arguments from the form if 
 * necessary
 * 
 */

/*
 * create a list of variables to allow existence outside of the conditionals
 * which will be instantiated based on a series of if statements
 * 
 */

$rootDirectory = explode('core/formManagement.php', $_SERVER['PHP_SELF'])[0];

$objectName = $coreExtended = $path = $formArgs =  "";


/*
 * site url is set to server name whether that is localhost or www.thinkrium.com
 */

$siteUrl = $_SERVER['SERVER_NAME'];

/*
 * form name is a variable that holds the referening request for form
 * it comes in from the variable and the '_' is replaced with '/' for
 * the refering path or where it came from basically
 * 
 */

$formName = $_POST['form_name'];





/* SET THe https global to true if https and false if http */

if(isset($_SERVER['HTTPS'])) {
 
   $https = 'https://';
}
 else {
   $https = 'http://';
}

/*
 * fullUrl is the combination of http the site url which is the server 
 * and the root directory if not the same as the server
 * 
 */
$fullUrl = $https . $siteUrl . $rootDirectory;


/*
 * objectName is the class that created the form view 
 */

if(isset($_POST['object_name'])) {
    $objectName = $_POST['object_name'];
}


/*
 * coreExtended requests the status of the module that calls the form management
 * then it determines the location 
 */

if(isset($_POST['core_extended'])) {
    $coreExtended = $_POST['core_extended'];
}

/*
 * if base then the path goes to the base module and core to core and extended to extended
 */

if($coreExtended == 'base') {
   $path = 'modules/base';    
}
else if($coreExtended == "core") {
   $path = 'modules/core';        
}
else if($coreExtended == "extended") {
       $path = 'modules/extended';    
}
else if($coreExtended == "content_types") {
       $path = 'content_types';    
       
}
else {}

/*
 * include the information that comes from the form keys
 */

$include_path = $path . "/" . $objectName . '/' . $objectName . '.php';

require_once $root_dir . '/' . $include_path;


    /*
     * call the url from addurl procedurally
     */

    $properUrl = call_user_func_array(array($objectName, $objectName."AddUrl"), array());

foreach ($properUrl as $url) {    

    /*
     * the validate function is set from the url array
     * 
     */
    

    if(isset($url[$formName])) {

        $validateFunction = $url[$formName]['validate'];

   
        /*
         * the submit function is set from the url array in the addURl api
         */

        $submitFunction = $url[$formName]['submit'];


    }

    $formArgs = $_POST;
    

    /*
     * 
     * formargs holds the post arg from the form
     */

    /*
     * create the arguments for redirect in tokenized form or straigth through
     */

    if(isset($formArgs['redirect'])) {
        $url_components = explode('/', $formArgs['redirect']);
    }    


    /*  
     * break up the url just in case there is a tokenized url
     * 
     * 
     */

    /*
     * put together a tokenized url string
     */

    $tokenized_url = [];

    foreach ($url_components as $arg) {
        if(preg_match('/!/', $arg)) {
            $tokenized_url[] = $formArgs[$arg];
        } 
        else {
            $tokenized_url[] = $arg;
        }   
    }

    if(sizeof($tokenized_url > 0)) {

        $redirect_url = join('/', $tokenized_url);
    }
    
    else { 
        if(isset($formArgs['redirect'])) {
 
            $redirect_url = $formArgs['redirect'];

        }    
    }
}

$formArgs['session_id'] = session_id();
$formArgs['connection'] = $connection;
$formArgs['error'] = $error;
$formArgs['email_object'] = $email_object; 
$formArgs['session_object'] = $session_object; 
$formArgs['file_object'] = $fileManagement;
$formArgs['media_requirements'] = $mediaRequirements;

/*
 * the form only process anything if the key matches. if not the 
 * form goes no where
 * 
 */

if( $formArgs['form_key'] == $_SESSION['form_key'] ) {

    /*
     *if the validate function passes then call  submit function
     * else throw error and return 
     */


    $form_validates =  call_user_func_array(array($objectName, $validateFunction ), array($formArgs));

    if($form_validates) {

        call_user_func_array(array($objectName, $submitFunction), array($formArgs));

        if(isset($formArgs['redirect'])) {

            
            header("HTTP/1.1 303 See Other");
            header("Location:$fullUrl" . $redirect_url);    
        }
    }
    else {
        if(isset($formArgs['redirect'])) {

            header("Location:$fullUrl" . $redirect_url);    
        }
    }
}

else {
    
    /*
     * if it fails it sets the error log
     */
    $error->set_error($uid, "The form key does not match; possible attack!");
                
}