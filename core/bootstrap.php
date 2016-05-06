<?php

/*
 * instantiate the globals
 * 
 */

/*
 * _SITE_URL = the current website name
 * 
 * _ROOT_DIRECTORY = the root directory
 *
 * _ABSOLUTE_ROOT = the absolute path to the root directory for file traversal
 */

require_once 'globals.php';


/*
 * includes the db configuration which creates a database object and then returns the connection to be used
 * in the rest of the objects 
 */

require_once "databaseConfig.php";

/*
 * create the database object and return the connection to be used in any base classes
 */

$db = new databaseConfig();

/*
 * return the connection for the database configuration object
 */

$dbConnection = $db->connection();

/*
 * including the error php to log errors when necessary
 *  
 */

include_once _ABSOLUTE_ROOT . "/modules/base/error/errors.php";

$error = new errors($dbConnection);


/*
 * include the session object 
 */
require_once _ABSOLUTE_ROOT . "/core/session.php";

$session = new session();



/*
 * using dbConnection confirm that there is an existing database in place
 * if not then you must create the database and if it is then use the existing
 * database
 * 
 */

$stmt = $dbConnection->query('show tables');

$results = $stmt->fetchAll();

if( sizeof($results) == 0 ) {
    
    $session->end_session();
    
    require_once 'install.php';
    
    $install = new install($dbConnection);
    
    /*
     * during the install phase the site redirects to the welcome page and will
     * only do that if you are installing the database
     * 
     */
    
    header("Location:" . _HTTPS . _SITE_URL . _ROOT_DIRECTORY . "/welcome");
    
}

