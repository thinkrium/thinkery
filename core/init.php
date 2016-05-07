<?php

/*
 * one of the session variable  is an error count and as errors increase for user
 * 
 */


//ob_start();

session_start();

/*
 * include the bootstrap file for all page loading needs
 */

require_once "bootstrap.php";


/*
 * check if there is an existent error count already
 * if not then set the error count. and increment as necessary
 * if the error count is 1 or more then show errors on page
 * 
 */

/*
 * set the session to hold a user profile
 * user is the variable on the session and it is set to an array so that it
 * can hold things like a uid. 
 * 
 * the uid is set on login
 * 
 */

$error->create_errors();


$session->create_user_profile();

if(!$session->user_id_exists()) {
    
    $session->set_uid(0);

}
else {
}

/*
 * set up an error system for anonymous users
 * that has to stay persistent across page loads in the
 * case of a form redirect or any redirect
 * 
 */

/*
 * 
 * include the nonce class for form key generation to be able to create a nonce key
 * for every form
 * 
 */

require_once 'nonce.php';

$formKey = new nonce();

/*
 * include module management.php
 * and send in the object into the thinkery constructor
 * 
 * we do this so that if we change module management we can do less modification in the
 * future of code and the parameter stays the same
 */

require_once 'moduleManagement.php';

/*
 * create the module management object and send it in as an argument to the 
 * thinkery object
 */

/*
 * the parameter it takes is the path in case we move the path around there is 
 * less to change
 */

/*
 * base, core, and extended module path is available to send in to the constructor
 * just incase it changes as we go
 * 
 */

$baseModulePath = 'modules/base';

$coreModulePath = 'modules/core';
   
$extendedModulePath = 'modules/extended';
   

$moduleManagement = new moduleManagement($baseModulePath, $coreModulePath, $extendedModulePath, $dbConnection);

/*
 * create a region management object in order to manipulate the gui regions and order the display and render of the content
 * 
 */

require_once 'regionManagement.php';

$regionManagement = new regionManagement($dbConnection, $moduleManagement, $session, $error);



/*
 * include the permissions management object to filter, protect, secure roles and
 * authority
 */
require_once 'permissionManagement.php';

$permissionManagement = new permissionManagement($dbConnection, $moduleManagement, $session);


/*
 * include file management in order to manipulate and secure files 
 */

require_once "fileManagement.php";

$fileManagement = new fileManagement();


/*
 * the mediaRequirement object that is basically the whitelist for file types 
 * 
 */

require_once 'mediaRequirements.php';

$mediaRequirements = new mediaRequirements();

/*
 * include the controller.php and that is the app.
 * everything is run through thinkery
 * 
 */

/*
 * build the render object right here 
 * this file includes the render system and is called in the controller
 */

require_once 'render.php';

$renderObject = new render();

require_once 'Controller.php';

/*
 * create a thinkery object and send in the database connection to set each module and class with the proper connections
 * 
 * parameters include the database connection, the module management to accrue the 
 * url keys and the functions that go into it
 */

$controller = new Controller( $dbConnection,
                              $moduleManagement,
                              $regionManagement,
                              $session,
                              $permissionManagement, 
                              $error, 
                              $fileManagement,
                              $mediaRequirements,
                              $renderObject
                    );


/*
 * check for the number of errors in the page load or processing and display them 
 * in html.view
 */

if($error->errors_exist() && $session->get_uid() > 0) {
    
    $stmt = $dbConnection->prepare('select eid, message from error_log where '
            . 'aid = :aid order by eid desc limit :count');
    
    $current_error_count = $error->get_error_count();
    $uid = 1;

    $stmt->bindParam(":aid", $uid);
    $stmt->bindParam(':count', $current_error_count, PDO::PARAM_INT);
    $stmt->execute();
    $logged_errors = $stmt->fetchAll();
       


    require_once _ABSOLUTE_ROOT . "/modules/base/error/display_error.view";

    $GLOBALS['page']['display_errors_messages'] = ob_get_contents();

    $error->clear_errors();    

}
else if($error->errors_exist() && $session->get_uid() == 0) {
    
    $logged_errors = $_SESSION['anonymous_user_errors'];
    require_once _ABSOLUTE_ROOT . "/modules/base/error/display_error.view";

    $GLOBALS['page']['display_errors_messages'] = ob_get_contents();

    $error->clear_errors();    
}



/*
 * 
 * all the output gets funneled  to the html.view.php
 * 
 */

ob_end_clean();
   
require_once _ABSOLUTE_ROOT . '/html.view';

