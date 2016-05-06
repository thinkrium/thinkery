<?php

/*
 *  _ROOT_DIRECTORY holds the the name of  directory whether that be www/Thinkery
 *      or thinkrium.com/
 * 
 * _ABSOLUTE_ROOT holds the absolute path of the root directory from / -> name.com
 * 
 * _SITE_URL holds the name of the server either that be localhost
 *  or www.thinkrium.com
 * 
 */

define("_ROOT_DIRECTORY", explode('/index.php', $_SERVER['PHP_SELF'])[0] );

define("_ABSOLUTE_ROOT", explode('/core', __DIR__)[0] );

define("_SITE_URL", $_SERVER['SERVER_NAME']);

/* SET THe https global to true if https and false if http */

if(isset($_SERVER['HTTPS'])) {
 
    define("_HTTPS", 'https://');
}
 else {
    define("_HTTPS", 'http://');    
}

/*
 * 
 * set the session id for the application and 
 * send it through the database for comparing
 * 
 */

$session_id = session_id();

/*
 * used to hold the page rendering content
 */

$GLOBALS['page'] = '';